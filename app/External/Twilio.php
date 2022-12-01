<?php

namespace App\External;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class Twilio
{
    public function __construct()
    {
        $this->sid = config('services.twilio.sid');
        $this->token = config('services.twilio.token');
        $this->number = config('services.twilio.number');
    }

    public function sendSms(string $phone, string $message)
    {
        try {
            $client = new Client($this->sid, $this->token);

            $client->messages->create($phone, [
                    'from' => $this->number,
                    'body' => $message,
                ]
            );
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
        }
    }
}
