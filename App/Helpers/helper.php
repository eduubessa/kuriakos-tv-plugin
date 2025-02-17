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

    $bot = new \App\Services\TelegramService();
    $bot->getPollResults($chat_id, $message_id);
}

function ktv_validation_pools(string $platform): array
{
    global $errors;

    $question = ktv_input_clear($_POST["{$platform}_question"] ?? '');
    $option1 = ktv_input_clear($_POST["{$platform}_option_1"] ?? '');
    $option2 = ktv_input_clear($_POST["{$platform}_option_2"] ?? '');
    $option3 = ktv_input_clear($_POST["{$platform}_option_3"] ?? '');

    if (empty($question)) {
        $errors[] = "A pergunta para {$platform} é obrigatória.";
    }
    if (empty($option1) || empty($option2) || empty($option3)) {
        $errors[] = "Todas as opções para {$platform} devem ser preenchidas.";
    }

    return [
        'question' => $question,
        'options' => [$option1, $option2, $option3]
    ];
}

// Verifica se algum formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit_whatsapp"])) {
        $whatsapp_data = ktv_validation_pools("whatsapp");
        if (empty($errors)) {
            $success = "Pool no WhatsApp criada com sucesso!";
            // Aqui podes adicionar a lógica para processar os dados
        }
    }

    if (isset($_POST["submit_telegram"])) {
        $telegram_data = ktv_validation_pools("telegram");
        if (empty($errors)) {
            $success = "Pool no Telegram criada com sucesso!";
            // Aqui podes adicionar a lógica para processar os dados
        }
    }

    if (isset($_POST["submit_all"])) {
        $whatsapp_data = ktv_validation_pools("whatsapp");
        $telegram_data = ktv_validation_pools("telegram");

        if (empty($errors)) {
            $success = "Pools no WhatsApp e Telegram criadas com sucesso!";
            // Aqui podes adicionar a lógica para processar os dados
        }
    }
}

function ktv_input_clear($data): string
{
    return htmlspecialchars(strip_tags(trim($data)));
}