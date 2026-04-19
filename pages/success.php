<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

$bookingSaved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['booking_errors'] = ['Ошибка безопасности (CSRF). Пожалуйста, обновите страницу и попробуйте снова.'];
        header('Location: /');
        exit;
    }

    // Rate limiting: max 5 bookings per 10 min
    $canSubmit = \App\Services\RateLimiter::attempt('booking_submit', 5, 600);
    if (!$canSubmit) {
        $retryAfter = \App\Services\RateLimiter::retryAfter('booking_submit', 600);
        $_SESSION['booking_errors'] = ["Слишком много запросов. Попробуйте через {$retryAfter} сек."];
        header('Location: /');
        exit;
    }

    // Server-side validation
    $clientName = trim($_POST['client_name'] ?? '');
    $clientPhone = trim($_POST['client_phone'] ?? '');
    $clientPhone2 = trim($_POST['client_phone2'] ?? '');
    $clientEmail = trim($_POST['client_email'] ?? '');
    $clientDob = trim($_POST['client_dob'] ?? '');
    $carId = $_POST['car_id'] ?? '0';
    $pickupDate = $_POST['pickup_date'] ?? '';
    $returnDate = $_POST['return_date'] ?? '';
    $pickupTime = preg_replace('/[^0-9:]/', '', $_POST['pickup_time'] ?? '12:00') ?: '12:00';
    $dropoffTime = preg_replace('/[^0-9:]/', '', $_POST['dropoff_time'] ?? '12:00') ?: '12:00';
    $pickupArea = $_POST['pickup_area'] ?? '';
    $dropoffArea = $_POST['dropoff_area'] ?? '';
    $deliveryAddress = trim($_POST['delivery_address'] ?? '');
    $childSeat = ($_POST['child_seat'] ?? '0') === '1';
    $notes = trim($_POST['notes'] ?? '');

    $errors = [];

    // Validate required fields
    if (empty($clientName) || mb_strlen($clientName) < 2) {
        $errors[] = 'Укажите имя (минимум 2 символа)';
    }
    if (empty($clientPhone) || mb_strlen($clientPhone) < 6) {
        $errors[] = 'Укажите корректный номер телефона';
    }
    if (empty($carId) || $carId === '0') {
        $errors[] = 'Не выбран автомобиль';
    }
    if (empty($pickupDate) || empty($returnDate)) {
        $errors[] = 'Укажите даты аренды';
    }

    // Validate car exists
    $carRepo = new \App\Repositories\CarRepository();
    $selectedCar = $carRepo->getById($carId);
    if (!$selectedCar) {
        $errors[] = 'Автомобиль не найден';
    } elseif (($selectedCar['status'] ?? 'active') !== 'active') {
        $errors[] = 'К сожалению, этот автомобиль временно недоступен для бронирования';
    }

    // Validate dates
    $d1 = DateTime::createFromFormat('d.m.Y', $pickupDate);
    $d2 = DateTime::createFromFormat('d.m.Y', $returnDate);
    if (!$d1 || !$d2) {
        $errors[] = 'Некорректный формат дат';
    } elseif ($d2 <= $d1) {
        $errors[] = 'Дата возврата должна быть позже даты получения';
    } else {
        // Check minimum rental period (1 day)
        $diff = $d1->diff($d2);
        if ($diff->days < 1) {
            $errors[] = 'Минимальный срок аренды: 1 день';
        }
    }

    // Check car availability
    $bookingRepo = new \App\Repositories\BookingRepository();
    if (!$bookingRepo->isAvailable($carId, $pickupDate, $returnDate)) {
        $errors[] = 'К сожалению, этот автомобиль уже забронирован на выбранные даты. Пожалуйста, выберите другие даты или другую модель.';
    }

    // Server-side price calculation (don't trust client-side total_price)
    $totalPrice = 0;
    if ($selectedCar && $d1 && $d2 && empty($errors)) {
        $pricing = new \App\Services\PricingService();
        $priceInfo = $pricing->calculate($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea);
        $totalPrice = $priceInfo['total'];
    }

    // Sanitize inputs
    $clientName = htmlspecialchars($clientName, ENT_QUOTES, 'UTF-8');
    $clientPhone = htmlspecialchars($clientPhone, ENT_QUOTES, 'UTF-8');
    $clientPhone2 = htmlspecialchars($clientPhone2, ENT_QUOTES, 'UTF-8');
    $clientEmail = htmlspecialchars($clientEmail, ENT_QUOTES, 'UTF-8');
    $clientDob = htmlspecialchars($clientDob, ENT_QUOTES, 'UTF-8');
    $deliveryAddress = htmlspecialchars($deliveryAddress, ENT_QUOTES, 'UTF-8');
    $notes = htmlspecialchars($notes, ENT_QUOTES, 'UTF-8');

    if (empty($errors)) {
        $bookingRepo = new \App\Repositories\BookingRepository();

        $newBookingData = array(
            'user_id' => '0',
            'car_id' => $carId,
            'start_date' => $pickupDate,
            'end_date' => $returnDate,
            'pickup_time' => $pickupTime,
            'dropoff_time' => $dropoffTime,
            'status' => 'pending',
            'total_price' => $totalPrice,
            'deposit' => $priceInfo['deposit'] ?? 0,
            'created_at' => date('Y-m-d'),
            'pickup_location' => $pickupArea,
            'return_location' => $dropoffArea,
            'delivery_address' => $deliveryAddress,
            'client_name' => $clientName,
            'client_phone' => $clientPhone,
            'client_phone2' => $clientPhone2,
            'client_email' => $clientEmail,
            'client_dob' => $clientDob,
            'child_seat' => $childSeat,
            'notes' => $notes
        );

        $newBookingData['order_id'] = (string)random_int(100000, 999999);
        $bookingResult = $bookingRepo->createIfAvailable($newBookingData);
        $savedBooking = !empty($bookingResult['success']) ? ($bookingResult['booking'] ?? null) : null;

        if ($savedBooking) {
            $_SESSION['last_booking'] = $savedBooking;

            \App\Services\TelegramNotificationService::sendBookingNotification($savedBooking, $selectedCar);

            // Send Email notifications
            \App\Services\MailService::sendOrderConfirmation($_SESSION['last_booking'], $selectedCar);
            \App\Services\MailService::sendAdminNotification($_SESSION['last_booking'], $selectedCar);
        } else {
            $_SESSION['booking_errors'] = [
                ($bookingResult['error'] ?? '') === 'unavailable'
                    ? 'К сожалению, этот автомобиль уже забронирован на выбранные даты. Пожалуйста, выберите другие даты или другую модель.'
                    : 'Не удалось сохранить бронирование. Попробуйте еще раз.'
            ];
            $backUrl = '/booking?id=' . urlencode($carId);
            header("Location: $backUrl");
            exit;
        }
    } else {
        // Log validation errors
        if (function_exists('log_error')) {
            log_error('Booking validation failed', [
                'errors' => $errors,
                'car_id' => $carId,
                'client_email' => $clientEmail,
                'pickup_date' => $pickupDate,
                'return_date' => $returnDate
            ]);
        }
        
        // Redirect back with errors
        $_SESSION['booking_errors'] = $errors;
        $backUrl = '/booking?id=' . urlencode($carId);
        header("Location: $backUrl");
        exit;
    }
}

// Redirect if no booking data
if (!isset($_SESSION['last_booking'])) {
    header('Location: /');
    exit;
}

$booking = $_SESSION['last_booking'];

render_layout('Успех', function($data) use ($booking) {
    echo '<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">';
    echo render_stepper(4);
    echo render_centered_notice(
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-10 h-10"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg><div class="absolute -bottom-2">' . render_text_badge('SUCCESS', 'green') . '</div>',
        'Заказ успешно оформлен!',
        'Номер вашего заказа: #' . $booking['order_id'] . '<br>Мы получили вашу заявку и свяжемся с вами в течение 15 минут для подтверждения бронирования и уточнения деталей аренды.',
        render_client_button('Вернуться на главную', '/', get_icon('home', 'w-4 h-4')) . render_client_button('Скачать PDF', '/download-pdf', get_icon('download', 'w-4 h-4'), 'secondary', '', '_blank'),
        'bg-green-100 text-green-600 shadow-none',
        'w-24 h-24',
        'text-4xl font-black text-gray-800 mb-2',
        'text-gray-500 text-lg mb-12 max-w-2xl mx-auto'
    );
    echo '</div>';
});
