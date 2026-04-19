<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$phone = $contactInfo['phone'] ?? '';
$navItems = [
    '/' => 'Главная',
    '/cars' => 'Автопарк',
    '/terms' => 'Условия',
    '/partnership' => 'Партнерство',
];

require_once __DIR__ . '/auth_client.php';
$isClientLoggedIn = is_client_logged_in();
$accountHref = $isClientLoggedIn ? '/profile' : '#contacts';
$accountLabel = $isClientLoggedIn ? 'Мои брони' : 'Контакты';
$logoSrc = function_exists('asset_url') ? asset_url('/assets/images/monkey-logo.webp') : '/assets/images/monkey-logo.webp';
?>

<header class="mc-header">
    <div class="mc-container mc-header-inner">
        <a href="/" class="mc-logo" aria-label="MonkeyCar Phuket">
            <span class="mc-logo-mark"><img src="<?php echo e($logoSrc); ?>" alt="" width="40" height="40" decoding="async"></span>
            <span>MONKEYCAR</span>
            <span class="mc-logo-city">PHUKET · TH</span>
        </a>

        <nav class="mc-nav" aria-label="Главная навигация">
            <?php foreach ($navItems as $href => $label): ?>
                <?php $isActive = $href !== '#contacts' && rtrim($currentPath, '/') === rtrim($href, '/'); ?>
                <a href="<?php echo e($href); ?>" class="<?php echo $isActive ? 'is-active' : ''; ?>"><?php echo e($label); ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="mc-header-right">
            <?php if ($phone): ?>
                <a href="tel:<?php echo e($phone); ?>" class="mc-phone" aria-label="Позвонить">
                    <?php echo get_icon('phone', 'w-4 h-4'); ?>
                    <b><?php echo e($phone); ?></b>
                </a>
            <?php endif; ?>
            <button id="burger-toggle" class="mc-header-burger" type="button" aria-label="Открыть меню" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M10 17h10" />
                </svg>
            </button>
        </div>

        <div id="burger-menu" class="mc-mobile-menu hidden">
            <?php foreach ($navItems as $href => $label): ?>
                <?php $isActive = $href !== '#contacts' && rtrim($currentPath, '/') === rtrim($href, '/'); ?>
                <a href="<?php echo e($href); ?>" class="<?php echo $isActive ? 'is-active' : ''; ?>"><?php echo e($label); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo e($accountHref); ?>"><?php echo e($accountLabel); ?></a>
            <?php if ($isClientLoggedIn): ?>
                <a href="/logout">Выйти</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.querySelector('#burger-toggle');
        const menu = document.querySelector('#burger-menu');

        if (!toggle || !menu) return;

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = menu.classList.toggle('hidden');
            toggle.setAttribute('aria-expanded', String(!isHidden));
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                menu.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    });
</script>
