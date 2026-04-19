<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

$bookingRepo = new \App\Repositories\BookingRepository();
$carRepo = new \App\Repositories\CarRepository();
$userRepo = new \App\Repositories\UserRepository();

$bookings = $bookingRepo->getAll();
$bookings = array_reverse($bookings); // Newest first
$carsById = [];
foreach ($carRepo->getAll() as $car) { $carsById[(string)$car['id']] = $car; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { header("Location: /admin/bookings?error=csrf"); exit; }
    $bookingRepo->delete($_POST['booking_id']);
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true, 'message' => 'Бронирование удалено!']); exit;
    }
    header("Location: /admin/bookings?success=deleted"); exit;
}

// Pagination logic
$totalItems = count($bookings);
$perPage = 15;
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagedBookings = array_slice($bookings, ($currentPage - 1) * $perPage, $perPage);

// Unified Table Generation
$tableHtml = render_admin_data_table(
    ['ID / Дата', 'Клиент', 'Автомобиль', 'Период / Локации', 'Сумма', 'Управление'],
    $pagedBookings,
    function($booking) use ($userRepo, $carsById) {
        $user = $userRepo->findById($booking['user_id']);
        $car = $carsById[(string)$booking['car_id']] ?? null;
        return render_admin_booking_row($booking, $user, $car, $_SESSION['csrf_token']);
    }
);

$content = $tableHtml . render_admin_pagination($currentPage, ceil($totalItems / $perPage), ($currentPage - 1) * $perPage, $perPage, $totalItems);
$scripts = render_admin_delete_script('booking', 'Удалить бронирование?', 'Вы уверены? Данное действие необратимо.');

render_admin_page('bookings', 'Бронирования', 'Бронирования', '', $content, $scripts);
