<?php

namespace App\Models;

use Core\Model;

class SettingModel extends Model
{
    protected string $tableName = 'settings';

    /**
     * Get a setting value by key, with optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $row = $this->fetch(
            "SELECT setting_value, setting_type FROM {$this->table()} WHERE setting_key = ?",
            [$key]
        );

        if (!$row) {
            return $default;
        }

        return $this->cast($row['setting_value'], $row['setting_type']);
    }

    /**
     * Set a setting value (insert or update).
     */
    public function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $existing = $this->fetch(
            "SELECT id FROM {$this->table()} WHERE setting_key = ?",
            [$key]
        );

        $storedValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        if ($existing) {
            $this->execute(
                "UPDATE {$this->table()} SET setting_value = ?, setting_type = ?, updated_at = NOW() WHERE setting_key = ?",
                [$storedValue, $type, $key]
            );
        } else {
            $this->execute(
                "INSERT INTO {$this->table()} (setting_key, setting_value, setting_type, setting_group, updated_at) VALUES (?, ?, ?, ?, NOW())",
                [$key, $storedValue, $type, $group]
            );
        }
    }

    /**
     * Get all settings in a group as key => value.
     */
    public function getGroup(string $group): array
    {
        $rows = $this->fetchAll(
            "SELECT setting_key, setting_value, setting_type FROM {$this->table()} WHERE setting_group = ?",
            [$group]
        );

        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $this->cast($row['setting_value'], $row['setting_type']);
        }
        return $result;
    }

    /**
     * Get all settings as key => value.
     */
    public function getAll(): array
    {
        $rows = $this->fetchAll("SELECT setting_key, setting_value, setting_type FROM {$this->table()}");

        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $this->cast($row['setting_value'], $row['setting_type']);
        }
        return $result;
    }

    /**
     * Cast a stored value to its declared type.
     */
    private function cast(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'bool'   => in_array(strtolower($value), ['1', 'true', 'yes'], true),
            'int'    => (int) $value,
            'json'   => json_decode($value, true),
            default  => $value,
        };
    }
}
