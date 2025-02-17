<?php

namespace App\Services;

class WhatsappService
{

    private string $app_id;
    private string $app_secret;
    private string $access_token;

    private string $account_id;
    private int $phone_number_id;

    private string $base_url = "https://graph.facebook.com/v22.0/";

    public function __construct()
    {
        $this->app_id = "1181979553298440";
        $this->account_id = "499439309929076";
        $this->phone_number_id = "543683172167285";
        $this->app_secret = "4f5790090d0aff921ba46d61f155bf9b";
        $this->access_token = "EAAQzAQ9nZBAgBO3DZCDc2nT2GEgwZA7NAb9mvDnCc8KUIO7nn4TJXC2v7Txr1kErLXWFdRWfWXEVjCMEEpAT0jh10gBCsieMv1WdrJ17Lz7BinW5G7sHR40pqKhxKbW35hidEuwXOTdwvZAFZC0pDo6HCwHwgyfnkO9NEW1ltWBUkM9kFNmyBmzyH1AIWzKdO0y8KpDvUEDDp3X4q5yuTmTapWM1EyIUyQziikTYjTwZDZD";
    }

    public function sendMessage()
    {
        $data = [
            "messaging_product" => "whatsapp",
            "to" => "351913946525",
            "type" => "template",
            "template" => [
                "name" => "hello_world",
                "language" => [
                    "code" => "en_US"
                ]
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v22.0/{$this->phone_number_id}/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->access_token}",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
        echo $response;

        ktv_dd($response);
    }

    public function createQuiz()
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => '351913946525',
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => 'Qual cidade você prefere?',
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => '1',  // ID do botão, que você pode ajustar conforme a resposta
                                'title' => 'Lisboa'  // Título do botão
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => '2',  // ID do segundo botão
                                'title' => 'Porto'  // Título do botão
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v22.0/{$this->phone_number_id}/messages");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->access_token}",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }

        curl_close($ch);
        echo $response;

        ktv_dd($response);
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }
}