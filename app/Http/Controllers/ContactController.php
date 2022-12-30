<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return Contact::orderBy('expiration_date', 'asc')->get();
    }

    public function create(Request $request)
    {
        $contact = new Contact();
        //$contact->first_name = $request->first_name;
        //$contact->last_name = $request->last_name;
        //$contact->policy_number = $request->policy_number;
        $contact->phone_number = $request->phone_number;
        $contact->expiration_date = $request->expiration_date;
        $contact->save();
        return $contact;
    }

    public function update (Request $request, $id)
    {
        $contact = Contact::find($id);
        //$contact->first_name = $request->first_name;
        //$contact->last_name = $request->last_name;
        //$contact->policy_number = $request->policy_number;
        $contact->phone_number = $request->phone_number;
        $contact->expiration_date = $request->expiration_date;
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
