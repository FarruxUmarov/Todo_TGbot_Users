<?php

declare(strict_types=1);

require 'src/DB.php';

use GuzzleHttp\Client;

class Bot {
    private const TOKEN = "7031918736:AAG3rETeqWfT5oR0M7q1f8aMN_KSDtfI4r0";
    private const API = "https://api.telegram.org/bot" . self::TOKEN . "/";

    private Client $http;
    private PDO $pdo;

    public function __construct()
    {
        $this->http = new Client(['base_uri' => self::API]);
        $this->pdo = DB::connect();
    }

    public function handleStartCommand(int $chatId): void
    {
        $this->sendMessage($chatId, 'Welcome !!!');
    }

    public function addHandlerCommand(int $chatId): void
    {
        $this->sendMessage($chatId, "Enter the task:");
    }

    public function handleAllCommand(int $chatId): void
    {
        $this->sendTaskList($chatId, "All tasks:", true);
    }

    public function addTask(string $text, int $chatId): void
    {
        $task = new Task($text);
        $task->add();
        $this->sendMessage($chatId, "A new task has been added: $text");
    }

    public function getTask(int $chatId): void
    {
        $this->sendTaskList($chatId, "Tasks:", true);
    }

    public function checkTask(int $taskId): void
    {
        $this->complete($taskId);
    }

    public function unCheckTask(int $taskId): void
    {
        $this->uncompleted($taskId);
    }

    public function handleCallbackQuery(array $callbackQuery): void
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $callbackData = $callbackQuery['data'];

        if (strpos($callbackData, 'check_task_') === 0) {
            $taskId = (int)str_replace('check_task_', '', $callbackData);
            $this->checkTask($taskId);
            $this->sendTaskList($chatId, "Task $taskId checked", true);
        } elseif (strpos($callbackData, 'uncheck_task_') === 0) {
            $taskId = (int)str_replace('uncheck_task_', '', $callbackData);
            $this->unCheckTask($taskId);
            $this->sendTaskList($chatId, "Task $taskId unchecked", true);
        } elseif ($callbackData === 'get_tasks') {
            $this->getTask($chatId);
        } elseif (strpos($callbackData, 'delete_task_') === 0) {
            $taskId = (int)str_replace('delete_task_', '', $callbackData);
            $this->deleteTask($taskId, $chatId);
            $this->sendTaskList($chatId, "Task $taskId removed", true);
        }
    }

    private function complete(int $taskId): void
    {
        $stmt = $this->pdo->prepare("UPDATE todos SET status = true WHERE id = ?");
        $stmt->execute([$taskId]);
    }

    private function uncompleted(int $taskId): void
    {
        $stmt = $this->pdo->prepare("UPDATE todos SET status = false WHERE id = ?");
        $stmt->execute([$taskId]);
    }

    public function sendMessage(int $chatId, string $text, ?string $replyMarkup = null): void
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ];
        if ($replyMarkup) {
            $data['reply_markup'] = $replyMarkup;
        }

        try {
            $this->http->post('sendMessage', ['json' => $data]);
        } catch (\Exception $e) {
            error_log("Failed to send message: " . $e->getMessage());
        }
    }

    private function sendTaskList(int $chatId, string $message, bool $includeCheckboxes): void
    {
        $task = new Task();
        $tasks = $task->getAll();

        $text = '';
        $count = 1;
        $keyboard = ['inline_keyboard' => []];
        foreach ($tasks as $task) {
            $buttonText = $task['status'] ? "✅ $count" : "❌ $count";
            $callbackData = $task['status'] ? "uncheck_task_{$task['id']}" : "check_task_{$task['id']}";

            $text .= $count . ". " . ($task['status'] ? "<del>" . $task['text'] . "</del>" : $task['text']) . "\n";
            $keyboard['inline_keyboard'][] = [
                ['text' => $buttonText, 'callback_data' => $callbackData],
                ['text' => 'delete', 'callback_data' => "delete_task_{$task['id']}"]
            ];
            $count++;
        }

        $replyMarkup = json_encode($keyboard);
        $text = !empty($text) ? $text : 'No tasks available.';
        $this->sendMessage($chatId, $message . "\n" . $text, $replyMarkup);

    }

    private function deleteTask(int $taskId, int $chatId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = ?");
        $stmt->execute([$taskId]);

        $this->sendMessage($chatId, "Task $taskId has been deleted.");
    }
}

?>
