<?php

function ktv_settings_page($page_name){
    return require_once(__DIR__ . "/../../Resources/Views/settings/{$page_name}.php");
}

function ktv_page($page_name){
    return require_once(__DIR__ . "/../../Resources/Views/{$page_name}.php");
}

function ktv_dd($data){
    echo "<pre>";
    echo json_encode($data);
    echo "</pre>";
    exit();
}

function ktv_telegram_send_message(): void
{
    $bot = new \App\Services\TelegramService();
    $bot->sendMessage("Olá, como estás?");
}

function ktv_telegram_create_quiz(string $question, array $options, int $correct_option_id)
{
    $bot = new \App\Services\TelegramService();
    return $bot->createQuiz($question, $options, ($correct_option_id-1));
}
function ktv_telegram_get_poll_results(int $chat_id, int $message_id): void
{
    ktv_dd($chat_id);
}