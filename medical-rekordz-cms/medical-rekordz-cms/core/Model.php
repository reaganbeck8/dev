<?php

namespace Core;

class Model
{
    protected Database $db;
    protected string $tableName = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ─── Prefixed table name ──────────────────────────────────────────────────
    protected function table(): string
    {
        if (empty($this->tableName)) {
            throw new \RuntimeException(static::class . ' must define $tableName');
        }
        return $this->db->table($this->tableName);
    }

    // ─── Convenience passthrough helpers ──────────────────────────────────────
    protected function fetch(string $sql, array $params = []): array|false
    {
        return $this->db->fetch($sql, $params);
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    protected function execute(string $sql, array $params = []): int
    {
        return $this->db->execute($sql, $params);
    }

    protected function lastInsertId(): string|false
    {
        return $this->db->lastInsertId();
    }

    // ─── Transaction passthrough ──────────────────────────────────────────────
    protected function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    protected function commit(): void
    {
        $this->db->commit();
    }

    protected function rollback(): void
    {
        $this->db->rollback();
    }
}
