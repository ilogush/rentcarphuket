<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once 'actions/handle_locations.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

$editing_loc = null;
if (isset($_GET['edit'])) {
    foreach ($locations as $loc) { if ($loc['id'] == $_GET['edit']) { $editing_loc = $loc; break; } }
}
if (isset($_GET['add'])) { $editing_loc = ['id' => '', 'name' => '', 'delivery_price' => 0]; }

$content = '';
$scripts = '';

if ($editing_loc) {
    $fields = render_admin_field('Название локации', render_admin_input('name', $editing_loc['name'], 'text', 'placeholder-gray-300', '', 'required')) .
             render_admin_field('Стоимость доставки (฿)', render_admin_input('delivery_price', (string)($editing_loc['delivery_price'] ?? 0), 'number', '', '', 'required'));
    
    $deleteHtml = $editing_loc['id'] ? render_admin_danger_button('Удалить', 'onclick="confirmDelete(\'' . $editing_loc['id'] . '\', this)"') : '';
    $formFooter = render_admin_form_actions('save-location-button', $deleteHtml);
    $formHtml = '
        <form id="location-form" method="POST" class="space-y-6 text-left">
            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
            <input type="hidden" name="save_location" value="1">
            <input type="hidden" name="location_id" value="' . $editing_loc['id'] . '">
            ' . render_admin_form_grid($fields) . '
        </form>
        <form id="delete-form-' . $editing_loc['id'] . '" method="POST" class="delete-form" data-id="' . $editing_loc['id'] . '" style="display:none;">
            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
            <input type="hidden" name="delete_location" value="1">
            <input type="hidden" name="location_id" value="' . $editing_loc['id'] . '">
        </form>
    ';
    $content = render_admin_form_card($editing_loc['id'] ? 'Редактировать' : 'Новая локация', '/admin/locations', $formHtml, $formFooter);
    $scripts = render_admin_form_scripts('location-form', 'save-location-button', '/admin/locations?success=saved') . render_admin_delete_script('location');
} else {
    $content = render_admin_data_table(
        ['Название', 'Цена', ''],
        $locations,
        function($loc) {
            return '<tr class="group hover:bg-blue-50/30 transition-all duration-300">'
                . '<td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">' . e($loc['name']) . '</td>'
                . '<td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">฿' . (int)($loc['delivery_price'] ?? 0) . '</td>'
                . '<td class="px-6 py-4 text-right align-middle">' . render_admin_edit_button('/admin/locations?edit=' . $loc['id']) . '</td>'
                . '</tr>';
        }
    );
}

render_admin_page(
    'locations', 
    'Локации', 
    'Локации', 
    render_admin_button('Добавить локацию', '/admin/locations?add=1', 'primary', 'flex items-center gap-2', 'link', get_icon('plus', 'w-4 h-4')),
    $content,
    $scripts
);
