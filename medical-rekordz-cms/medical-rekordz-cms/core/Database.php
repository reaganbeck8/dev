<?php

namespace Core;

class Database
{
    private static ?self $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        require_once CONFIG_PATH . '/database.php';

        try {
            $this->pdo = new \PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
        } catch (\PDOException $e) {
            error_log('[Database] Connection failed: ' . $e->getMessage());
            die('Database connection failed. Check logs for details.');
        }
    }

    // ─── Singleton ──────────────────────────────────────────────────────────────
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // ─── Table prefix helper ────────────────────────────────────────────────────
    public function table(string $name): string
    {
        return DB_PREFIX . $name;
    }

    // ─── Query helpers ──────────────────────────────────────────────────────────
    public function fetch(string $sql, array $params = []): array|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    // ─── Transactions ───────────────────────────────────────────────────────────
    public function beginTransaction(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    // ─── Raw PDO access (escape hatch) ──────────────────────────────────────────
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }
}
