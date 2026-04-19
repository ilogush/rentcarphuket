<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/config.php';

use App\Repositories\CarRepository;
use App\Services\PricingService;

// Validate input
$carId = $_GET['car_id'] ?? null;
if (!$carId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing car_id'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Get parameters with defaults
$pickupDate = $_GET['pickup_date'] ?? date('d.m.Y');
$returnDate = $_GET['return_date'] ?? date('d.m.Y', strtotime('+3 days'));
$pickupArea = $_GET['pickup_area'] ?? '';
$dropoffArea = $_GET['dropoff_area'] ?? '';

// Get car
$carRepo = new CarRepository();
$car = $carRepo->getById($carId);

if (!$car) {
    http_response_code(404);
    echo json_encode(['error' => 'Car not found'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Calculate price
$pricing = new PricingService();
$priceInfo = $pricing->calculate($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea);

// Response
echo json_encode([
    'success' => true,
    'days' => $priceInfo['days'],
    'daily_price' => $priceInfo['daily_final'],
    'daily_base' => $priceInfo['daily_base'],
    'delivery_price' => $priceInfo['delivery_price'],
    'return_price' => $priceInfo['return_price'],
    'deposit' => $priceInfo['deposit'],
    'total' => $priceInfo['total'],
    'discount_pct' => $priceInfo['discount_pct'],
    'duration_rate' => $priceInfo['duration_rate'],
    'duration_label' => $priceInfo['duration_label'],
    'season_rate' => $priceInfo['season_rate'],
    'season_label' => $priceInfo['season_label'],
    'breakdown' => $priceInfo['breakdown'],
    'calculation_steps' => $priceInfo['calculation_steps'],
], JSON_UNESCAPED_UNICODE);
