<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Simple file-based rate limiter with static access
 */
class RateLimiter {
    private static $storageDir = __DIR__ . '/../../storage/rate_limits';
    
    private static function init() {
        if (!is_dir(self::$storageDir)) {
            mkdir(self::$storageDir, 0755, true);
        }
    }

    private static function getFilePath(string $key): string {
        $ip = self::getIp();
        $fullKey = $key . '_' . $ip;
        return self::$storageDir . '/' . md5($fullKey) . '.json';
    }

    private static function readRequests($handle, int $now, int $periodSeconds): array {
        rewind($handle);
        $raw = stream_get_contents($handle);
        $data = json_decode($raw ?: '', true);
        $requests = $data['requests'] ?? [];

        return array_values(array_filter($requests, function($ts) use ($now, $periodSeconds) {
            return (int)$ts > ($now - $periodSeconds);
        }));
    }

    private static function writeRequests($handle, array $requests): void {
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode(['requests' => array_values($requests)]));
        fflush($handle);
    }
    
    /**
     * Attempt an action and check if it's within limits
     */
    public static function attempt($key, $limit, $periodSeconds) {
        self::init();
        $file = self::getFilePath((string)$key);
        $now = time();

        $handle = fopen($file, 'c+');
        if ($handle === false) {
            return false;
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                return false;
            }

            $requests = self::readRequests($handle, $now, (int)$periodSeconds);
            if (count($requests) >= $limit) {
                return false;
            }

            $requests[] = $now;
            self::writeRequests($handle, $requests);
            return true;
        } finally {
            if (is_resource($handle)) {
                @flock($handle, LOCK_UN);
                @fclose($handle);
            }
        }
    }
    
    /**
     * Get remaining wait time
     */
    public static function retryAfter($key, $periodSeconds) {
        self::init();
        $file = self::getFilePath((string)$key);

        if (!file_exists($file)) {
            return 0;
        }

        $handle = fopen($file, 'r');
        if ($handle === false) {
            return 0;
        }

        try {
            if (!flock($handle, LOCK_SH)) {
                return 0;
            }

            $raw = stream_get_contents($handle);
            $data = json_decode($raw ?: '', true);

            if (!empty($data['requests'])) {
                $earliest = min(array_map('intval', $data['requests']));
                $wait = $periodSeconds - (time() - $earliest);
                return max(0, $wait);
            }
        } finally {
            if (is_resource($handle)) {
                @flock($handle, LOCK_UN);
                @fclose($handle);
            }
        }

        return 0;
    }
    
    /**
     * Get client IP address
     */
    public static function getIp(): string {
        // Trust X-Forwarded-For only from known proxy (server itself or private range)
        $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($forwarded !== '') {
            $first = trim(explode(',', $forwarded)[0]);
            if (filter_var($first, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $first;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
