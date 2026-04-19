<?php
/**
 * Централизованный обработчик ошибок для production
 * Подключается автоматически через config.php
 */

declare(strict_types=1);

class ErrorHandler {
    private static bool $registered = false;
    private static bool $isDebug = false;
    
    public static function register(bool $isDebug = false): void {
        if (self::$registered) {
            return;
        }
        
        self::$isDebug = $isDebug;
        self::$registered = true;
        
        // Обработчик исключений
        set_exception_handler([self::class, 'handleException']);
        
        // Обработчик ошибок
        set_error_handler([self::class, 'handleError']);
        
        // Обработчик фатальных ошибок
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    public static function handleException(Throwable $e): void {
        self::logError($e);
        self::displayError($e);
    }
    
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile = '',
        int $errline = 0
    ): bool {
        // Не обрабатываем подавленные ошибки (@)
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $error = new ErrorException($errstr, 0, $errno, $errfile, $errline);
        self::logError($error);
        
        // В debug режиме показываем ошибку
        if (self::$isDebug) {
            throw $error;
        }
        
        return true;
    }
    
    public static function handleShutdown(): void {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            
            self::logError($exception);
            self::displayError($exception);
        }
    }
    
    private static function logError(Throwable $e): void {
        $message = sprintf(
            "[%s] %s in %s:%d",
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        
        error_log($message);
        error_log("Stack trace:\n" . $e->getTraceAsString());
        
        // Используем logger если доступен
        if (function_exists('log_error')) {
            log_error($e->getMessage(), [
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $_SERVER['REQUEST_URI'] ?? '',
                'method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'ip' => self::getClientIp(),
            ]);
        }
        
        // Отправляем в Telegram критические ошибки
        if (!self::$isDebug && defined('TELEGRAM_BOT_TOKEN') && TELEGRAM_BOT_TOKEN !== '') {
            self::sendTelegramAlert($e);
        }
    }
    
    private static function displayError(Throwable $e): void {
        // Очищаем буфер вывода
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        http_response_code(500);
        
        if (self::$isDebug) {
            // В debug режиме показываем детали
            echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Error</title>";
            echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;}";
            echo ".error{background:#fff;border-left:4px solid #d32f2f;padding:20px;margin:20px 0;}";
            echo "h1{color:#d32f2f;margin:0 0 10px 0;}";
            echo "pre{background:#f5f5f5;padding:10px;overflow:auto;}</style></head><body>";
            echo "<div class='error'>";
            echo "<h1>" . get_class($e) . "</h1>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            echo "</div></body></html>";
        } else {
            // В production показываем дружелюбное сообщение
            echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Ошибка</title>";
            echo "<style>body{font-family:Arial,sans-serif;text-align:center;padding:50px;background:#f5f5f5;}";
            echo ".container{max-width:600px;margin:0 auto;background:#fff;padding:40px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}";
            echo "h1{color:#d32f2f;margin:0 0 20px 0;}";
            echo "p{color:#666;line-height:1.6;}</style></head><body>";
            echo "<div class='container'>";
            echo "<h1>Произошла ошибка</h1>";
            echo "<p>Извините, произошла техническая ошибка. Мы уже работаем над её устранением.</p>";
            echo "<p>Пожалуйста, попробуйте позже или <a href='/'>вернитесь на главную страницу</a>.</p>";
            echo "</div></body></html>";
        }
        
        exit(1);
    }
    
    private static function sendTelegramAlert(Throwable $e): void {
        if (!function_exists('sendTelegramMessage')) {
            return;
        }
        
        $message = "🚨 <b>CRITICAL ERROR</b>\n\n";
        $message .= "<b>Type:</b> " . get_class($e) . "\n";
        $message .= "<b>Message:</b> " . htmlspecialchars($e->getMessage()) . "\n";
        $message .= "<b>File:</b> " . basename($e->getFile()) . ":" . $e->getLine() . "\n";
        $message .= "<b>URL:</b> " . ($_SERVER['REQUEST_URI'] ?? 'unknown') . "\n";
        $message .= "<b>Method:</b> " . ($_SERVER['REQUEST_METHOD'] ?? 'unknown') . "\n";
        $message .= "<b>IP:</b> " . self::getClientIp() . "\n";
        $message .= "<b>Date:</b> " . date('Y-m-d') . "\n";
        
        sendTelegramMessage($message);
    }
    
    private static function getClientIp(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                return $ip;
            }
        }
        
        return 'unknown';
    }
}
