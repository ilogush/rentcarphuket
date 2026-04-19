<?php
/**
 * Enhanced Logging System for Production Debugging
 * Система логирования для диагностики проблем в production
 */

declare(strict_types=1);

class Logger {
    private static ?Logger $instance = null;
    private string $logDir;
    private string $errorLog;
    private string $accessLog;
    private string $debugLog;
    private bool $isDebug;
    
    private function __construct() {
        $this->logDir = __DIR__ . '/../storage/logs';
        $this->errorLog = $this->logDir . '/error.log';
        $this->accessLog = $this->logDir . '/access.log';
        $this->debugLog = $this->logDir . '/debug.log';
        $this->isDebug = $this->isDebugMode();
        
        // Создаем директорию если не существует
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }
    }
    
    public static function getInstance(): Logger {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }
    
    private function isDebugMode(): bool {
        $debug = $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? getenv('APP_DEBUG') ?? 'false';
        return in_array(strtolower($debug), ['true', '1', 'yes'], true);
    }
    
    /**
     * Записать ошибку
     */
    public function error(string $message, array $context = []): void {
        $this->writeLog($this->errorLog, 'ERROR', $message, $context);
        
        // В production отправляем критические ошибки в Telegram
        if (!$this->isDebug && function_exists('sendTelegramMessage')) {
            $telegramMsg = "🚨 <b>ERROR</b>\n\n";
            $telegramMsg .= "<b>Message:</b> " . htmlspecialchars($message) . "\n";
            $telegramMsg .= "<b>Date:</b> " . date('Y-m-d') . "\n";
            if (!empty($context)) {
                $telegramMsg .= "<b>Context:</b> " . htmlspecialchars(json_encode($context, JSON_UNESCAPED_UNICODE)) . "\n";
            }
            sendTelegramMessage($telegramMsg);
        }
    }
    
    /**
     * Записать предупреждение
     */
    public function warning(string $message, array $context = []): void {
        $this->writeLog($this->errorLog, 'WARNING', $message, $context);
    }
    
    /**
     * Записать информационное сообщение
     */
    public function info(string $message, array $context = []): void {
        $this->writeLog($this->accessLog, 'INFO', $message, $context);
    }
    
    /**
     * Записать отладочное сообщение (только в debug режиме)
     */
    public function debug(string $message, array $context = []): void {
        if ($this->isDebug) {
            $this->writeLog($this->debugLog, 'DEBUG', $message, $context);
        }
    }
    
    /**
     * Логировать HTTP запрос
     */
    public function logRequest(): void {
        $data = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
        ];
        
        $this->info('HTTP Request', $data);
    }
    
    /**
     * Логировать исключение
     */
    public function logException(Throwable $e): void {
        $context = [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
        
        $this->error($e->getMessage(), $context);
    }
    
    /**
     * Логировать SQL запрос
     */
    public function logQuery(string $query, array $params = [], float $executionTime = 0): void {
        if ($this->isDebug) {
            $context = [
                'query' => $query,
                'params' => $params,
                'execution_time' => round($executionTime, 4) . 's',
            ];
            $this->debug('SQL Query', $context);
        }
    }
    
    /**
     * Логировать действие пользователя
     */
    public function logUserAction(string $action, array $data = []): void {
        $context = array_merge([
            'user_id' => $_SESSION['user_id'] ?? 'guest',
            'ip' => $this->getClientIp(),
            'action' => $action,
        ], $data);
        
        $this->info('User Action', $context);
    }
    
    /**
     * Получить IP клиента
     */
    private function getClientIp(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
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
    
    /**
     * Записать в лог файл
     */
    private function writeLog(string $file, string $level, string $message, array $context = []): void {
        $timestamp = date('Y-m-d');
        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[$timestamp] [$level] $message$contextStr\n";
        
        @file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);
        
        // Ротация логов если файл больше 10MB
        if (file_exists($file) && filesize($file) > 10 * 1024 * 1024) {
            $this->rotateLog($file);
        }
    }
    
    /**
     * Ротация лог файла
     */
    private function rotateLog(string $file): void {
        $backupFile = $file . '.' . date('Y-m-d-His') . '.bak';
        @rename($file, $backupFile);
        
        // Удаляем старые бэкапы (старше 7 дней)
        $files = glob(dirname($file) . '/*.bak');
        foreach ($files as $oldFile) {
            if (filemtime($oldFile) < time() - 7 * 24 * 3600) {
                @unlink($oldFile);
            }
        }
    }
    
    /**
     * Получить последние записи из лога
     */
    public function getRecentLogs(string $type = 'error', int $lines = 50): array {
        $file = match($type) {
            'error' => $this->errorLog,
            'access' => $this->accessLog,
            'debug' => $this->debugLog,
            default => $this->errorLog,
        };
        
        if (!file_exists($file)) {
            return [];
        }
        
        $content = file($file);
        return array_slice($content, -$lines);
    }
}

/**
 * Helper функции для быстрого доступа
 */
function logger(): Logger {
    return Logger::getInstance();
}

function log_error(string $message, array $context = []): void {
    Logger::getInstance()->error($message, $context);
}

function log_warning(string $message, array $context = []): void {
    Logger::getInstance()->warning($message, $context);
}

function log_info(string $message, array $context = []): void {
    Logger::getInstance()->info($message, $context);
}

function log_debug(string $message, array $context = []): void {
    Logger::getInstance()->debug($message, $context);
}
