<?php
declare(strict_types=1);

// Carga de configuración en memoria
$GLOBALS['app_config'] = require __DIR__ . '/config.php';

function config(string $key, mixed $default = null): mixed
{
    $config = $GLOBALS['app_config'] ?? [];
    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (is_array($value) && array_key_exists($segment, $value)) {
            $value = $value[$segment];
        } else {
            return $default;
        }
    }

    return $value;
}

function base_url(string $path = ''): string
{
    $base = config('app.base_url', '');
    if ($base === '') {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if ($base === '.' || $base === '/') {
            $base = '';
        }
    }

    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function asset_url(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message === null) {
        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }
        $value = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $value;
    }

    $_SESSION['flash'][$key] = $message;
    return null;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $path = '/' . ltrim($uri, '/');

    if ($base && strpos($path, $base) === 0) {
        $path = substr($path, strlen($base));
    }

    return $path === '' ? '/' : $path;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Debes iniciar sesión para acceder.');
        redirect('login');
    }
}

function require_admin(): void
{
    if (!is_logged_in() || ($_SESSION['user']['role'] ?? null) !== 'admin') {
        flash('error', 'No tienes permisos para realizar esta acción.');
        redirect('/');
    }
}

function old(string $key, string $default = ''): string
{
    if (!isset($_SESSION['old'])) {
        return $default;
    }

    $value = $_SESSION['old'][$key] ?? $default;
    return is_string($value) ? $value : $default;
}

function store_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function render(string $view, array $data = [], ?string $layout = 'layout'): void
{
    $viewFile = __DIR__ . '/Views/' . $view . '.php';
    if (!file_exists($viewFile)) {
        http_response_code(500);
        echo 'Vista no encontrada: ' . e($view);
        exit;
    }

    $currentPath = current_path();
    extract($data);

    ob_start();
    include $viewFile;
    $content = ob_get_clean();

    if ($layout === null) {
        echo $content;
        return;
    }

    $layoutFile = __DIR__ . '/Views/' . $layout . '.php';
    if (!file_exists($layoutFile)) {
        http_response_code(500);
        echo 'Layout no encontrado: ' . e($layout);
        exit;
    }

    include $layoutFile;
}
