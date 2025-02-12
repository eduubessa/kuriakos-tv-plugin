<?php

namespace App\Services;

use App\Core\KuriakosPlugin;

class TelegramService {

    private string $base_url = "https://api.telegram.org/bot";
    private string $token = "7261066212:AAEoY6u6vNP_HY3DchfJnf0eW7DsaOKj7fY";
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

        $resp = new \stdClass();
        $resp = json_decode($response, true);

        echo $resp->result->message_id;

        exit();
    }

    public function getPollResults()
    {
        $data = [
            'msg_id' => $this->quiz_message_id
        ];

        $ch = curl_init("{$this->base_url}{$this->token}/getPollResults");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->quiz_message_id = $response['result'][0]['message_id'];

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
