<?php

namespace Core;

class Session
{
    private const FLASH_KEY = '_flash';

    // ─── Core CRUD ────────────────────────────────────────────────────────────
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function delete(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // ─── Flash messages ───────────────────────────────────────────────────────
    public function flash(string $type, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$type] = $message;
    }

    public function getFlash(string $type): ?string
    {
        $message = $_SESSION[self::FLASH_KEY][$type] ?? null;
        unset($_SESSION[self::FLASH_KEY][$type]);
        return $message;
    }

    public function hasFlash(string $type): bool
    {
        return isset($_SESSION[self::FLASH_KEY][$type]);
    }

    public function getAllFlash(): array
    {
        $flashes = $_SESSION[self::FLASH_KEY] ?? [];
        unset($_SESSION[self::FLASH_KEY]);
        return $flashes;
    }

    // ─── Expiry check (idle timeout) ──────────────────────────────────────────
    public function isExpired(int $timeout): bool
    {
        $last = $this->get('_last_activity');
        if ($last && (time() - $last) > $timeout) {
            return true;
        }
        $this->set('_last_activity', time());
        return false;
    }

    // ─── Session management ───────────────────────────────────────────────────
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}
