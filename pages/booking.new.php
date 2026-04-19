<?php
declare(strict_types=1);

/**
 * Страница бронирования (новая версия с презентером)
 */

require_once __DIR__ . '/../includes/layout.php';

// Получаем ID автомобиля
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Получаем параметры бронирования
$params = [
    'pickup_date' => $_GET['pickup_date'] ?? '',
    'return_date' => $_GET['return_date'] ?? '',
    'pickup_area' => $_GET['pickup_area'] ?? '',
    'dropoff_area' => $_GET['dropoff_area'] ?? '',
];

// Создаем презентер
$presenter = new \App\Presenters\BookingPresenter();

// Рендерим страницу
render_layout('Бронирование', function() use ($presenter, $carId, $params) {
    echo $presenter->render($carId, $params);
});
