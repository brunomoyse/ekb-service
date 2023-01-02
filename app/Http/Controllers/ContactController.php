<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return Contact::orderBy('contract_end_date', 'asc')->get();
    }

    public function create(Request $request)
    {
        $contact = new Contact();
        $contact->phone_number = $request->phone_number;
        $contact->policy_holder = $request->policy_holder;
        $contact->contract_start_date = $request->contract_start_date;
        $contact->contract_end_date = $request->contract_end_date;
        $contact->registration_number = $request->registration_number;
        $contact->save();
        return $contact->id;
    }

    public function update (Request $request, $id)
    {
        $contact = Contact::find($id);
        $contact->phone_number = $request->phone_number;
        $contact->policy_holder = $request->policy_holder;
        $contact->contract_start_date = $request->contract_start_date;
        $contact->contract_end_date = $request->contract_end_date;
        $contact->registration_number = $request->registration_number;
        $contact->save();
        return $contact;
    }

    public function delete (Request $request)
    {
        $contact = Contact::find($request->id);
        $contact->delete();
        return $request->id;
    }
}
