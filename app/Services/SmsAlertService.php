<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsAlertService
{
    public static function send($mobile, $message)
    {
        $response = Http::post(
            'https://www.smsalert.co.in/api/push.json',
            [
                'apikey'   => env('SMS_ALERT_API_KEY'),
                'sender'   => env('SMS_ALERT_SENDER'),
                'mobileno' => $mobile,
                'text'     => $message,
            ]
        );

        Log::info('SMS Response', [
            'mobile' => $mobile,
            'response' => $response->json()
        ]);

        return $response->json();
    }
}