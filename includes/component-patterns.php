<?php
/**
 * Готовые паттерны и обертки для частого использования компонентов
 */

use function Components\component;

/**
 * Форма логина
 * 
 * @param string $action URL для отправки формы
 * @param string $theme Тема: 'client' или 'admin'
 * @return string HTML формы
 */
function render_login_form(string $action, string $theme = 'client'): string {
    return component()->form([
        'action' => $action,
        'method' => 'POST',
        'theme' => $theme,
        'fields' => [
            [
                'type' => 'email',
                'name' => 'email',
                'label' => 'Email',
                'icon' => get_icon('mail', 'w-5 h-5'),
                'required' => true,
                'placeholder' => 'your@email.com'
            ],
            [
                'type' => 'password',
                'name' => 'password',
                'label' => 'Пароль',
                'icon' => get_icon('lock', 'w-5 h-5'),
                'required' => true,
                'placeholder' => '••••••••'
            ]
        ],
        'submitButton' => [
            'text' => 'Войти',
            'theme' => $theme,
            'fullWidth' => true,
            'type' => 'submit'
        ]
    ]);
}

/**
 * Универсальная админ-таблица с данными
 * 
 * @param array $config Конфигурация таблицы
 *   - headers: array Заголовки колонок
 *   - columns: array Ключи данных или callback-функции для каждой колонки
 *   - data: array Массив данных для отображения
 *   - emptyText: string Текст при отсутствии данных (опционально)
 * @return string HTML таблицы
 */
function render_admin_table(array $config): string {
    $headers = $config['headers'] ?? [];
    $columns = $config['columns'] ?? [];
    $data = $config['data'] ?? [];
    $emptyText = $config['emptyText'] ?? 'Нет данных для отображения';
    
    if (empty($data)) {
        $rows = '<tr><td colspan="' . count($headers) . '" class="px-6 py-10 text-center text-gray-400 font-bold">' . 
                htmlspecialchars($emptyText) . '</td></tr>';
    } else {
        $rows = array_map(function($item) use ($columns) {
            $cells = '';
            foreach ($columns as $column) {
                if (is_callable($column)) {
                    $value = $column($item);
                } else {
                    $value = htmlspecialchars($item[$column] ?? '');
                }
                $cells .= '<td class="px-6 py-4">' . $value . '</td>';
            }
            return '<tr class="hover:bg-gray-50">' . $cells . '</tr>';
        }, $data);
        $rows = implode('', $rows);
    }
    
    return component()->table([
        'headers' => $headers,
        'rows' => $rows,
        'theme' => 'admin',
        'hoverable' => true
    ]);
}

/**
 * Модальное окно подтверждения действия
 * 
 * @param array $config Конфигурация модального окна
 *   - id: string ID модального окна
 *   - title: string Заголовок
 *   - message: string Текст сообщения
 *   - confirmText: string Текст кнопки подтверждения (по умолчанию "Подтвердить")
 *   - cancelText: string Текст кнопки отмены (по умолчанию "Отмена")
 *   - variant: string Вариант кнопки подтверждения: 'danger', 'primary', 'success' (по умолчанию 'danger')
 *   - onConfirm: string JavaScript код для выполнения при подтверждении
 * @return string HTML модального окна
 */
function render_confirm_modal(array $config): string {
    $id = $config['id'] ?? 'confirm-modal-' . uniqid();
    $title = $config['title'] ?? 'Подтвердите действие';
    $message = $config['message'] ?? 'Вы уверены?';
    $confirmText = $config['confirmText'] ?? 'Подтвердить';
    $cancelText = $config['cancelText'] ?? 'Отмена';
    $variant = $config['variant'] ?? 'danger';
    $onConfirm = $config['onConfirm'] ?? '';
    
    return component()->modal([
        'id' => $id,
        'title' => $title,
        'size' => 'sm',
        'content' => '<p class="text-gray-600 text-base">' . htmlspecialchars($message) . '</p>',
        'footer' => 
            '<div class="flex gap-3 justify-end">' .
            component()->button([
                'text' => $cancelText,
                'variant' => 'secondary',
                'size' => 'md',
                'attrs' => "onclick=\"closeModal('$id')\""
            ]) .
            component()->button([
                'text' => $confirmText,
                'variant' => $variant,
                'size' => 'md',
                'attrs' => "onclick=\"$onConfirm; closeModal('$id')\""
            ]) .
            '</div>'
    ]);
}

