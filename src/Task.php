<?php

use GuzzleHttp\Client;

class Task
{
    private PDO $pdo;
    public string $text;

    public function __construct(string $text = '')
    {
        $this->text = $text;
        $this->pdo = DB::connect();
    }

    public function add_add(string $text): void
    {
        $status = 0;
        $stmt = $this->pdo->prepare("INSERT INTO todos (text, status) VALUES (:text, :status)");
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
    }

    public function add(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO todos (text, status) VALUES (:text, 0)");
        $stmt->execute(['text' => $this->text]);
    }

    public function getAll(): array
    {
        return $this->pdo->query("SELECT * FROM todos")->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function complete(int $id): bool
    {
        $status = true;
        $stmt = $this->pdo->prepare("UPDATE todos SET status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        return $stmt->execute();
    }
    

    public function uncompleted(int $id): bool
    {
        $status = false;
        $stmt = $this->pdo->prepare("UPDATE todos SET status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM todos WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
