<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

// Get car ID and selected values from query string
$carId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$pickupDate = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : date('d.m.Y');
$returnDate = isset($_GET['return_date']) ? $_GET['return_date'] : date('d.m.Y', strtotime('+3 days'));

$locationRepo = new \App\Repositories\LocationRepository();
$availableLocations = $locationRepo->getAll();
$defaultLocation = $availableLocations[0]['name'] ?? '';

// Separate pickup and dropoff locations if provided, or default to the same
$pickupArea = isset($_GET['pickup_area']) ? $_GET['pickup_area'] : (isset($_GET['dropoff_area']) ? $_GET['dropoff_area'] : $defaultLocation);
$dropoffArea = isset($_GET['dropoff_area']) ? $_GET['dropoff_area'] : $pickupArea;

// Use CarRepository instead of global $cars
$carRepo = new \App\Repositories\CarRepository();
$selectedCar = $carId > 0 ? $carRepo->getById($carId) : null;

render_layout(
    $selectedCar ? 'Бронирование' : 'Машина не найдена',
    function($data) use ($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea) {
        if (!$selectedCar) {
            echo render_container(render_booking_not_found());
            return;
        }

        // Display errors if any
        if (isset($_SESSION['booking_errors']) && !empty($_SESSION['booking_errors'])) {
            echo '<div class="mc-container mb-8">';
            foreach ($_SESSION['booking_errors'] as $error) {
                echo '<div class="bg-red-50 border-2 border-red-200 rounded-3xl p-6 mb-4 flex items-start gap-4">';
                echo '<div class="text-red-500 mt-1">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>';
                echo '</div>';
                echo '<div class="flex-1">';
                echo '<h3 class="font-black text-red-800 text-sm uppercase tracking-widest mb-1">Ошибка</h3>';
                echo '<p class="text-red-700 font-bold">' . htmlspecialchars($error) . '</p>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            unset($_SESSION['booking_errors']); // Clear errors after displaying
        }

        ob_start();
        ?>
        <!-- Progress Tracker -->
        <?php echo render_stepper(3, $selectedCar['id']); ?>

        <div class="mc-detail-page mc-container">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <?php
                $pricing = new \App\Services\PricingService();
                $priceInfo = $pricing->calculate($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea);
                echo render_booking_form($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea, $priceInfo['total']);
                echo render_booking_summary_sidebar($selectedCar, $pickupArea, $dropoffArea, $pickupDate, $returnDate);
                ?>
            </div>
        </div>
        <?php echo render_booking_scripts(); ?>
        <?php
        echo ob_get_clean();
    }
);
