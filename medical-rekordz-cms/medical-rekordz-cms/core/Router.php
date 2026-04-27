<?php

namespace Core;

class Router
{
    private string $uri;
    private string $method;
    private Auth $auth;

    private const DEFAULT_CONTROLLERS = [
        'public'     => 'Home',
        'admin'      => 'Dashboard',
        'superadmin' => 'Dashboard',
    ];

    private const NAMESPACE_MAP = [
        'public'     => 'App\\Controllers\\',
        'admin'      => 'App\\Controllers\\Admin\\',
        'superadmin' => 'App\\Controllers\\Superadmin\\',
    ];

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri    = $this->parseUri();
        $this->auth   = new Auth();
    }

    // ─── Entry point ──────────────────────────────────────────────────────────
    public function dispatch(): void
    {
        // 1. Validate HTTP_HOST first — kill invalid requests immediately
        $this->validateHost();

        // 2. CSRF check on all POST requests
        if ($this->method === 'POST') {
            $this->validateCsrf();
        }

        // 3. Detect scope and remaining segments
        [$scope, $segments] = $this->detectScope();

        // 4. Resolve controller, action, and id
        [$controllerName, $action, $id] = $this->resolveRoute($scope, $segments);

        // 5. Build fully qualified class name
        $class = self::NAMESPACE_MAP[$scope] . $controllerName . 'Controller';

        // 6. Verify class and method exist
        if (!class_exists($class)) {
            $this->abort(404);
        }

        if (!method_exists($class, $action)) {
            $this->abort(404);
        }

        // 7. Apply auth guard per scope (public routes skip guard)
        if ($scope === 'admin') {
            // Login routes bypass guard
            if ($controllerName !== 'Auth') {
                $this->auth->guard('admin');
            }
        } elseif ($scope === 'superadmin') {
            if ($controllerName !== 'Auth') {
                $this->auth->guard('superadmin');
            }
        }

        // 8. Dispatch
        $controller = new $class($this->auth);
        $id !== null
            ? $controller->$action($id)
            : $controller->$action();
    }

    // ─── URI parsing ──────────────────────────────────────────────────────────
    private function parseUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }

    // ─── Scope detection ──────────────────────────────────────────────────────
    private function detectScope(): array
    {
        // Lazy-load superadmin config so SUPERADMIN_SLUG is available
        require_once CONFIG_PATH . '/superadmin.php';

        $segments = array_values(array_filter(explode('/', $this->uri)));

        if (empty($segments)) {
            return ['public', []];
        }

        $first = $segments[0];

        if ($first === 'admin') {
            return ['admin', array_slice($segments, 1)];
        }

        if ($first === SUPERADMIN_SLUG) {
            return ['superadmin', array_slice($segments, 1)];
        }

        return ['public', $segments];
    }

    // ─── Route resolution ─────────────────────────────────────────────────────
    private function resolveRoute(string $scope, array $segments): array
    {
        $controllerName = self::DEFAULT_CONTROLLERS[$scope];
        $action         = 'index';
        $id             = null;

        if (!empty($segments[0])) {
            $controllerName = $this->toStudlyCase($segments[0]);
        }

        if (!empty($segments[1])) {
            if (is_numeric($segments[1])) {
                $id = (int) $segments[1];
            } else {
                $action = $segments[1];
            }
        }

        if (!empty($segments[2])) {
            $id = is_numeric($segments[2]) ? (int) $segments[2] : $segments[2];
        }

        return [$controllerName, $action, $id];
    }

    // ─── Host validation ──────────────────────────────────────────────────────
    private function validateHost(): void
    {
        $host = strtolower(explode(':', $_SERVER['HTTP_HOST'] ?? '')[0]);

        if (!in_array($host, ALLOWED_HOSTS, true)) {
            $this->logError('Invalid HTTP_HOST: ' . $host);
            http_response_code(400);
            exit('Bad Request');
        }
    }

    // ─── CSRF validation ──────────────────────────────────────────────────────
    private function validateCsrf(): void
    {
        $token        = $_POST['_csrf_token'] ?? '';
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        if (empty($token) || !hash_equals($sessionToken, $token)) {
            $this->logError('CSRF validation failed for URI: ' . $this->uri);
            $this->abort(403);
        }
    }

    // ─── Abort ────────────────────────────────────────────────────────────────
    private function abort(int $code): never
    {
        http_response_code($code);

        $view = VIEW_PATH . '/public/errors/' . $code . '.php';
        if (file_exists($view)) {
            require $view;
        } else {
            echo $code === 404 ? 'Page not found.' : 'Forbidden.';
        }

        exit;
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function toStudlyCase(string $segment): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segment)));
    }

    private function logError(string $message): void
    {
        $logFile = STORAGE_PATH . '/logs/app.log';
        $entry   = '[' . date('Y-m-d H:i:s') . '] [ROUTER] ' . $message . PHP_EOL;
        error_log($entry, 3, $logFile);
    }
}
