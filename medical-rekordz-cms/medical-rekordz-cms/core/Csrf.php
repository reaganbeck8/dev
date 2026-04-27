<?php

namespace Core;

class Csrf
{
    // ─── Token access ─────────────────────────────────────────────────────────
    public static function token(): string
    {
        return $_SESSION['_csrf_token'] ?? '';
    }

    // ─── Hidden input field for forms ─────────────────────────────────────────
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8')
            . '">';
    }
}
