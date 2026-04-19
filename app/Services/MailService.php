<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Basic Email Service for order notifications.
 * NOTE: Currently uses PHP's mail() function which requires a properly configured 
 * MTA (like postfix or sendmail) on the server. For production use SMTP with PHPMailer.
 */
class MailService
{
    private static function getSiteHost(): string
    {
        $host = parse_url(app_base_url(), PHP_URL_HOST);

        return is_string($host) && $host !== '' ? $host : 'rentcarphuket.ru';
    }

    /**
     * Send order confirmation to client.
     */
    public static function sendOrderConfirmation(array $booking, array $selectedCar): bool
    {
        $to = $booking['client_email'] ?? ''; // Currently email is not collected in booking.php
        if (!$to) return false;

        $subject = "Ваш заказ #{$booking['order_id']} | Rent Car Phuket";
        $message = "Здравствуйте, {$booking['client_name']}!\n\n";
        $message .= "Ваш заказ #{$booking['order_id']} успешно оформлен.\n";
        $message .= "Автомобиль: {$selectedCar['name']}\n";
        $message .= "Период: {$booking['start_date']} - {$booking['end_date']}\n";
        $message .= "Итого: ฿" . number_format($booking['total_price']) . "\n\n";
        $message .= "Мы свяжемся с вами в ближайшее время для подтверждения.";

        $siteHost = self::getSiteHost();
        $adminEmail = env_value('ADMIN_EMAIL', 'triptopcarcom@gmail.com');
        $headers = "From: no-reply@{$siteHost}\r\n" .
                   "Reply-To: {$adminEmail}\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        return @mail($to, $subject, $message, $headers);
    }

    /**
     * Send order notification to admin.
     */
    public static function sendAdminNotification(array $booking, array $selectedCar): bool
    {
        $siteHost = self::getSiteHost();
        $to = env_value('ADMIN_EMAIL', 'triptopcarcom@gmail.com');
        $subject = "Новый заказ #{$booking['order_id']} | {$booking['client_name']}";
        $message = "Новый заказ на сайте {$siteHost}\n\n";
        $message .= "ID заказа: #{$booking['order_id']}\n";
        $message .= "Клиент: {$booking['client_name']} ({$booking['client_phone']})\n";
        $message .= "Автомобиль: {$selectedCar['name']}\n";
        $message .= "Даты: {$booking['start_date']} - {$booking['end_date']}\n";
        $message .= "Сумма: ฿" . number_format($booking['total_price']) . "\n";
        $message .= "Комментарий: " . ($booking['notes'] ?? '—') . "\n";

        $headers = "From: alerts@{$siteHost}\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        return @mail($to, $subject, $message, $headers);
    }
}
