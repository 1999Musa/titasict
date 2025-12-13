<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlphaSmsService
{
    public function send(string $to, string $message)
    {
        return Http::withoutVerifying()   
            ->asForm()
            ->post('https://api.sms.net.bd/sendsms', [
                'api_key' => config('services.alpha_sms.api_key'),
                'to'      => $to,
                'msg'     => $message,
            ]);
    }
}
