<?php
declare(strict_types=1);

/**
 * Health Check Endpoint для быстрой диагностики
 * Доступ: /health-check.php?secret=<HEALTH_CHECK_SECRET>
 */

require_once __DIR__ . '/../includes/config.php';

$secret = $_GET['secret'] ?? '';
$expectedSecret = (string)env_value('HEALTH_CHECK_SECRET', '');

if ($expectedSecret === '') {
    http_response_code(404);
    exit;
}

if (!hash_equals($expectedSecret, (string)$secret)) {
    http_response_code(403);
    exit('Access denied');
}

header('Content-Type: application/json');

$status = [
    'timestamp' => date('Y-m-d'),
    'status' => 'checking',
    'checks' => [],
    'errors' => [],
    'warnings' => [],
];

// 1. Проверка PHP версии
$phpVersion = PHP_VERSION;
$status['checks']['php_version'] = [
    'value' => $phpVersion,
    'ok' => version_compare($phpVersion, '8.0.0', '>='),
];
if (!$status['checks']['php_version']['ok']) {
    $status['errors'][] = 'PHP version must be >= 8.0';
}

// 2. Проверка .env файла
$envPath = __DIR__ . '/../.env';
$status['checks']['env_file'] = [
    'exists' => file_exists($envPath),
    'readable' => file_exists($envPath) && is_readable($envPath),
];
if (!$status['checks']['env_file']['exists']) {
    $status['errors'][] = '.env file not found';
}

// 3. Проверка config.php
try {
    $status['checks']['config'] = ['loaded' => true];
    
    // Проверка APP_URL
    $appUrl = env_value('APP_URL', '');
    $status['checks']['app_url'] = [
        'value' => $appUrl,
        'ok' => !empty($appUrl),
    ];
    if (empty($appUrl)) {
        $status['errors'][] = 'APP_URL not set in .env';
    }
    
    // Проверка APP_DEBUG
    $appDebug = env_value('APP_DEBUG', 'false');
    $status['checks']['app_debug'] = [
        'value' => $appDebug,
        'ok' => in_array(strtolower($appDebug), ['false', '0', 'no'], true),
    ];
    if (!$status['checks']['app_debug']['ok']) {
        $status['warnings'][] = 'APP_DEBUG should be false in production';
    }
    
} catch (Throwable $e) {
    $status['checks']['config'] = ['loaded' => false, 'error' => $e->getMessage()];
    $status['errors'][] = 'Config loading failed: ' . $e->getMessage();
}

// 4. Проверка файлового хранилища данных
$dataFiles = glob(__DIR__ . '/../includes/data_*.php') ?: [];
$status['checks']['file_storage'] = [
    'files_count' => count($dataFiles),
    'readable' => true,
    'writable' => true,
];

foreach ($dataFiles as $dataFile) {
    if (!is_readable($dataFile)) {
        $status['checks']['file_storage']['readable'] = false;
        $status['errors'][] = 'Data file is not readable: ' . basename($dataFile);
    }

    if (!is_writable($dataFile)) {
        $status['checks']['file_storage']['writable'] = false;
        $status['errors'][] = 'Data file is not writable: ' . basename($dataFile);
    }
}

// 5. Проверка критических директорий
$dirs = [
    'includes' => __DIR__ . '/../includes',
    'pages' => __DIR__ . '/../pages',
    'admin' => __DIR__ . '/../admin',
    'assets' => __DIR__ . '/../assets',
    'storage/logs' => __DIR__ . '/../storage/logs',
];

foreach ($dirs as $name => $path) {
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    
    $status['checks']['directories'][$name] = [
        'exists' => $exists,
        'writable' => $writable,
    ];
    
    if (!$exists) {
        $status['errors'][] = "Directory missing: $name";
    }
    
    if ($name === 'storage/logs' && !$writable) {
        $status['errors'][] = "Directory not writable: $name";
    }
}

// 6. Проверка критических файлов
$files = [
    'router.php' => __DIR__ . '/../router.php',
    'includes/config.php' => __DIR__ . '/../includes/config.php',
    'includes/layout.php' => __DIR__ . '/../includes/layout.php',
    'pages/index.php' => __DIR__ . '/../pages/index.php',
];

foreach ($files as $name => $path) {
    $exists = file_exists($path);
    $status['checks']['files'][$name] = [
        'exists' => $exists,
        'size' => $exists ? filesize($path) : 0,
    ];
    
    if (!$exists) {
        $status['errors'][] = "File missing: $name";
    }
}

// 7. Проверка Composer
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
$status['checks']['composer'] = [
    'installed' => file_exists($autoloadPath),
];
if (!file_exists($autoloadPath)) {
    $status['warnings'][] = 'Composer dependencies not installed (run: composer install)';
}

// 8. Проверка PHP расширений
$requiredExtensions = ['mbstring', 'json', 'session', 'gd'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    $loaded = extension_loaded($ext);
    $status['checks']['extensions'][$ext] = $loaded;
    
    if (!$loaded) {
        $missingExtensions[] = $ext;
        $status['errors'][] = "PHP extension missing: $ext";
    }
}

// 9. Проверка логов
$logFile = __DIR__ . '/../storage/logs/error.log';
if (file_exists($logFile)) {
    $logSize = filesize($logFile);
    $status['checks']['logs'] = [
        'exists' => true,
        'size' => $logSize,
        'size_mb' => round($logSize / 1024 / 1024, 2),
    ];
    
    if ($logSize > 0) {
        $lines = file($logFile);
        $recentErrors = array_slice($lines, -5);
        $status['checks']['logs']['recent_errors'] = array_map('trim', $recentErrors);
    }
} else {
    $status['checks']['logs'] = ['exists' => false];
}

// 10. Проверка прав доступа
$status['checks']['permissions'] = [
    'php_user' => function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'unknown',
    'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown',
];

// Итоговый статус
if (count($status['errors']) === 0) {
    $status['status'] = count($status['warnings']) === 0 ? 'healthy' : 'warning';
} else {
    $status['status'] = 'error';
}

$status['summary'] = [
    'errors' => count($status['errors']),
    'warnings' => count($status['warnings']),
];

echo json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
