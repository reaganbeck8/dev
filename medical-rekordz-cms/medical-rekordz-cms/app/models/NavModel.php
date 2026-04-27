<?php

namespace App\Models;

use Core\Model;

class NavModel extends Model
{
    protected string $tableName = 'nav_items';

    public function getMenuItems(bool $activeOnly = true): array
    {
        $where = $activeOnly ? "WHERE is_active = 1" : "";
        $rows = $this->fetchAll(
            "SELECT n.*, p.slug as page_slug FROM {$this->table()} n
             LEFT JOIN {$this->db->table('pages')} p ON n.page_id = p.id
             {$where} ORDER BY n.sort_order"
        );

        return $this->buildTree($rows);
    }

    public function findById(int $id): array|false
    {
        return $this->fetch("SELECT * FROM {$this->table()} WHERE id = ?", [$id]);
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO {$this->table()} (label, url, page_id, parent_id, sort_order, is_active)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['label'],
                $data['url'] ?? null,
                $data['page_id'] ?? null,
                $data['parent_id'] ?? null,
                $data['sort_order'] ?? 0,
                $data['is_active'] ?? 1,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        foreach (['label', 'url', 'page_id', 'parent_id', 'sort_order', 'is_active'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

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

    /**
     * Build nested tree from flat rows using parent_id.
     */
    private function buildTree(array $rows, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] == $parentId) {
                $row['children'] = $this->buildTree($rows, (int) $row['id']);
                $tree[] = $row;
            }
        }
        return $tree;
    }
}
