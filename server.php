<?php
declare(strict_types=1);

/**
 * Router for PHP's built-in development server.
 *
 * Run from the project root:
 *   php -S localhost:8000 server.php
 *
 * This keeps page routing and static assets working without Apache/Nginx rewrite rules.
 */

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

function serve_dev_file(string $path): bool
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if ($extension === 'php') {
        require $path;
        return true;
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
        return true;
    }

    readfile($path);
    return true;
}

if ($uri !== '/' && $uri !== '') {
    $rootPath = __DIR__ . $uri;
    if (is_file($rootPath)) {
        return serve_dev_file($rootPath);
    }

    $publicPath = __DIR__ . '/public' . $uri;
    if (is_file($publicPath)) {
        return serve_dev_file($publicPath);
    }
}

require __DIR__ . '/index.php';
