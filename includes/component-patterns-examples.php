<?php
/**
 * ПРИМЕРЫ ИСПОЛЬЗОВАНИЯ ГОТОВЫХ ПАТТЕРНОВ
 * Этот файл только для справки, не подключается автоматически
 */

// ============================================
// 1. INPUT С PREFIX/SUFFIX
// ============================================

// Цена с валютой
echo component()->input([
    'name' => 'price',
    'type' => 'number',
    'label' => 'Цена',
    'prefix' => '฿',
    'suffix' => '/день',
    'value' => '1200'
]);

// Телефон с кодом страны
echo component()->input([
    'name' => 'phone',
    'type' => 'tel',
    'label' => 'Телефон',
    'prefix' => '+66',
    'placeholder' => '812345678'
]);

// Процент
echo component()->input([
    'name' => 'discount',
    'type' => 'number',
    'label' => 'Скидка',
    'suffix' => '%',
    'value' => '10'
]);

// ============================================
// 2. SELECT С ГРУППИРОВКОЙ (OPTGROUP)
// ============================================

// Выбор автомобиля по категориям
echo component()->select([
    'name' => 'car_type',
    'label' => 'Тип автомобиля',
    'grouped' => true,
    'options' => [
        'Легковые' => [
            'sedan' => 'Седан',
            'hatchback' => 'Хэтчбек',
            'coupe' => 'Купе'
        ],
        'Внедорожники' => [
            'suv' => 'Кроссовер',
            'pickup' => 'Пикап'
        ],
        'Минивэны' => [
            'minivan' => 'Минивэн',
            'van' => 'Фургон'
        ]
    ]
]);

// Выбор локации по регионам
echo component()->select([
    'name' => 'location',
    'label' => 'Локация',
    'grouped' => true,
    'options' => [
        'Пхукет' => [
            '1' => 'Аэропорт Пхукета',
            '2' => 'Патонг',
            '3' => 'Карон'
        ],
        'Краби' => [
            '4' => 'Аэропорт Краби',
            '5' => 'Ао Нанг'
        ]
    ],
    'selected' => '2'
]);

// ============================================
// 3. ФОРМА ЛОГИНА
// ============================================

// Клиентская форма логина
echo render_login_form('/login', 'client');

// Админская форма логина
echo render_login_form('/admin/login', 'admin');

// ============================================
// 4. АДМИН-ТАБЛИЦА
// ============================================

// Таблица пользователей
echo render_admin_table([
    'headers' => ['ID', 'Email', 'Роль', 'Дата регистрации', 'Действия'],
    'columns' => [
        'id',
        'email',
        'role',
        fn($user) => date('d.m.Y', strtotime($user['created_at'])),
        fn($user) => 
            component()->button([
                'text' => 'Редактировать',
                'size' => 'xs',
                'href' => "/admin/users/edit/{$user['id']}",
                'theme' => 'admin'
            ]) . ' ' .
            component()->button([
                'text' => 'Удалить',
                'size' => 'xs',
                'variant' => 'danger',
                'theme' => 'admin',
                'attrs' => "onclick=\"openModal('delete-user-{$user['id']}-modal')\""
            ])
    ],
    'data' => $users,
    'emptyText' => 'Пользователи не найдены'
]);

// Таблица автомобилей
echo render_admin_table([
    'headers' => ['ID', 'Название', 'Цена', 'Статус', 'Действия'],
    'columns' => [
        'id',
        'name',
        fn($car) => '฿' . number_format($car['price'], 0) . '/день',
        fn($car) => component()->badge([
            'text' => $car['available'] ? 'Доступен' : 'Занят',
            'color' => $car['available'] ? 'green' : 'red'
        ]),
        fn($car) => component()->button([
            'text' => 'Редактировать',
            'size' => 'xs',
            'href' => "/admin/cars/edit/{$car['id']}",
            'theme' => 'admin'
        ])
    ],
    'data' => $cars
]);

