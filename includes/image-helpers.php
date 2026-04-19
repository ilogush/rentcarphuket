<?php
declare(strict_types=1);

/**
 * Image optimization helpers
 */

/**
 * Generate responsive image srcset
 */
function responsive_image(string $imagePath, string $alt = '', string $class = '', array $sizes = [320, 640, 768, 1024, 1280]): string {
    $imageUrl = asset_image_url($imagePath);
    $placeholder = placeholder_image_url();
    
    // For now, return optimized single image with lazy loading
    // In future, can generate multiple sizes server-side
    return sprintf(
        '<img src="%s" alt="%s" class="%s" loading="lazy" decoding="async" onerror="this.onerror=null;this.src=\'%s\'">',
        htmlspecialchars($imageUrl),
        htmlspecialchars($alt),
        htmlspecialchars($class),
        htmlspecialchars($placeholder)
    );
}

/**
 * Generate picture element with webp fallback
 */
function picture_element(string $imagePath, string $alt = '', string $class = ''): string {
    $imageUrl = asset_image_url($imagePath);
    $placeholder = placeholder_image_url();
    
    // Check if webp version exists
    $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
    $webpUrl = asset_image_url($webpPath);
    
    return sprintf(
        '<picture>
            <source srcset="%s" type="image/webp">
            <img src="%s" alt="%s" class="%s" loading="lazy" decoding="async" onerror="this.onerror=null;this.src=\'%s\'">
        </picture>',
        htmlspecialchars($webpUrl),
        htmlspecialchars($imageUrl),
        htmlspecialchars($alt),
        htmlspecialchars($class),
        htmlspecialchars($placeholder)
    );
}

/**
 * Preload critical images
 */
function preload_image(string $imagePath, string $as = 'image'): string {
    $imageUrl = asset_image_url($imagePath);
    return sprintf('<link rel="preload" href="%s" as="%s">', htmlspecialchars($imageUrl), htmlspecialchars($as));
}

/**
 * Generate blur placeholder data URL
 */
function blur_placeholder(): string {
    // Tiny 1x1 transparent gif
    return 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
}
