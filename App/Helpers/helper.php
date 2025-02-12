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

function ktv_telegram_send_message()
{
    $bot = new \App\Services\TelegramService();
    $bot->sendMessage("Olá, como estás?");
}

function ktv_telegram_create_poll($question, $options)
{
    $bot = new \App\Services\TelegramService();
    $bot->createQuiz("Quem é que foi morto na cruz do calvário por amor de nós?", [
        "A) João",
        "B) Pedro",
        "C) Jesus",
    ], 3);
}