/**
 * Сетка элементов с кастомным рендерером
 * 
 * @param array $items Массив элементов для отображения
 * @param callable $renderer Функция для рендеринга каждого элемента
 * @param int $columns Количество колонок (2, 3, 4)
 * @return string HTML сетки
 */
function render_grid(array $items, callable $renderer, int $columns = 4): string {
    $colClass = match($columns) {
        2 => 'md:grid-cols-2',
        3 => 'md:grid-cols-3',
        4 => 'md:grid-cols-4',
        default => 'md:grid-cols-3'
    };
    
    $html = '<div class="grid grid-cols-1 ' . $colClass . ' gap-6">';
    foreach ($items as $item) {
        $html .= $renderer($item);
    }
    $html .= '</div>';
    
    return $html;
}

/**
 * Карточка с ценой и списком преимуществ
 * 
 * @param float $price Цена
 * @param string $period Период (например, "за день", "в месяц")
 * @param array $features Список преимуществ
 * @param bool $highlighted Выделить карточку (рекомендуемый тариф)
 * @param string $buttonText Текст кнопки
 * @param string $buttonHref Ссылка кнопки
 * @return string HTML карточки
 */
function render_price_card(
    float $price, 
    string $period, 
    array $features, 
    bool $highlighted = false,
    string $buttonText = 'Выбрать',
    string $buttonHref = '#'
): string {
    $featuresHtml = '<ul class="space-y-3 mb-6">';
    foreach ($features as $feature) {
        $featuresHtml .= '<li class="flex items-center gap-2 text-sm">' . 
                        get_icon('check', 'w-5 h-5 text-green-500 shrink-0') . 
                        '<span class="text-gray-700">' . htmlspecialchars($feature) . '</span></li>';
    }
    $featuresHtml .= '</ul>';
    
    $cardClass = $highlighted ? 'ring-2 ring-blue-600 shadow-xl' : '';
    $badge = $highlighted ? component()->badge(['text' => 'Рекомендуем', 'color' => 'blue']) : '';
    
    return component()->card([
        'content' => "
            <div class='text-center mb-6'>
                $badge
                <div class='text-4xl font-black text-gray-800 mt-4'>฿" . number_format($price, 0) . "</div>
                <div class='text-sm text-gray-500 mt-1'>$period</div>
            </div>
            $featuresHtml
            " . component()->button([
                'text' => $buttonText,
                'href' => $buttonHref,
                'fullWidth' => true,
                'variant' => $highlighted ? 'primary' : 'secondary'
            ]),
        'class' => "hover:shadow-lg transition-all $cardClass"
    ]);
}

/**
 * Секция со статистикой
 * 
 * @param array $stats Массив статистики
 *   Каждый элемент: ['label' => '...', 'value' => '...', 'icon' => '...', 'color' => '...']
 * @param int $columns Количество колонок
 * @return string HTML секции
 */
function render_stats_section(array $stats, int $columns = 4): string {
    return render_grid($stats, function($stat) {
        return component()->stat([
            'label' => $stat['label'] ?? '',
            'value' => $stat['value'] ?? '',
            'icon' => $stat['icon'] ?? null,
            'color' => $stat['color'] ?? 'blue'
        ]);
    }, $columns);
}

/**
 * Админ-панель с заголовком и кнопкой действия
 * 
 * @param string $title Заголовок панели
 * @param string $content Контент панели
 * @param string|null $addButtonText Текст кнопки добавления (null = без кнопки)
 * @param string|null $addButtonHref Ссылка кнопки добавления
 * @return string HTML панели
 */
