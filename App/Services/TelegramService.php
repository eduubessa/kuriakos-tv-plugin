<?php

namespace App\Services;

use App\Core\KuriakosPlugin;

class TelegramService {

    private string $base_url = "https://api.telegram.org/bot";
    private string $token = "7261066212:AAEoY6u6vNP_HY3DchfJnf0eW7DsaOKj7fY";
    private $quiz_chat_id;
    private $quiz_message_id = 0;

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

        $data = [
            'chat_id' => $update['result'][0]['message']['chat']['id'],
            'text' => $message
        ];

        $ch = curl_init("{$this->base_url}{$this->token}/sendMessage");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        ktv_dd($response);
    }

    public function createQuiz(string $question, array $options, int $correct_option_id)
    {
        $update = $this->getUpdates();

        if(!$update['ok']){
            ktv_dd($update);
        }

        $data = [
            'chat_id' => $update['result'][0]['message']['chat']['id'],
            'question' => $question,
            'options' => json_encode($options),
            'is_anonymous' => false,
            'type' => 'quiz',
            'correct_option_id' => $correct_option_id
        ];

        $ch = curl_init("{$this->base_url}{$this->token}/sendPoll");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        $this->quiz_chat_id = $response->result->chat->id;
        $this->quiz_message_id = $response->result->message_id;

        ktv_dd($this->quiz_message_id);
    }

    public function getPollResults(int $chat_id, int $message_id)
    {
        $data = [
            'chat_id' => $chat_id,
            'message_id' => $$message_id
        ];

        $ch = curl_init("{$this->base_url}{$this->token}/getPollResults");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        ktv_dd($response);
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
