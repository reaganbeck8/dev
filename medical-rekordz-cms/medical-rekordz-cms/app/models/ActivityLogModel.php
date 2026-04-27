<?php

namespace App\Models;

use Core\Model;

class ActivityLogModel extends Model
{
    protected string $tableName = 'activity_log';

    public function record(string $action, ?int $userId = null, ?string $entityType = null, ?int $entityId = null, ?string $detail = null): void
    {
        $this->execute(
            "INSERT INTO {$this->table()} (user_id, action, entity_type, entity_id, detail, ip_address, created_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [
                $userId,
                $action,
                $entityType,
                $entityId,
                $detail,
                $_SERVER['REMOTE_ADDR'] ?? null,
            ]
        );
    }

    public function getAll(int $limit = 50, int $offset = 0): array
    {
        return $this->fetchAll(
            "SELECT l.*, u.name as user_name FROM {$this->table()} l
             LEFT JOIN {$this->db->table('users')} u ON l.user_id = u.id
             ORDER BY l.created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function getByUser(int $userId, int $limit = 50): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function getByEntity(string $entityType, int $entityId): array
    {
        return $this->fetchAll(
            "SELECT l.*, u.name as user_name FROM {$this->table()} l
             LEFT JOIN {$this->db->table('users')} u ON l.user_id = u.id
             WHERE l.entity_type = ? AND l.entity_id = ?
             ORDER BY l.created_at DESC",
            [$entityType, $entityId]
        );
    }
}
