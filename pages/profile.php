<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';
require_once __DIR__ . '/../includes/auth_client.php';

if (!is_client_logged_in()) {
    header("Location: /");
    exit;
}

$userRepo = new \App\Repositories\UserRepository();
$bookingRepo = new \App\Repositories\BookingRepository();
$carRepo = new \App\Repositories\CarRepository();

$user = get_logged_client();
$bookings = $bookingRepo->getByUser($user['id']);

// Sort by date desc
usort($bookings, function($a, $b) {
    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
});

render_layout('Мой профиль', function($data) use ($user, $bookings, $carRepo) {
    ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-6">
                <?php echo render_profile_sidebar($user); ?>
            </aside>

            <!-- Main area -->
            <div class="lg:col-span-3 space-y-10">
                <?php echo render_page_section_header('История бронирований'); ?>

                <?php if (empty($bookings)): ?>
                    <?php echo render_empty_state(
                        'calendar',
                        'Пока нет заказов',
                        'Вы еще не бронировали автомобиль. Самое время выбрать подходящую модель!',
                        render_client_button('В каталог', '/', get_icon('car', 'w-5 h-5'))
                    ); ?>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-6">
                        <?php foreach($bookings as $booking): 
                            $car = $carRepo->getById($booking['car_id']);
                            echo render_booking_card($booking, $car);
                        endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
});
