<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';
render_layout('Автомобили', function($data) {
    $cars = $data['cars'] ?? [];
    echo render_page_header('Наш автопарк', 'Выбирайте лучший автомобиль для вашего отдыха');
    echo render_car_section($cars);
    echo render_home_scripts();
}, [
    'meta_description' => 'Каталог автомобилей в аренду на Пхукете. Кроссоверы, седаны, пикапы, минивэны — от ฿800/день. Залог указан в карточке авто, страховка по договору.',
    'canonical' => app_base_url() . '/cars',
]);
