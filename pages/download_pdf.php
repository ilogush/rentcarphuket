<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../admin/auth.php'; // For session

use Dompdf\Dompdf;
use Dompdf\Options;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['last_booking'])) {
    header('Location: /');
    exit;
}

$booking = $_SESSION['last_booking'];
$carId = (int)($booking['car_id'] ?? 0);

// Use Repositories instead of direct data_*.php access
$carRepo = new \App\Repositories\CarRepository();
$siteRepo = new \App\Repositories\SiteRepository();

$selectedCar = $carRepo->getById($carId);
$contactInfo = $siteRepo->getContactInfo();

if (!$selectedCar) {
    header('Location: /');
    exit;
}

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans'); // Supports Cyrillic
$dompdf = new Dompdf($options);

$totalPrice = number_format((float)($booking['total_price'] ?? 0));
$orderId = $booking['order_id'] ?? '0000';
$clientName = htmlspecialchars($booking['client_name'] ?? '-');
$clientPhone = htmlspecialchars($booking['client_phone'] ?? '-');
$pickupDate = htmlspecialchars($booking['pickup_date'] ?? $booking['start_date'] ?? '-');
$returnDate = htmlspecialchars($booking['return_date'] ?? $booking['end_date'] ?? '-');
$pickupLocation = htmlspecialchars($booking['pickup_location'] ?? $booking['pickup_area'] ?? 'Пхукет');
$returnLocation = htmlspecialchars($booking['return_location'] ?? $booking['dropoff_area'] ?? $pickupLocation);
$carName = htmlspecialchars($selectedCar['name']);

$html = "
<html>
<head>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 14px; color: #333; line-height: 1.6; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .order-info { float: right; text-align: right; }
        .section-title { font-size: 18px; font-weight: bold; border-left: 4px solid #2563eb; padding-left: 10px; margin: 25px 0 15px; background: #f8fafc; padding-top: 5px; padding-bottom: 5px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { padding: 8px 0; vertical-align: top; }
        .label { color: #94a3b8; font-size: 12px; text-transform: uppercase; font-weight: bold; }
        .value { font-weight: bold; color: #1e293b; }
        .total-box { background: #2563eb; color: white; padding: 20px; border-radius: 12px; margin-top: 30px; text-align: right; }
        .total-amount { font-size: 28px; font-weight: bold; display: block; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .car-card { background: #f8fafc; padding: 20px; border-radius: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='order-info'>
            <div style='font-size: 12px; color: #64748b;'>ЗАКАЗ №</div>
            <div style='font-size: 20px; font-weight: bold; color: #2563eb;'>#{$orderId}</div>
            <div style='font-size: 12px; color: #94a3b8;'>" . date('d.m.Y') . "</div>
        </div>
        <div class='logo'>rentcarphuket.ru</div>
        <div style='font-size: 12px; color: #64748b;'>Аренда автомобилей на Пхукете</div>
    </div>

    <div class='section-title'>Клиент</div>
    <table class='grid'>
        <tr>
            <td width='50%'>
                <div class='label'>Имя</div>
                <div class='value'>{$clientName}</div>
            </td>
            <td width='50%'>
                <div class='label'>Телефон</div>
                <div class='value'>{$clientPhone}</div>
            </td>
        </tr>
    </table>

    <div class='section-title'>Автомобиль</div>
    <div class='car-card'>
        <div style='font-size: 22px; font-weight: bold; margin-bottom: 10px;'>{$carName}</div>
        <div class='label'>Тип</div>
        <div class='value'>" . htmlspecialchars($selectedCar['type'] ?? 'Standard') . "</div>
    </div>

    <div class='section-title'>Детали аренды</div>
    <table class='grid' style='background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;'>
        <tr>
            <td width='50%' style='padding: 15px; border-right: 1px solid #e2e8f0;'>
                <div class='label'>Получение</div>
                <div class='value' style='color: #2563eb;'>{$pickupDate}</div>
                <div style='font-size: 13px;'>{$pickupLocation}</div>
            </td>
            <td width='50%' style='padding: 15px;'>
                <div class='label'>Возврат</div>
                <div class='value' style='color: #2563eb;'>{$returnDate}</div>
                <div style='font-size: 13px;'>{$returnLocation}</div>
            </td>
        </tr>
    </table>

    <div class='total-box'>
        <span style='font-size: 14px; opacity: 0.8;'>Итого: ПРИ ПОЛУЧЕНИИ:</span>
        <span class='total-amount'>฿{$totalPrice}</span>
        <div style='font-size: 12px; opacity: 0.8; margin-top: 5px;'>Страховка действует по условиям договора</div>
    </div>

    <div class='footer'>
        <div>Спасибо, что выбрали rentcarphuket.ru!</div>
        <div style='margin-top: 5px;'>" . htmlspecialchars($contactInfo['phone']) . " | " . htmlspecialchars($contactInfo['email']) . "</div>
        <div style='margin-top: 5px;'>Пожалуйста, сохраните этот файл или сделайте скриншот для предъявления при получении авто.</div>
    </div>
</body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("booking-{$orderId}.pdf", ["Attachment" => true]);
