<?php
$phone = $contactInfo['phone'] ?? '';
$email = $contactInfo['email'] ?? '';
$address = $contactInfo['address'] ?? 'Phuket, Thailand';
$telegram = $contactInfo['socialMedia']['telegram'] ?? '#';
$whatsapp = $contactInfo['socialMedia']['whatsapp'] ?? '#';
$logoSrc = function_exists('asset_url') ? asset_url('/assets/images/monkey-logo.webp') : '/assets/images/monkey-logo.webp';
?>

<footer class="mc-footer" id="contacts">
    <div class="mc-container">
        <div class="mc-footer-cta">
            <div>
                <h3>Авто к отелю за час.</h3>
                <p class="mc-footer-cta-copy">Подберем машину под маршрут, даты и район Пхукета без лишней переписки.</p>
            </div>
            <a class="mc-btn mc-btn-lg" href="/#cars-section">
                Выбрать авто
                <?php echo get_icon('arrow-right', 'w-4 h-4'); ?>
            </a>
        </div>

        <div class="mc-footer-grid">
            <div>
                <a href="/" class="mc-logo">
                    <span class="mc-logo-mark"><img src="<?php echo e($logoSrc); ?>" alt="" width="40" height="40" loading="lazy" decoding="async"></span>
                    <span>MONKEYCAR</span>
                </a>
                <p style="margin-top: 14px; max-width: 320px;">Аренда автомобилей на Пхукете с доставкой, страховкой и поддержкой каждый день.</p>
                <div class="mc-socials">
                    <a href="<?php echo e($telegram); ?>" aria-label="Telegram"><?php echo get_icon('telegram', 'w-4 h-4'); ?></a>
                    <a href="<?php echo e($whatsapp); ?>" aria-label="WhatsApp"><?php echo get_icon('whatsapp', 'w-4 h-4'); ?></a>
                </div>
            </div>

            <div>
                <h5>Навигация</h5>
                <ul>
                    <li><a href="/">Главная</a></li>
                    <li><a href="/cars">Автопарк</a></li>
                    <li><a href="/terms">Условия</a></li>
                    <li><a href="/partnership">Партнерство</a></li>
                    <li><a href="/admin/login">Войти</a></li>
                </ul>
            </div>

            <div>
                <h5>Сервис</h5>
                <ul>
                    <li><a href="/calculator">Калькулятор</a></li>
                    <li><a href="/#cars-section">Бронирование</a></li>
                    <li><a href="#contacts">Связаться</a></li>
                    <li><a href="/terms">Документы</a></li>
                </ul>
            </div>

            <div>
                <h5>Контакты</h5>
                <ul>
                    <?php if ($email): ?><li><a href="mailto:<?php echo e($email); ?>"><?php echo e($email); ?></a></li><?php endif; ?>
                    <?php if ($phone): ?><li><a href="tel:<?php echo e($phone); ?>"><?php echo e($phone); ?></a></li><?php endif; ?>
                    <li><?php echo e($address); ?></li>
                </ul>
            </div>
        </div>

        <div class="mc-footer-legal">
            <span>© <?php echo date('Y'); ?> Rent Car Phuket. Все права защищены.</span>
            <span>PHUKET · DAILY RENTAL · SUPPORT 24/7</span>
        </div>
    </div>
</footer>
