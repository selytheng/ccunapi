<?php

namespace App\Http\Controllers;

use App\Models\PartnerContact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PartnerContactController extends Controller
{
    public function getContactByPartnerId($partnerId)
    {
        $contact = PartnerContact::where('partner_id', $partnerId)->first();

        if (!$contact) {
            return response()->json([
                'message' => 'Contact not found for this partner.'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($contact, Response::HTTP_OK);
    }


    public function createContact($partnerId)
    {
        $contact = PartnerContact::create(['partner_id' => $partnerId]);
        return response()->json($contact, Response::HTTP_CREATED);
    }

    public function updateContact(Request $request, $id)
    {
        $contact = PartnerContact::findOrFail($id);
        $contact->update($request->only([
            'phone_number',
            'email',
            'location_link',
            'address',
            'website',
            'moodle_link',
        ]));

        return response()->json($contact, Response::HTTP_OK);
    }
}