function render_admin_panel(
    string $title, 
    string $content,
    ?string $addButtonText = null,
    ?string $addButtonHref = null
): string {
    $button = '';
    if ($addButtonText && $addButtonHref) {
        $button = component()->button([
            'text' => $addButtonText,
            'size' => 'sm',
            'href' => $addButtonHref,
            'icon' => get_icon('plus', 'w-4 h-4'),
            'theme' => 'admin'
        ]);
    }
    
    return component()->card([
        'header' => "
            <div class='flex justify-between items-center'>
                <h2 class='text-xl font-bold text-gray-800'>$title</h2>
                $button
            </div>
        ",
        'content' => $content,
        'class' => 'mb-6'
    ]);
}

/**
 * Пустое состояние (Empty State)
 * 
 * @param string $title Заголовок
 * @param string $description Описание
 * @param string|null $actionText Текст кнопки действия (null = без кнопки)
 * @param string|null $actionHref Ссылка кнопки действия
 * @return string HTML пустого состояния
 */
function render_empty_state(
    string $title, 
    string $description, 
    ?string $actionText = null, 
    ?string $actionHref = null
): string {
    $actionButton = '';
    if ($actionText && $actionHref) {
        $actionButton = component()->button([
            'text' => $actionText,
            'href' => $actionHref,
            'variant' => 'primary'
        ]);
    }
    
    return "
        <div class='text-center py-16'>
            <div class='text-gray-300 mb-6'>" . get_icon('inbox', 'w-20 h-20 mx-auto') . "</div>
            <h3 class='text-2xl font-bold text-gray-800 mb-3'>$title</h3>
            <p class='text-gray-600 mb-8 max-w-md mx-auto'>$description</p>
            $actionButton
        </div>
    ";
}

/**
 * Форма поиска автомобилей
 * 
 * @param array $locations Массив локаций
 * @param array $values Текущие значения формы (для сохранения состояния)
 * @return string HTML формы поиска
 */
function render_car_search_form(array $locations = [], array $values = []): string {
    $locationOptions = [];
    foreach ($locations as $location) {
        $locationOptions[$location['id']] = $location['name'];
    }
    
    return component()->form([
        'action' => '/cars',
        'method' => 'GET',
        'theme' => 'inline',
        'class' => 'bg-white rounded-3xl shadow-lg p-6',
        'fields' => [
            [
                'type' => 'select',
                'name' => 'location',
                'label' => 'Локация',
                'options' => $locationOptions,
                'selected' => $values['location'] ?? '',
                'required' => true
            ],
            [
                'type' => 'date',
                'name' => 'pickup_date',
                'label' => 'Дата получения',
                'value' => $values['pickup_date'] ?? '',
                'required' => true
            ],
            [
                'type' => 'date',
                'name' => 'return_date',
                'label' => 'Дата возврата',
                'value' => $values['return_date'] ?? '',
                'required' => true
            ]
        ],
        'submitButton' => [
            'text' => 'Найти автомобиль',
            'icon' => get_icon('search', 'w-5 h-5'),
            'variant' => 'primary'
        ]
    ]);
}

/**
 * Информационная карточка с иконкой
 * 
 * @param string $icon Название иконки
 * @param string $title Заголовок
 * @param string $description Описание
 * @param string $iconBgClass CSS класс для фона иконки
 * @param string $iconTextClass CSS класс для цвета иконки
 * @return string HTML карточки
 */
function render_info_card(
    string $icon,
    string $title,
    string $description,
    string $iconBgClass = 'bg-blue-50',
    string $iconTextClass = 'text-blue-600'
): string {
    return component()->card([
        'content' => "
            <div class='flex items-start gap-4'>
                <div class='$iconBgClass $iconTextClass p-3 rounded-2xl shrink-0'>
                    " . get_icon($icon, 'w-6 h-6') . "
                </div>
                <div>
                    <h3 class='font-bold text-lg text-gray-800 mb-1'>$title</h3>
                    <p class='text-gray-600 text-sm'>$description</p>
                </div>
            </div>
        ",
        'class' => 'hover:shadow-md transition-shadow'
    ]);
}
