<?php

namespace App\Http\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MessageService
{
    public string $email = 'azamattaganovv@gmail.com';
    public string $password = 'bxeD5jsoZwgMZKl0eiKpFaqJn5YAki99vEGde66f';

    public function refreshToken()
    {
        $response = Http::patch("notify.eskiz.uz/api/auth/refresh")->json();
        return $response['data']['token'];
    }

    public function sendMessage($phone, $message)
    {
        $token = $this->getToken();
        $res = Http::withToken($token)->post("notify.eskiz.uz/api/message/sms/send", [
            'mobile_phone' => "998$phone",
            'message' => $message,
            'from' => '4546',
            'callback_url' => route('receive_status')
        ]);
//        try {
//            (new Client())->post(
//                "https://api.telegram.org/bot"
//                . "7548694251:AAGHmbGxP1GDfsTWWuLVpl5jNq_cwfgKJDo"
//                . "/sendMessage",
//                [
//                    'form_params' => [
//                        'chat_id' => -1002444198264,
//                        'message_thread_id'=>12,
//                        'text' => $message.PHP_EOL,
//                    ]
//                ]
//            );
//        } catch (GuzzleException $e) {
//            echo $e->getMessage();
//        }
        return $res;
    }


    public function getToken()
    {
        $response = Http::post("notify.eskiz.uz/api/auth/login", [
            'email' => $this->email,
            'password' => $this->password,
        ])->json();

        return $response['data']['token'];
    }
}
