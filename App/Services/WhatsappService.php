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
        $this->access_token = "EAAQzAQ9nZBAgBO6jihnedHzCEZBjHWZAWG0cRYt8yZBVFlCjPZCUgooqKEOryIieunwWvYiol9QyCZCZAwZBNW0qR1RQOvg4gdZBnZAVLJ7cQjJ9li2d5RCqs2LEwlLyPz5LI5gLq7H36SstxJRacYcYNszayYnHcHLC9pMIJ7sLVIJnZAvOzJu5CfhqExwAhx2fR2YJAlNIdZCmOocq5qfYPBfEaaRo0ZBswNfHNsayQ5xjw4AZDZD";
    }

    public function send_message()
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

        $ch = curl_init("{$this->base_url}/{$this->account_id}/messages");

        curl_setopt($ch, CURLOPT_HEADER, [
            "Authorization: Bearer {$this->access_token}",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        ktv_dd($response);
    }
}