<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if (!isset($_POST['csrf_token']) || !hash_equals(csrf_token(), (string) $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Permintaan ditolak. Token keamanan tidak sesuai. Kembali lalu muat ulang halaman.');
    }
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function take_flash(): ?array
{
    if (!isset($_SESSION['flash_message'])) {
        return null;
    }
    $msg = [$_SESSION['flash_message'], $_SESSION['flash_type'] ?? 'success'];
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    return $msg;
}

function go(string $path): void
{
    header('Location: ' . $path, true, 303);
    exit;
}

function positive_id($raw): ?int
{
    $number = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return ($number === false) ? null : (int) $number;
}
