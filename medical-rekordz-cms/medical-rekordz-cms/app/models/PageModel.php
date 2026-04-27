<?php

namespace App\Models;

use Core\Model;

class PageModel extends Model
{
    protected string $tableName = 'pages';

    public function findById(int $id): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function findBySlug(string $slug): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE slug = ? AND deleted_at IS NULL",
            [$slug]
        );
    }

    public function getPublished(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE status = 'published' AND deleted_at IS NULL ORDER BY sort_order, title"
        );
    }

    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE deleted_at IS NULL ORDER BY sort_order, title"
        );
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO {$this->table()} (title, slug, content, meta_title, meta_desc, status, sort_order, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['title'],
                $data['slug'],
                $data['content'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_desc'] ?? null,
                $data['status'] ?? 'draft',
                $data['sort_order'] ?? 0,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        foreach (['title', 'slug', 'content', 'meta_title', 'meta_desc', 'status', 'sort_order'] as $field) {
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
            "UPDATE {$this->table()} SET " . implode(', ', $fields) . " WHERE id = ? AND deleted_at IS NULL",
            $params
        );
    }

    public function delete(int $id): void
    {
        $this->execute(
            "UPDATE {$this->table()} SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );
    }
}
