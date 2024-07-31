<?php

$bot = new Bot();
$task = new Task();

$update = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_URI'] === '/tasks'){
    echo json_encode($task->getAll());
    return;
}

if (isset($update)){
    if (!isset($update->update_id)){
        $path = parse_url($_REQUEST['REQUEST_URI'])['path'];
        if ($path === '/add'){
            $task->add_add($update->text);
        }
    }
}

if (isset($update['message'])) {
    $message = $update['message'];
    $chatId = $message['chat']['id'];
    $text = $message['text'] ?? null;

    if ($text === '/start') {
        $bot->handleStartCommand($chatId);
    } elseif ($text === '/add') {
        $bot->addHandlerCommand($chatId);
    } elseif ($text === '/all') {
        $bot->handleAllCommand($chatId);
    } elseif ($text !== null) {
        $bot->addTask($text, $chatId);
    } else {
        echo "Task text is required.";
    }
} elseif (isset($update['callback_query'])) {
    $bot->handleCallbackQuery($update['callback_query']);
}