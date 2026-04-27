<?php

// ─── HTML escaping ────────────────────────────────────────────────────────────
// Use on all user-supplied output to prevent XSS
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ─── Absolute URL generation ──────────────────────────────────────────────────
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

// ─── Versioned asset URL (cache busting) ─────────────────────────────────────
function asset(string $path): string
{
    return BASE_URL . '/assets/' . ltrim($path, '/') . '?v=' . APP_VERSION;
}

// ─── URL-safe slug generation ─────────────────────────────────────────────────
function slug(string $text): string
{
    $text = mb_strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// ─── Current datetime string in app timezone ─────────────────────────────────
// Use in models for created_at/updated_at instead of SQL NOW()
function now(string $format = 'Y-m-d H:i:s'): string
{
    return date($format);
}

// ─── Dump and die (development only) ─────────────────────────────────────────
function dd(mixed ...$vars): never
{
    if (!APP_DEBUG) {
        exit;
    }
    echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:16px;margin:16px;border-radius:6px;font-size:13px;overflow:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
        echo PHP_EOL;
    }
    echo '</pre>';
    exit;
}

// ─── Truncate string to max length ───────────────────────────────────────────
function truncate(string $text, int $limit = 100, string $suffix = '...'): string
{
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $limit)) . $suffix;
}

// ─── Format date for display ──────────────────────────────────────────────────
function formatDate(string $date, string $format = 'd M Y'): string
{
    return date($format, strtotime($date));
}

// ─── Format datetime for display ──────────────────────────────────────────────
function formatDateTime(string $date, string $format = 'd M Y, H:i'): string
{
    return date($format, strtotime($date));
}

// ─── Human-readable file size ─────────────────────────────────────────────────
function humanFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i     = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

// ─── Active nav state check ───────────────────────────────────────────────────
function isActive(string $path): bool
{
    $current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return str_starts_with(rtrim($current, '/'), '/' . ltrim($path, '/'));
}
