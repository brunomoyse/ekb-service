<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function payReminder($contactId) {
        $contact = Contact::find($contactId);
        $template_name = 'ins_reminder';
        // $template_name = 'reminder_auto_insurrance';
        // $user_name = $contact->policy_holder ?: '';
        // $insurance_number = $contact->registration_number;
        $contract_end_date = $this->formatDateForKazakhstan($contact->contract_end_date);
        $recipient_number = $contact->phone_number;

        $parameters = [];
        // $params1 = new \stdClass();
        // $params1->type = 'text';
        // $params1->text = $user_name;
        //$params2 = new \stdClass();
        //$params2->type = 'text';
        //$params2->text = $insurance_number;
        $params3 = new \stdClass();
        $params3->type = 'text';
        $params3->text = $contract_end_date;
        $params4 = new \stdClass();
        $params4->type = 'text';
        $params4->text = $contract_end_date;

        array_push($parameters, $params3, $params4);

        $components = [];
        $component = new \stdClass();
        $component->type = 'body';
        $component->parameters = $parameters;
        $components[] = $component;

        $language = new \stdClass();
        $language->policy = 'deterministic';
        $language->code = 'ru';

        $template = new \stdClass();
        $template->name = $template_name;
        $template->language = $language; //kazakh === 'kk'
        $template->components = $components;

        $body = new \stdClass();
        $body->messaging_product = 'whatsapp';
        $body->to = $recipient_number;
        $body->type = 'template';
        $body->template = $template;

        $baseUrl = 'https://graph.facebook.com/';
        $url = $baseUrl . env('WA_VERSION') . '/' . env('WA_PHONE_NUMBER_ID') . '/messages';
        $res = Http::withToken(env('WA_USER_ACCESS_TOKEN'))
            ->withBody(json_encode($body), 'application/json')
            ->post($url);

        if ($res->status() === 200) {
            if ($res['messages'][0]['id']) {
                $contact->last_message_id = $res['messages'][0]['id'];
                $contact->last_message_status = 'sent';
            }
        } else $contact->last_message_status = 'failed';
        $contact->last_sent_at = new DateTime();
        $contact->save();

        return $contact;
    }

    public function handleVerificationRequest(Request $request)
    {
        try {
            $verifyToken = env('WA_WEBHOOKS_TOKEN');
            $query = $request->query();
            $mode = $query['hub_mode'];
            $challenge = $query['hub_challenge'];
            $token = $query['hub_verify_token'];
            if ($mode && $verifyToken === $token) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }
            throw new Exception('Invalid request');
        } catch (Exception $e){
            return response($e->getMessage(), 500);
        }
    }

    public function processWebhook (Request $request) {
        try {
            $bodyContent = json_decode($request->getContent(), true);
            $value = $bodyContent['entry'][0]['changes'][0]['value'];

            // STATUS
            if (isset($value['statuses'][0])) {
                $statuses = $bodyContent['entry'][0]['changes'][0]['value']['statuses'][0];
                $updated_message_status = $statuses['status'];
                $updated_message_recipient_id = $statuses['recipient_id'];
                $updated_message_id = $statuses['id'];
                if (isset($statuses) && !empty($updated_message_status)) {
                    $contact = Contact::where('last_message_id', $updated_message_id)->first();
                    if ($contact) {
                        $contact->last_message_status = $updated_message_status;
                        $contact->save();
                    }
                }
            }

            // MESSAGES
            if (!empty($value['messages'])) {
                if ($value['messages'][0]['type'] == 'text') {
                    $id = $value['messages'][0]['id'];
                    $phone_number = $value['messages'][0]['from'];
                    $body = $value['messages'][0]['text']['body'];

                    $contact = Contact::where('phone_number', $phone_number)->first();
                    if (isset($contact->last_message_auto_replied_at)) {
                        $date = new DateTime();
                        $date->modify('-24 hours');
                        $formattedDate = $date->format('Y-m-d H:i:s');
                        if ($contact->last_message_auto_replied_at < $formattedDate) {
                            // if already sent > 24 hours ago
                            $this->sendAutoReply($phone_number);
                        }
                    } else {
                        $this->sendAutoReply($phone_number);
                    }
                    DB::table('messages')->insert([
                        'phone_number' => $phone_number,
                        'message_id' => $id,
                        'message_body' => $body,
                        'created_at' => now(),
                    ]);
                }
            }
        } catch (Exception $e){
            return response($e->getMessage(), 500);
        }
    }

    private function sendAutoReply($phone_number) {
        $template_name = 'auto_reply';
        $recipient_number = $phone_number;

        $parameters = [];

        $components = [];
        $component = new \stdClass();
        $component->type = 'body';
        $component->parameters = $parameters;
        $components[] = $component;

        $language = new \stdClass();
        $language->policy = 'deterministic';
        $language->code = 'ru';

        $template = new \stdClass();
        $template->name = $template_name;
        $template->language = $language;
        $template->components = $components;

        $body = new \stdClass();
        $body->messaging_product = 'whatsapp';
        $body->to = $recipient_number;
        $body->type = 'template';
        $body->template = $template;

        $baseUrl = 'https://graph.facebook.com/';
        $url = $baseUrl . env('WA_VERSION') . '/' . env('WA_PHONE_NUMBER_ID') . '/messages';
        Http::withToken(env('WA_USER_ACCESS_TOKEN'))
            ->withBody(json_encode($body), 'application/json')
            ->post($url);
        $contact = Contact::where('phone_number', $phone_number)->first();
        $contact->last_message_auto_replied_at = new DateTime();
    }

    private function formatDateForKazakhstan($dateString) {
        $date = new DateTime($dateString);

        $monthNames = array(
            "янв", "фев", "мар", "апр", "май", "июн",
            "июл", "авг", "сен", "окт", "ноя", "дек"
        );

        $monthIndex = $date->format('n') - 1;

        $monthName = $monthNames[$monthIndex];

        return $date->format('j') . " " . $monthName . " " . $date->format('Y');
    }

}

