<?php
declare(strict_types=1);
/**
 * Main Layout Template for Rent Car Phuket
 */

// Global inclusions to ensure availability
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/components.php';
// Initialize Repositories
$carRepo = new \App\Repositories\CarRepository();
$locationRepo = new \App\Repositories\LocationRepository();
$siteRepo = new \App\Repositories\SiteRepository();

$contactInfo = $siteRepo->getContactInfo();
$siteSettings = $siteRepo->getSettings();

// Get active cars for the frontend
$cars = array_filter($carRepo->getAll(), function($car) {
    return ($car['status'] ?? 'active') === 'active';
});

// Get locations for the search box
$locations = $locationRepo->getAll();

/**
 * Render the full HTML page layout.
 *
 * @param string $pageTitle   Page title (used in <title> and OG tags)
 * @param callable|string $contentCallback  Content render callback or HTML string
 * @param array $data         Additional data (supports 'meta_description', 'og_image', 'canonical')
 */
function render_layout($pageTitle, $contentCallback, $data = []) {
    // Shared data
    global $cars, $contactInfo, $locations;
    
    $siteName = 'Rent Car Phuket';
    $domain = app_base_url();
    $metaDesc = $data['meta_description'] ?? 'Аренда автомобилей на Пхукете. Новые авто 2023–2025, бесплатная доставка в аэропорт, залог указан в карточке авто.';
    $ogImage = $data['og_image'] ?? $domain . '/assets/images/bg-hero.webp';
    $canonical = $data['canonical'] ?? $domain . ($_SERVER['REQUEST_URI'] === '/' ? '' : parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $tailwindCss = asset_url('/assets/css/tailwind.min.css');
    $stylesCss = asset_url('/assets/css/styles.min.css');
    $toastCss = asset_url('/assets/css/toast.min.css');
    $monkeyThemeCss = asset_url('/assets/css/monkey-theme.css');
    $calculatorJs = asset_url('/assets/js/calculator.min.js');

    $data['cars'] = $cars;
    $data['locations'] = $locations;
    extract($data, EXTR_SKIP);
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?> | <?php echo $siteName; ?></title>
        <meta name="description" content="<?php echo htmlspecialchars($metaDesc); ?>">
        <link rel="canonical" href="<?php echo htmlspecialchars($canonical); ?>">

        <!-- Open Graph -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="<?php echo $siteName; ?>">
        <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?> | <?php echo $siteName; ?>">
        <meta property="og:description" content="<?php echo htmlspecialchars($metaDesc); ?>">
        <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
        <meta property="og:url" content="<?php echo htmlspecialchars($canonical); ?>">
        <meta property="og:locale" content="ru_RU">

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        
        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0a0a0a">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="<?php echo $siteName; ?>">
        
        <link rel="preload" href="<?php echo htmlspecialchars($tailwindCss); ?>" as="style">
        <link rel="preload" href="<?php echo htmlspecialchars($stylesCss); ?>" as="style">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
        <link rel="preload" href="<?php echo htmlspecialchars($calculatorJs); ?>" as="script">

        <!-- Site Styles -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo htmlspecialchars($tailwindCss); ?>">
        <link rel="stylesheet" href="<?php echo htmlspecialchars($stylesCss); ?>">
        <link rel="stylesheet" href="<?php echo htmlspecialchars($toastCss); ?>">
        <link rel="stylesheet" href="<?php echo htmlspecialchars($monkeyThemeCss); ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ru.js" defer></script>
        <script src="<?php echo htmlspecialchars($calculatorJs); ?>" defer></script>

        <!-- JSON-LD Organization -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "AutoRental",
            "name": "<?php echo $siteName; ?>",
            "url": "<?php echo $domain; ?>",
            "telephone": "<?php echo $contactInfo['phone']; ?>",
            "email": "<?php echo $contactInfo['email']; ?>",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Phuket",
                "addressCountry": "TH"
            },
            "openingHours": "Mo-Su <?php echo $contactInfo['workingHours']['start']; ?>-<?php echo $contactInfo['workingHours']['end']; ?>",
            "sameAs": [
                "<?php echo $contactInfo['socialMedia']['instagram']; ?>",
                "<?php echo $contactInfo['socialMedia']['facebook']; ?>"
            ]
        }
        </script>
    </head>
    <body class="mc-page">
        <?php include_once __DIR__ . '/header.php'; ?>

        <main>
            <?php 
                if (is_callable($contentCallback)) {
                    $contentCallback($data);
                } else {
                    echo $contentCallback;
                }
            ?>
        </main>

        <?php include_once __DIR__ . '/footer.php'; ?>
        <?php echo render_public_contact_bar($contactInfo); ?>
        <?php echo render_toast(); ?>
        
        <!-- Service Worker Registration -->
        <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                if (!window.location.pathname.startsWith('/admin')) {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('SW registered'))
                        .catch(err => console.log('SW registration failed'));
                }
            });
        }
        </script>
    </body>
    </html>
    <?php
}
