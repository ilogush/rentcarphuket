<?php
declare(strict_types=1);

/**
 * Dynamic Sitemap Generator
 * Generates sitemap.xml with all public pages and car detail pages.
 */
header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/includes/config.php';

$carRepo = new \App\Repositories\CarRepository();
$cars = $carRepo->getAll();

$domain = app_base_url();
$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static pages -->
    <url>
        <loc><?php echo $domain; ?>/</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo $domain; ?>/cars</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $domain; ?>/terms</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc><?php echo $domain; ?>/partnership</loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Car detail pages -->
<?php
$activeCars = array_filter($cars, function($c) { return ($c['status'] ?? 'active') === 'active'; });
foreach ($activeCars as $car):
?>
    <url>
        <loc><?php echo $domain; ?>/car/<?php echo $car['id']; ?></loc>
        <lastmod><?php echo $today; ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
</urlset>
