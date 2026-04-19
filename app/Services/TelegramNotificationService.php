<?php
declare(strict_types=1);

namespace App\Services;

class TelegramNotificationService
{
    private const API_TIMEOUT_SECONDS = 5;

    public static function sendBookingNotification(array $booking, array $selectedCar): bool
    {
        $orderId     = self::text($booking['order_id'] ?? $booking['id'] ?? '-');
        $carName     = self::text($selectedCar['name'] ?? '-');
        $startDate   = self::text($booking['start_date'] ?? '-');
        $endDate     = self::text($booking['end_date'] ?? '-');
        $pickupArea  = self::text($booking['pickup_location'] ?? '-');
        $returnArea  = self::text($booking['return_location'] ?? '-');
        $clientName  = self::text($booking['client_name'] ?? '-');
        $clientPhone = self::text($booking['client_phone'] ?? '-');
        $notes       = trim((string)($booking['notes'] ?? ''));
        $totalPrice  = number_format((float)($booking['total_price'] ?? 0));
        $deposit     = number_format((float)($booking['deposit'] ?? (float)($selectedCar['deposit'] ?? 0)));

        $message  = "Новая заявка с сайта MonkeyCar\n\n";
        $message .= "<b>{$carName}</b>\n";
        $message .= "Заказ #{$orderId}\n\n";
        $message .= "{$startDate} — {$endDate}\n";
        $message .= "{$pickupArea} → {$returnArea}\n\n";
        $message .= "{$clientName}\n";
        $message .= "{$clientPhone}\n";

        if ($notes !== '') {
            $message .= self::text($notes) . "\n";
        }

        $message .= "\n<b>К оплате: {$totalPrice} THB</b>\n";
        $message .= "Депозит: {$deposit} THB";

        return self::sendMessage($message);
    }

    private static function formatDate(string $date): string
    {
        // Convert d.m.Y → d/m/y
        $parts = explode('.', $date);
        if (count($parts) === 3) {
            return $parts[0] . '/' . $parts[1] . '/' . substr($parts[2], 2);
        }
        return $date;
    }

    public static function sendMessage(string $message): bool
    {
        $token = self::config('TELEGRAM_BOT_TOKEN');
        $chatId = self::config('TELEGRAM_ADMIN_CHAT_ID') ?: self::config('TELEGRAM_CHAT_ID');

        if ($token === '' || $chatId === '') {
            self::logWarning('Telegram notification skipped: token or chat id is empty');
            return false;
        }

        $response = self::request($token, [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => '1',
        ]);

        if (!$response['ok']) {
            self::logWarning('Telegram notification failed', [
                'status' => $response['status'],
                'error' => $response['error'],
            ]);
        }

        return $response['ok'];
    }

    private static function request(string $token, array $payload): array
    {
        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
        $body = http_build_query($payload);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $body,
                'timeout' => self::API_TIMEOUT_SECONDS,
                'ignore_errors' => true,
            ],
        ]);

        $rawResponse = @file_get_contents($url, false, $context);
        $status = self::responseStatus($http_response_header ?? []);

        if ($rawResponse === false) {
            return [
                'ok' => false,
                'status' => $status,
                'error' => 'request_failed',
            ];
        }

        $decoded = json_decode($rawResponse, true);
        if (!is_array($decoded)) {
            return [
                'ok' => false,
                'status' => $status,
                'error' => 'invalid_json_response',
            ];
        }

        return [
            'ok' => ($decoded['ok'] ?? false) === true,
            'status' => $status,
            'error' => (string)($decoded['description'] ?? ''),
        ];
    }

    private static function responseStatus(array $headers): int
    {
        $firstHeader = $headers[0] ?? '';
        if (preg_match('/\s(\d{3})\s/', $firstHeader, $matches)) {
            return (int)$matches[1];
        }

        return 0;
    }

    private static function adminBookingUrl(): string
    {
        $baseUrl = rtrim(self::config('APP_URL'), '/');
        if ($baseUrl === '') {
            return '';
        }

        return $baseUrl . '/admin/bookings';
    }

    private static function config(string $key): string
    {
        if (function_exists('env_value')) {
            return trim((string)env_value($key, ''));
        }

        return trim((string)($_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: ''));
    }

    private static function text($value): string
    {
        $decoded = html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return htmlspecialchars($decoded, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function logWarning(string $message, array $context = []): void
    {
        if (function_exists('log_warning')) {
            log_warning($message, $context);
            return;
        }

        $contextText = $context ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        error_log($message . $contextText);
    }
}
