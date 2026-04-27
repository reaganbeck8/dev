<?php

namespace Core;

class Auth
{
    private Database $db;
    private Session $session;

    private const ADMIN_SESSION_KEY = 'auth_admin';
    private const SA_ATTEMPTS_KEY   = 'sa_login_attempts';

    public function __construct()
    {
        // Load superadmin config lazily — same pattern as Database.php
        require_once CONFIG_PATH . '/superadmin.php';

        $this->db      = Database::getInstance();
        $this->session = new Session();
    }

    // ─── Scope checks ─────────────────────────────────────────────────────────
    public function check(): bool
    {
        return $this->session->has(self::ADMIN_SESSION_KEY)
            || $this->session->has(SUPERADMIN_SESSION_KEY);
    }

    public function isAdmin(): bool
    {
        $user = $this->session->get(self::ADMIN_SESSION_KEY);
        return $user && in_array($user['role'], ['admin', 'superadmin'], true);
    }

    public function isSuperAdmin(): bool
    {
        $user = $this->session->get(SUPERADMIN_SESSION_KEY);
        return $user && $user['role'] === 'superadmin';
    }

    public function user(): ?array
    {
        return $this->session->get(self::ADMIN_SESSION_KEY)
            ?? $this->session->get(SUPERADMIN_SESSION_KEY)
            ?? null;
    }

    // ─── Guard (middleware) ───────────────────────────────────────────────────
    public function guard(string $scope): void
    {
        match ($scope) {
            'admin'      => $this->guardAdmin(),
            'superadmin' => $this->guardSuperAdmin(),
            default      => $this->redirectTo('/'),
        };
    }

    private function guardAdmin(): void
    {
        if ($this->session->isExpired(1800)) {
            $this->session->destroy();
            $this->session->flash('error', 'Your session has expired. Please log in again.');
            $this->redirectTo('/admin/login');
        }

        if (!$this->isAdmin()) {
            $this->redirectTo('/admin/login');
        }
    }

    private function guardSuperAdmin(): void
    {
        // IP whitelist — silent bounce, reveals nothing
        if (!$this->checkIpWhitelist()) {
            $this->redirectTo('/');
        }

        // Session expiry — silent bounce
        if ($this->session->isExpired(SUPERADMIN_SESSION_TIMEOUT)) {
            $this->session->destroy();
            $this->redirectTo('/');
        }

        // Not authenticated — silent bounce
        if (!$this->isSuperAdmin()) {
            $this->redirectTo('/');
        }
    }

    // ─── Login / Logout ───────────────────────────────────────────────────────
    public function login(array $user, string $scope): void
    {
        $this->session->regenerate();

        $sessionKey = $scope === 'superadmin'
            ? SUPERADMIN_SESSION_KEY
            : self::ADMIN_SESSION_KEY;

        $this->session->set($sessionKey, [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]);

        $this->session->set('_last_activity', time());
        $this->clearFailedAttempts($scope . ':' . strtolower($user['email']));
        $this->updateLastLogin($user['id']);
    }

    public function logout(string $scope): void
    {
        $sessionKey = $scope === 'superadmin'
            ? SUPERADMIN_SESSION_KEY
            : self::ADMIN_SESSION_KEY;

        $this->session->delete($sessionKey);
        $this->session->delete('_last_activity');
    }

    // ─── Credential attempt ───────────────────────────────────────────────────
    public function attempt(string $email, string $password, string $scope): array|false
    {
        $identifier = $scope . ':' . strtolower($email);

        if ($this->isLockedOut($identifier)) {
            return false;
        }

        $table = $this->db->table('users');
        $role  = $scope === 'superadmin' ? 'superadmin' : 'admin';

        $user = $this->db->fetch(
            "SELECT * FROM {$table} WHERE email = ? AND role = ? AND is_active = 1 LIMIT 1",
            [strtolower($email), $role]
        );

        if (!$user || !$this->verifyPassword($password, $user['password_hash'])) {
            $this->recordFailedAttempt($identifier);
            return false;
        }

        return $user;
    }

    // ─── Brute force protection ───────────────────────────────────────────────
    // Note: session-based for v1 — swap for DB-backed mrk_login_attempts for production hardening
    public function recordFailedAttempt(string $identifier): void
    {
        $key      = self::SA_ATTEMPTS_KEY . '_' . md5($identifier);
        $attempts = $this->session->get($key, ['count' => 0, 'first' => time()]);
        $attempts['count']++;
        $this->session->set($key, $attempts);
    }

    public function isLockedOut(string $identifier): bool
    {
        $key      = self::SA_ATTEMPTS_KEY . '_' . md5($identifier);
        $attempts = $this->session->get($key);

        if (!$attempts) {
            return false;
        }

        if ($attempts['count'] >= SUPERADMIN_MAX_ATTEMPTS) {
            if ((time() - $attempts['first']) < SUPERADMIN_LOCKOUT_TIME) {
                return true;
            }
            $this->clearFailedAttempts($identifier);
        }

        return false;
    }

    private function clearFailedAttempts(string $identifier): void
    {
        $this->session->delete(self::SA_ATTEMPTS_KEY . '_' . md5($identifier));
    }

    // ─── Password helpers ─────────────────────────────────────────────────────
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // ─── IP whitelist ─────────────────────────────────────────────────────────
    private function checkIpWhitelist(): bool
    {
        if (empty(SUPERADMIN_IP_WHITELIST)) {
            return true;
        }
        return in_array($_SERVER['REMOTE_ADDR'] ?? '', SUPERADMIN_IP_WHITELIST, true);
    }

    // ─── Redirect helper ──────────────────────────────────────────────────────
    private function redirectTo(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    // ─── Last login tracker ───────────────────────────────────────────────────
    private function updateLastLogin(int $userId): void
    {
        $table = $this->db->table('users');
        $this->db->execute(
            "UPDATE {$table} SET last_login = NOW() WHERE id = ?",
            [$userId]
        );
    }
}
