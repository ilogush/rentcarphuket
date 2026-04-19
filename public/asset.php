<?php
declare(strict_types=1);

$baseDir = realpath(__DIR__ . '/../assets');
if ($baseDir === false) {
    http_response_code(404);
    exit;
}

$relativePath = (string)($_GET['path'] ?? '');
$relativePath = str_replace('\\', '/', $relativePath);
$relativePath = ltrim($relativePath, '/');

if ($relativePath === '' || str_contains($relativePath, "\0")) {
    http_response_code(404);
    exit;
}

$targetPath = realpath($baseDir . DIRECTORY_SEPARATOR . $relativePath);
if ($targetPath === false || !str_starts_with($targetPath, $baseDir . DIRECTORY_SEPARATOR) && $targetPath !== $baseDir) {
    http_response_code(404);
    exit;
}

if (!is_file($targetPath) || !is_readable($targetPath)) {
    http_response_code(404);
    exit;
}

$extension = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
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
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    default => 'application/octet-stream',
};

$lastModified = filemtime($targetPath) ?: time();
$etag = '"' . sha1($relativePath . '|' . $lastModified . '|' . (filesize($targetPath) ?: 0)) . '"';
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

readfile($targetPath);
