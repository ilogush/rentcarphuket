<?php
declare(strict_types=1);

/**
 * Главная страница (новая версия с презентером)
 * 
 * Пример использования новой компонентной архитектуры
 */

require_once __DIR__ . '/../includes/layout.php';

// Создаем презентер для главной страницы
$presenter = new \App\Presenters\HomePresenter();

// Рендерим страницу через layout
render_layout('Главная', function() use ($presenter) {
    echo $presenter->render();
});
