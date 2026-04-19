<?php
declare(strict_types=1);

// Load .env file (optimized)
if (!function_exists('load_project_env')) {
    function load_project_env(string $path): void {
        static $loaded = false;
        if ($loaded || !is_readable($path)) return;
        
        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return;

        foreach ($lines as $line) {
            if ($line[0] === '#' || !str_contains($line, '=')) continue;
            
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            
            if ($key && !getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $_SERVER[$key] = $value;
            }
        }
        $loaded = true;
    }
}

load_project_env(__DIR__ . '/../.env');

// Подключаем автозагрузчик классов
require_once __DIR__ . '/autoload.php';

// Session (optimized - single start)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    
    // CSRF token
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

// Helper functions
function env_value(string $key, $default = null) {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

function app_base_url(): string {
    static $url;
    return $url ??= rtrim((string) env_value('APP_URL', ''), '/');
}

function app_image_storage_dir(): string {
    return __DIR__ . '/../assets/images';
}

function asset_url(string $path): string {
    static $rootPath = null;

    $path = '/' . ltrim($path, '/');
    $cleanPath = parse_url($path, PHP_URL_PATH) ?: $path;
    $rootPath ??= realpath(__DIR__ . '/..');

    if ($rootPath === false) {
        return $path;
    }

    $filePath = realpath($rootPath . $cleanPath);
    if ($filePath !== false && is_file($filePath) && str_starts_with($filePath, $rootPath . DIRECTORY_SEPARATOR)) {
        $separator = str_contains($path, '?') ? '&' : '?';
        return $path . $separator . 'v=' . filemtime($filePath);
    }

    return $path;
}

// Error handling
$isDebug = in_array(strtolower(env_value('APP_DEBUG', 'false')), ['true', '1', 'yes'], true);

if ($isDebug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../storage/logs/error.log');
}

// Telegram constants
define('TELEGRAM_BOT_TOKEN', env_value('TELEGRAM_BOT_TOKEN', ''));
define('TELEGRAM_CHAT_ID', env_value('TELEGRAM_CHAT_ID', ''));
define('TELEGRAM_ADMIN_CHAT_ID', env_value('TELEGRAM_ADMIN_CHAT_ID', TELEGRAM_CHAT_ID));

function sendTelegramMessage(string $message): bool {
    return \App\Services\TelegramNotificationService::sendMessage($message);
}

// Exception handler (optimized)
set_exception_handler(function (Throwable $e) use ($isDebug) {
    $errorMsg = sprintf(
        "[%s] %s in %s:%d",
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
    
    error_log($errorMsg . "\n" . $e->getTraceAsString());
    
    // Logger integration
    if (function_exists('log_error')) {
        log_error($e->getMessage(), [
            'type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
        ]);
    }
    
    // Telegram alert (production only)
    if (!$isDebug && TELEGRAM_BOT_TOKEN) {
        sendTelegramMessage(
            "🚨 <b>ERROR</b>\n\n" .
            "<b>Type:</b> " . get_class($e) . "\n" .
            "<b>Message:</b> " . htmlspecialchars($e->getMessage()) . "\n" .
            "<b>File:</b> " . basename($e->getFile()) . ":" . $e->getLine() . "\n" .
            "<b>URL:</b> " . ($_SERVER['REQUEST_URI'] ?? 'unknown')
        );
    }
    
    if ($isDebug) throw $e;
    
    http_response_code(500);
    echo '<h1>Произошла ошибка</h1><p>Пожалуйста, попробуйте позже.</p>';
    exit;
});
