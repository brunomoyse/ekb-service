<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function payReminder($contactId) {
        $contact = Contact::find($contactId);
        $template_name = 'auto_reminder';
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

        array_push($parameters, $params3);

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
        $test = Http::withToken(env('WA_USER_ACCESS_TOKEN'))
            ->withBody(json_encode($body), 'application/json')
            ->post($url);
        var_dump($test);
        $contact->last_sent_at = new DateTime();
        $contact->save();

        return $contact;
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

