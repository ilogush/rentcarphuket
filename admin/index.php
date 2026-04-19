<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once 'actions/handle_cars.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

if (isset($_GET['logout'])) { logout(); }

$editing_car = null;
if (isset($_GET['edit'])) {
    foreach ($cars as $car) { if ($car['id'] == $_GET['edit']) { $editing_car = $car; break; } }
}
if (isset($_GET['add'])) {
    $editing_car = [
        'id' => '', 'name' => '', 'type' => 'Кроссовер', 'year' => date('Y'), 
        'transmission' => 'АКПП', 'seats' => 5, 'fuel' => 'Бензин', 'engine' => '1.5', 
        'price' => 1000, 'deposit' => 3000, 'status' => 'active', 'image' => ''
    ];
}

$content = '';
$scripts = '';

if ($editing_car) {
    ob_start();
    include __DIR__ . '/partials/car-form.php';
    $content = ob_get_clean();
    $scripts = render_admin_car_form_script($_SESSION['csrf_token']);
} else {
    // Pagination
    $allCars = array_reverse($cars);
    $totalItems = count($allCars);
    $perPage = 15;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $pagedCars = array_slice($allCars, ($currentPage - 1) * $perPage, $perPage);

    $tableHtml = render_admin_data_table(
        ['Автомобиль', 'Тип', 'Цена', 'Депозит', 'Двигатель', 'КПП', 'Скидка', 'Статус'],
        $pagedCars,
        function($car) { return render_admin_car_row($car); }
    );
    $content = $tableHtml . render_admin_pagination($currentPage, ceil($totalItems / $perPage), ($currentPage - 1) * $perPage, $perPage, $totalItems);
}

render_admin_page(
    'cars', 
    'Админ-панель', 
    'Управление автопарком', 
    render_admin_button('Добавить авто', '/admin?add=1', 'primary', 'flex items-center gap-2', 'link', get_icon('plus', 'w-4 h-4')),
    $content,
    $scripts . render_confirm_modal(),
    [
        'error' => [
            'upload' => ['message' => 'Не удалось сохранить изображение. Проверьте формат файла.', 'type' => 'error'],
        ],
        'success' => [
            'saved' => ['message' => 'Автомобиль сохранён.', 'type' => 'success'],
            'deleted' => ['message' => 'Автомобиль удалён.', 'type' => 'success'],
        ],
    ]
);