// ============================================
// 5. МОДАЛЬНОЕ ОКНО ПОДТВЕРЖДЕНИЯ
// ============================================

// Подтверждение удаления пользователя
echo render_confirm_modal([
    'id' => 'delete-user-modal',
    'title' => 'Удалить пользователя?',
    'message' => 'Это действие нельзя отменить. Все данные пользователя будут удалены.',
    'confirmText' => 'Удалить',
    'cancelText' => 'Отмена',
    'variant' => 'danger',
    'onConfirm' => 'deleteUser(123)'
]);

// Подтверждение отмены бронирования
echo render_confirm_modal([
    'id' => 'cancel-booking-modal',
    'title' => 'Отменить бронирование?',
    'message' => 'Бронирование будет отменено, и автомобиль станет доступен для других клиентов.',
    'confirmText' => 'Отменить бронирование',
    'variant' => 'danger',
    'onConfirm' => 'cancelBooking(456)'
]);

// Подтверждение публикации
echo render_confirm_modal([
    'id' => 'publish-modal',
    'title' => 'Опубликовать изменения?',
    'message' => 'Изменения станут видны всем пользователям.',
    'confirmText' => 'Опубликовать',
    'variant' => 'success',
    'onConfirm' => 'publishChanges()'
]);

// ============================================
// 6. СЕТКА ЭЛЕМЕНТОВ
// ============================================

// Сетка автомобилей (4 колонки)
echo render_grid($cars, fn($car) => component()->carCard($car), 4);

// Сетка статистики (4 колонки)
echo render_grid($stats, function($stat) {
    return component()->stat([
        'label' => $stat['label'],
        'value' => $stat['value'],
        'icon' => $stat['icon']
    ]);
}, 4);

// Сетка информационных карточек (3 колонки)
echo render_grid($features, function($feature) {
    return render_info_card(
        $feature['icon'],
        $feature['title'],
        $feature['description']
    );
}, 3);

// ============================================
// 7. КАРТОЧКИ С ЦЕНАМИ
// ============================================

// Обычная карточка
echo render_price_card(
    800,
    'за день',
    [
        'Базовая страховка',
        'Неограниченный пробег',
        'Одно детское кресло'
    ],
    false,
    'Выбрать',
    '/booking?plan=basic'
);

// Выделенная карточка (рекомендуемая)
echo render_price_card(
    1200,
    'за день',
    [
        'Страховка по договору',
        'Неограниченный пробег',
        'GPS навигатор',
        'Детское кресло',
        'Бесплатная доставка'
    ],
    true,
    'Забронировать',
    '/booking?plan=premium'
);

// Сетка тарифов
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <?php echo render_price_card(800, 'за день', ['Базовая страховка', 'Пробег'], false); ?>
    <?php echo render_price_card(1200, 'за день', ['Страховка по договору', 'GPS'], true); ?>
    <?php echo render_price_card(1500, 'за день', ['Все включено', 'VIP'], false); ?>
</div>
<?php

// ============================================
// 8. СЕКЦИЯ СО СТАТИСТИКОЙ
// ============================================

echo render_stats_section([
    [
        'label' => 'Автомобилей',
        'value' => '150+',
        'icon' => get_icon('car', 'w-6 h-6'),
        'color' => 'blue'
    ],
    [
        'label' => 'Довольных клиентов',
        'value' => '5000+',
        'icon' => get_icon('user', 'w-6 h-6'),
        'color' => 'green'
    ],
    [
        'label' => 'Лет опыта',
        'value' => '10',
        'icon' => get_icon('calendar', 'w-6 h-6'),
        'color' => 'purple'
    ],
    [
        'label' => 'Поддержка',
        'value' => '24/7',
        'icon' => get_icon('clock', 'w-6 h-6'),
        'color' => 'orange'
    ]
], 4);

// ============================================
// 9. АДМИН-ПАНЕЛЬ
// ============================================

