<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;

class ContactController extends Controller
{
    public function contactDetails()
    {
        $contact = ContactSetting::first();

        return response()->json([
            'status' => true,
            'data' => [
                'address' => $contact->contact_address ?? '',
                'phone' => $contact->contact_phone ?? '',
                'email' => $contact->contact_email ?? '',

                'business_days' => $contact->business_days ?? '',
                'business_hours' => $contact->business_hours ?? '',

                'support_message' => $contact->support_message ?? '',
                'support_timings' => $contact->support_timings ?? '',
            ]
        ]);
    }
}