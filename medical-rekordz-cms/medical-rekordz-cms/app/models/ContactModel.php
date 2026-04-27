<?php

namespace App\Models;

use Core\Model;

class ContactModel extends Model
{
    protected string $tableName = 'contact_submissions';

    public function save(array $data): int
    {
        $this->execute(
            "INSERT INTO {$this->table()} (name, email, phone, message, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['message'],
                $data['ip_address'] ?? null,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function getAll(int $limit = 50, int $offset = 0): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function findById(int $id): array|false
    {
        return $this->fetch("SELECT * FROM {$this->table()} WHERE id = ?", [$id]);
    }

    public function markRead(int $id): void
    {
        $this->execute("UPDATE {$this->table()} SET is_read = 1 WHERE id = ?", [$id]);
    }

    public function delete(int $id): void
    {
        $this->execute("DELETE FROM {$this->table()} WHERE id = ?", [$id]);
    }

    public function countUnread(): int
    {
        $row = $this->fetch("SELECT COUNT(*) as total FROM {$this->table()} WHERE is_read = 0");
        return (int) ($row['total'] ?? 0);
    }
}
