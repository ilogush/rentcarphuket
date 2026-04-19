<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

// Get car ID from query parameter
$carId = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Get search params from URL
$pickupArea = $_GET['pickup_area'] ?? '';
$dropoffArea = $_GET['dropoff_area'] ?? '';
$pickupDate = $_GET['pickup_date'] ?? date('d.m.Y');
$returnDate = $_GET['return_date'] ?? date('d.m.Y', strtotime('+3 days'));

// Use CarRepository instead of global $cars
$carRepo = new \App\Repositories\CarRepository();
$selectedCar = $carId > 0 ? $carRepo->getById($carId) : null;

// Build SEO meta data
$metaData = [];
if ($selectedCar) {
    $price = number_format($selectedCar['price']);
    $metaData['meta_description'] = "Арендуйте {$selectedCar['name']} на Пхукете от ฿{$price}/день. {$selectedCar['type']}, {$selectedCar['transmission']}, {$selectedCar['year']} г.в. Залог указан в карточке авто, доставка по условиям заказа.";
    $domain = app_base_url();
    $metaData['og_image'] = $domain . '/assets/images/' . $selectedCar['image'];
    $metaData['canonical'] = $domain . '/car/' . $selectedCar['id'];
}

// Render the page
render_layout(
    $selectedCar ? $selectedCar['name'] : 'Машина не найдена', 
    function($data) use($selectedCar, $pickupArea, $dropoffArea, $pickupDate, $returnDate) {
        $cars = $data['cars'] ?? [];
        if($selectedCar) {
            echo render_car_detail($selectedCar, $cars, $pickupArea, $dropoffArea, $pickupDate, $returnDate);
        } else {
            echo render_centered_notice(
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-12 h-12"><path stroke-linecap="round" stroke-linejoin="round" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'Машина не найдена',
                'К сожалению, запрашиваемый автомобиль не существует',
                render_client_button('Вернуться на главную', '/', get_icon('home', 'w-4 h-4'))
            );
        }
    },
    $metaData
);
