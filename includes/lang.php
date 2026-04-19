<?php
declare(strict_types=1);
$supported_langs = ['ru', 'en', 'th', 'zh', 'de', 'es'];
$lang = 'ru'; // Default language

// Check lang from URL (set via .htaccess)
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported_langs)) {
    $lang = $_GET['lang'];
}

// Function to generate clean URL
function l($path) {
    global $lang;
    $path = trim($path, '/');
    // Remove .php extension for clean URLs
    $path = str_replace('.php', '', $path);
    
    // Ensure absolute path from root
    if ($path === 'index' || $path === '') {
        return "/";
    }
    return "/$path";
}
?>
