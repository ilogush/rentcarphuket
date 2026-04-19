<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

http_response_code(404);

render_layout('404 - Страница не найдена', function() {
    echo render_centered_notice(
        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-12 h-12"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>',
        '404',
        'Ой! Похоже, эта страница уехала в отпуск без предупреждения.',
        render_client_button('Вернуться на главную', '/', get_icon('home', 'w-4 h-4')) . render_client_button('Сотрудничество', '/partnership', get_icon('briefcase', 'w-4 h-4'), 'secondary'),
        'bg-red-50 text-red-500'
    );
});
