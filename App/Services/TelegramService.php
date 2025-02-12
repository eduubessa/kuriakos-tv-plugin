<?php

namespace App\Services;

use App\Core\KuriakosPlugin;

class TelegramService {

    private string $base_url = "https://api.telegram.org/bot";
    private string $token = "7261066212:AAEoY6u6vNP_HY3DchfJnf0eW7DsaOKj7fY";
    public function set_up(): static
    {
        $this->base_url = $this->base_url . $this->token;

        return $this;
    }

    private function request(string $method, array $params = [])
    {
        $url = "{$this->base_url}{$this->token}/{$method}";

        $options = [
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($params)
            ]
        ];

        $stream = stream_context_create($options);
        return json_decode(file_get_contents($url, false, $stream), true);
    }

    public function getUpdates()
    {
        return $this->request("getUpdates");
    }

    public function getMe()
    {
        return $this->request("getMe");
    }

    public function info()
    {
        ktv_dd($this->getMe());
    }

    public function sendMessage(string $message)
    {
        $update = $this->getUpdates();

        if(!$update['ok']){
            ktv_dd($update);
        }

        $this->request('sendMessage', [
            "chat_id" => $update["result"]["message"]["chat"]["id"],
            "text" => $message
        ]);
    }

    public static function init($file): ?TelegramService
    {
        static $instance = null;

        if(!$instance){
            $instance = new TelegramService($file);
        }

        return $instance;
    }

}
