<?php
declare(strict_types=1);
$carTypes = ['Внедорожник', 'Кроссовер', 'Седан', 'Хэтчбек', 'Минивэн', 'Пикап'];
$fuels = ['Бензин', 'Дизель', 'Гибрид', 'Электро'];

$mainFields = '
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        ' . render_admin_field('Название модели', render_admin_input('name', $editing_car['name'], 'text', 'placeholder-gray-300', '', 'required')) . '
        ' . render_admin_field('Тип кузова', render_admin_select('type', array_combine($carTypes, $carTypes), $editing_car['type'])) . '
        ' . render_admin_field('Цена (฿/день)', render_admin_money_input('price', $editing_car['price'])) . '
        ' . render_admin_field('Депозит (฿)', render_admin_money_input('deposit', $editing_car['deposit'] ?? '')) . '
        ' . render_admin_field('Год выпуска', render_admin_input('year', $editing_car['year'], 'number', '', '', 'required')) . '
        ' . render_admin_field('Двигатель (л.)', render_admin_input('engine', $editing_car['engine'], 'text', '', '', 'required')) . '
        ' . render_admin_field('КПП', render_admin_select('transmission', ['АКПП' => 'Автоматическая', 'МКПП' => 'Механическая'], $editing_car['transmission'])) . '
        ' . render_admin_field('Топливо', render_admin_select('fuel', array_combine($fuels, $fuels), $editing_car['fuel'])) . '
        ' . render_admin_field('Количество мест', render_admin_input('seats', $editing_car['seats'], 'number', '', '', 'required')) . '
        ' . render_admin_field('Размер скидки (%)', render_admin_money_input('discount', $editing_car['discount'] ?? '', false, '0')) . '
        ' . render_admin_field('Начало скидки', render_admin_readonly_date_input('discount_start', $editing_car['discount_start'] ?? '', 'дд.мм.гггг', 'discount_start')) . '
        ' . render_admin_field('Конец скидки', render_admin_readonly_date_input('discount_end', $editing_car['discount_end'] ?? '', 'дд.мм.гггг', 'discount_end')) . '
    </div>
';

$statusField = render_admin_status_block('Статус автомобиля', 'Активное авто в каталоге', ($editing_car['status'] ?? 'active') === 'active', !empty($editing_car['id']) ? "toggleCarStatus('{$editing_car['id']}', this.checked)" : '');

$imageField = render_admin_image_picker($editing_car['image']);

$footer = '
    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
        ' . ($editing_car['id'] ? render_admin_danger_button('Удалить', 'onclick="openConfirmModal(\'confirm-modal\', \'Удалить авто?\', \'Вы уверены? Это действие необратимо.\', () => document.getElementById(\'delete-form\').submit())"') : '') . '
        ' . render_admin_submit_button('Сохранить', 'id="save-button"', 'button-text', 'loading-spinner hidden', true) . '
    </div>
    <form id="delete-form" method="POST" style="display:none;">
        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
        <input type="hidden" name="delete_car" value="1">
        <input type="hidden" name="car_id" value="' . $editing_car['id'] . '">
    </form>
';

$content = '
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
        <input type="hidden" name="save_car" value="1">
        <input type="hidden" name="car_id" value="' . $editing_car['id'] . '">
        <input type="hidden" name="existing_image" value="' . $editing_car['image'] . '">
        ' . $mainFields . '

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            ' . render_admin_field('Статус автомобиля', $statusField) . '
            ' . render_admin_field('Фотография', $imageField) . '
        </div>
        ' . $footer . '
    </form>
';

echo render_admin_form_card($editing_car['id'] ? 'Редактировать' : 'Новое авто', '/admin', $content);