// Панель с кнопкой добавления
echo render_admin_panel(
    'Управление автомобилями',
    render_admin_table([
        'headers' => ['ID', 'Название', 'Цена'],
        'columns' => ['id', 'name', 'price'],
        'data' => $cars
    ]),
    'Добавить автомобиль',
    '/admin/cars/add'
);

// Панель без кнопки
echo render_admin_panel(
    'Статистика',
    render_stats_section($stats, 4)
);

// ============================================
// 10. ПУСТОЕ СОСТОЯНИЕ
// ============================================

// С кнопкой действия
echo render_empty_state(
    'Нет автомобилей',
    'Добавьте первый автомобиль в систему, чтобы начать работу',
    'Добавить автомобиль',
    '/admin/cars/add'
);

// Без кнопки
echo render_empty_state(
    'Бронирования не найдены',
    'Здесь будут отображаться все бронирования'
);

// ============================================
// 11. ФОРМА ПОИСКА АВТОМОБИЛЕЙ
// ============================================

echo render_car_search_form($locations, [
    'location' => $_GET['location'] ?? '',
    'pickup_date' => $_GET['pickup_date'] ?? '',
    'return_date' => $_GET['return_date'] ?? ''
]);

// ============================================
// 12. ИНФОРМАЦИОННЫЕ КАРТОЧКИ
// ============================================

// Карточка со страховкой
echo render_info_card(
    'shield',
    'Страховка по договору',
    'Все автомобили застрахованы от повреждений',
    'bg-green-50',
    'text-green-600'
);

// Карточка с поддержкой
echo render_info_card(
    'clock',
    'Поддержка 24/7',
    'Всегда готовы помочь по любому вопросу',
    'bg-blue-50',
    'text-blue-600'
);

// Сетка информационных карточек
?>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php echo render_info_card('shield', 'Страховка', 'Полная защита', 'bg-green-50', 'text-green-600'); ?>
    <?php echo render_info_card('star', 'Качество', 'Новые авто', 'bg-yellow-50', 'text-yellow-600'); ?>
    <?php echo render_info_card('clock', 'Поддержка', '24/7', 'bg-blue-50', 'text-blue-600'); ?>
</div>
<?php

// ============================================
// 13. КОМПОЗИЦИЯ: ФОРМА В МОДАЛЬНОМ ОКНЕ
// ============================================

echo component()->modal([
    'id' => 'add-car-modal',
    'title' => 'Добавить автомобиль',
    'size' => 'lg',
    'content' => component()->form([
        'action' => '/admin/cars/add',
        'theme' => 'admin',
        'fields' => [
            ['type' => 'text', 'name' => 'name', 'label' => 'Название', 'required' => true],
            ['type' => 'number', 'name' => 'price', 'label' => 'Цена', 'prefix' => '฿', 'suffix' => '/день', 'required' => true],
            ['type' => 'number', 'name' => 'seats', 'label' => 'Мест', 'required' => true],
            [
                'type' => 'select',
                'name' => 'transmission',
                'label' => 'Трансмиссия',
                'options' => ['manual' => 'Механика', 'automatic' => 'Автомат'],
                'required' => true
            ]
        ],
        'submitButton' => ['text' => 'Добавить', 'icon' => get_icon('plus', 'w-4 h-4')]
    ])
]);

// ============================================
// 14. КОМПОЗИЦИЯ: ТАБЛИЦА В КАРТОЧКЕ
// ============================================

echo component()->card([
    'header' => '
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold">Последние бронирования</h2>
            ' . component()->button([
                'text' => 'Все бронирования',
                'size' => 'sm',
                'variant' => 'secondary',
                'href' => '/admin/bookings'
            ]) . '
        </div>
    ',
    'content' => render_admin_table([
        'headers' => ['ID', 'Клиент', 'Автомобиль', 'Дата'],
        'columns' => ['id', 'client_name', 'car_name', 'pickup_date'],
        'data' => array_slice($bookings, 0, 5)
    ])
]);
