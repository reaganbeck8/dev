<?php

namespace Core;

class Controller
{
    protected Auth    $auth;
    protected Session $session;
    protected string  $scope = 'public';

    public function __construct(Auth $auth)
    {
        $this->auth    = $auth;
        $this->session = new Session();
        $this->detectScope();
    }

    // ─── Scope detection from namespace ───────────────────────────────────────
    private function detectScope(): void
    {
        $class = static::class;
        if (str_contains($class, 'Controllers\\Admin\\')) {
            $this->scope = 'admin';
        } elseif (str_contains($class, 'Controllers\\Superadmin\\')) {
            $this->scope = 'superadmin';
        }
    }

    // ─── View rendering ───────────────────────────────────────────────────────
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data, $this->scope);
    }

    // ─── Redirect ─────────────────────────────────────────────────────────────
    protected function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    // ─── JSON response ────────────────────────────────────────────────────────
    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // ─── Current user ─────────────────────────────────────────────────────────
    protected function currentUser(): ?array
    {
        return $this->auth->user();
    }

    // ─── Abort ────────────────────────────────────────────────────────────────
    protected function abort(int $code): never
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
}
