<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

render_layout('Главная', function($data) {
    $cars = $data['cars'];
    $locations = $data['locations'];
    
    // 1. Hero Block
    echo render_hero(
        'Аренда авто Пхукет', 
        'Лучшие условия аренды и премиальный сервис на острове', 
        'https://images.unsplash.com/photo-1519451241324-20b4ea2c4220?auto=format&fit=crop&q=80&w=2070',
        $locations
    );

    // 2. Cars Catalog Section
    echo render_car_section($cars);

    // 3. Trust and service details
    echo render_home_trust_section();

    // 4. Home Specific Scripts
    echo render_home_scripts();
});
