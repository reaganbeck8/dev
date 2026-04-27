<?php

namespace App\Models;

use Core\Model;

class VideoModel extends Model
{
    protected string $tableName = 'videos';

    public function findById(int $id): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE id = ?",
            [$id]
        );
    }

    public function getPublished(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE status = 'published' ORDER BY sort_order, created_at DESC"
        );
    }

    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} ORDER BY sort_order, created_at DESC"
        );
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO {$this->table()} (title, description, video_type, youtube_url, media_id, thumbnail_id, sort_order, status, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['title'],
                $data['description'] ?? null,
                $data['video_type'] ?? 'youtube',
                $data['youtube_url'] ?? null,
                $data['media_id'] ?? null,
                $data['thumbnail_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['status'] ?? 'published',
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        foreach (['title', 'description', 'video_type', 'youtube_url', 'media_id', 'thumbnail_id', 'sort_order', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        $fields[] = "updated_at = NOW()";
        $params[] = $id;

        $this->execute(
            "UPDATE {$this->table()} SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        );
    }

    public function delete(int $id): void
    {
        $this->execute("DELETE FROM {$this->table()} WHERE id = ?", [$id]);
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $position => $id) {
            $this->execute(
                "UPDATE {$this->table()} SET sort_order = ? WHERE id = ?",
                [$position, $id]
            );
        }
    }
}
