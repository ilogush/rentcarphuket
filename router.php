<?php
declare(strict_types=1);

define('APP_ROOT', __DIR__);

// Autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Config (includes session start)
require_once __DIR__ . '/includes/config.php';

// Logger (only if needed)
$shouldLog = env_value('APP_DEBUG', 'false') === 'true' || env_value('ENABLE_ACCESS_LOG', 'false') === 'true';
if ($shouldLog && file_exists(__DIR__ . '/includes/logger.php')) {
    require_once __DIR__ . '/includes/logger.php';
    logger()->logRequest();
}

// Admin auth
require_once __DIR__ . '/admin/auth.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function serve_project_file(string $path): void
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if ($extension === 'php') {
        require $path;
        exit;
    }

    $contentType = match ($extension) {
        'css' => 'text/css; charset=UTF-8',
        'js' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'webmanifest' => 'application/manifest+json',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        default => 'application/octet-stream',
    };

    $lastModified = filemtime($path) ?: time();
    $etag = '"' . sha1($path . '|' . $lastModified . '|' . (filesize($path) ?: 0)) . '"';
    $ifNoneMatch = trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? ''));
    $ifModifiedSince = strtotime((string)($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '')) ?: 0;

    header('Content-Type: ' . $contentType);
    header('Cache-Control: public, max-age=31536000, immutable');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
    header('ETag: ' . $etag);
    header('X-Content-Type-Options: nosniff');

    if ($ifNoneMatch === $etag || $ifModifiedSince >= $lastModified) {
        http_response_code(304);
        exit;
    }

    readfile($path);
    exit;
}

function is_public_project_uri(string $uri): bool
{
    if (preg_match('#^/(assets|api)/#', $uri)) {
        return true;
    }

    return in_array($uri, [
        '/manifest.json',
        '/robots.txt',
        '/favicon.ico',
    ], true);
}

// Admin auth check (optimized)
if (str_starts_with($uri, '/admin') && $uri !== '/admin/login') {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: /admin/login', true, 302);
        exit;
    }
}

// Route map for faster lookup
$routes = [
    '/' => '/pages/index.php',
    '/sitemap.xml' => '/sitemap.php',
    '/booking' => '/pages/booking.php',
    '/success' => '/pages/success.php',
    '/download-pdf' => '/pages/download_pdf.php',
    '/profile' => '/pages/profile.php',
    '/admin' => '/admin/index.php',
    '/admin/bookings' => '/admin/bookings.php',
    '/admin/users' => '/admin/users.php',
    '/admin/locations' => '/admin/locations.php',
    '/admin/discounts' => '/admin/discounts.php',
    '/admin/durations' => '/admin/durations.php',
    '/admin/seasons' => '/admin/seasons.php',
    '/admin/profile' => '/admin/profile.php',
    '/admin/login' => '/admin/login.php',
];

// Exact match (fastest)
if (isset($routes[$uri])) {
    require APP_ROOT . $routes[$uri];
    exit;
}

// Logout action
if ($uri === '/logout') {
    require_once APP_ROOT . '/includes/auth_client.php';
    client_logout();
    exit;
}

// Dynamic routes (regex - slower)
if (preg_match('#^/car/(\d+)$#', $uri, $m)) {
    $_GET['id'] = $m[1];
    require APP_ROOT . '/pages/car.php';
    exit;
}

// Static file passthrough. Serve files directly so assets outside /public
// still work when the dev server is started with "-t public".
if ($uri !== '/' && is_public_project_uri($uri) && is_file(__DIR__ . $uri)) {
    serve_project_file(__DIR__ . $uri);
}

$publicFilePath = __DIR__ . '/public' . preg_replace('#^/public#', '', $uri);
if ($uri !== '/' && is_file($publicFilePath)) {
    serve_project_file($publicFilePath);
}

// Fallback: check pages directory
$pagePath = __DIR__ . '/pages/' . trim($uri, '/') . '.php';
if (is_file($pagePath)) {
    require $pagePath;
    exit;
}

// 404
if ($shouldLog && function_exists('log_warning')) {
    log_warning('404 Not Found', ['uri' => $uri, 'method' => $_SERVER['REQUEST_METHOD']]);
}
http_response_code(404);
require APP_ROOT . '/pages/404.php';
