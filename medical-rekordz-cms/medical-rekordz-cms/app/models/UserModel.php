<?php

namespace App\Models;

use Core\Model;

class UserModel extends Model
{
    protected string $tableName = 'users';

    public function findByEmail(string $email): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE email = ?",
            [$email]
        );
    }

    public function findById(int $id): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE id = ?",
            [$id]
        );
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO {$this->table()} (name, email, password_hash, role, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['role'] ?? 'admin',
                $data['is_active'] ?? 1,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        foreach (['name', 'email', 'role', 'is_active'] as $field) {
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

    public function updatePassword(int $id, string $password): void
    {
        $this->execute(
            "UPDATE {$this->table()} SET password_hash = ?, updated_at = NOW() WHERE id = ?",
            [password_hash($password, PASSWORD_BCRYPT), $id]
        );
    }

    public function getAll(string $role = null): array
    {
        if ($role) {
            return $this->fetchAll(
                "SELECT id, name, email, role, is_active, last_login, created_at FROM {$this->table()} WHERE role = ? ORDER BY name",
                [$role]
            );
        }
        return $this->fetchAll(
            "SELECT id, name, email, role, is_active, last_login, created_at FROM {$this->table()} ORDER BY name"
        );
    }

    public function delete(int $id): void
    {
        $this->execute("DELETE FROM {$this->table()} WHERE id = ?", [$id]);
    }

    public function updateLastLogin(int $id): void
    {
        $this->execute(
            "UPDATE {$this->table()} SET last_login = NOW() WHERE id = ?",
            [$id]
        );
    }
}
