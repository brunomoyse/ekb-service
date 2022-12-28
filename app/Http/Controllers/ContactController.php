<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return Contact::orderBy('expiration_date', 'desc')->get();
    }

    public function create(Request $request)
    {
        $contact = new Contact();
        //$contact->first_name = $request->first_name;
        //$contact->last_name = $request->last_name;
        //$contact->policy_number = $request->policy_number;
        $contact->phone_number = $this->convertPhoneNumberToSQL($request->phone_number);
        $contact->expiration_date = $this->convertDateToSQL($request->expiration_date);
        $contact->save();
        return $contact;
    }

    public function update (Request $request, $id)
    {
        $contact = Contact::find($id);
        //$contact->first_name = $request->first_name;
        //$contact->last_name = $request->last_name;
        //$contact->policy_number = $request->policy_number;
        $contact->phone_number = $this->convertPhoneNumberToSQL($request->phone_number);
        $contact->expiration_date = $this->convertDateToSQL($request->expiration_date);
        $contact->save();
        return $contact;
    }

    public function delete (Request $request)
    {
        $contact = Contact::find($request->id);
        $contact->delete();
        return $request->id;
    }

    private function convertDateToSQL($dateString) {
        $parts = explode("/", $dateString);
        $day = $parts[0];
        $month = $parts[1];
        $year = $parts[2];
        if (checkdate($month, $day, $year)) return "$year-$month-$day";
        else return null;
    }

    private function convertPhoneNumberToSQL($phoneNumber) {
        $phoneNumber = str_replace(' ', '', $phoneNumber);
        if (strlen($phoneNumber) !== 11) return $phoneNumber;
        $phoneNumber = '7' . substr($phoneNumber, 1);
        return $phoneNumber;
    }
}
