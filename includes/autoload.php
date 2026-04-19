<?php
declare(strict_types=1);

/**
 * Простой автозагрузчик классов
 * Поддерживает PSR-4 структуру
 */
spl_autoload_register(function ($class) {
    // Базовая директория проекта
    $baseDir = __DIR__ . '/../';
    
    // Маппинг пространств имен на директории
    $namespaceMap = [
        'App\\' => 'app/',
        'Components\\' => 'includes/components/',
    ];
    
    foreach ($namespaceMap as $namespace => $dir) {
        // Проверяем, начинается ли класс с этого namespace
        if (strpos($class, $namespace) === 0) {
            // Получаем относительное имя класса
            $relativeClass = substr($class, strlen($namespace));
            
            // Заменяем namespace разделители на разделители директорий
            $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';
            
            // Если файл существует, подключаем его
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Подключаем фабрику компонентов
require_once __DIR__ . '/components/ComponentFactory.php';
