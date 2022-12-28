<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function payReminder(Request $request) {
        $template_name = 'reminder_auto_insurrance';
        $user_name = $request->first_name . ' ' . $request->last_name;
        $insurance_number = $request->policy_number;
        $expiration_date = $request->expiration_date;
        //$recipient_number = "77773344869";
        $recipient_number = $request->phone_number;

        $parameters = [];
        $params1 = new \stdClass();
        $params1->type = 'text';
        $params1->text = $user_name;
        $params2 = new \stdClass();
        $params2->type = 'text';
        $params2->text = $insurance_number;
        $params3 = new \stdClass();
        $params3->type = 'text';
        $params3->text = $expiration_date;
        array_push($parameters, $params1, $params2, $params3);

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

        //return $body;

        $baseUrl = 'https://graph.facebook.com/';
        $url = $baseUrl . env('WA_VERSION') . '/' . env('WA_PHONE_NUMBER_ID') . '/messages';

        return Http::withToken(env('WA_USER_ACCESS_TOKEN'))
            ->withBody(json_encode($body), 'application/json')
            ->post($url);
    }
}

