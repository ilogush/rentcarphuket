<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once 'actions/handle_durations.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

$editing_duration = null;
if (isset($_GET['edit'])) {
    foreach ($durations as $d) {
        if ($d['id'] == $_GET['edit']) { $editing_duration = $d; break; }
    }
}
if (isset($_GET['add'])) {
    $editing_duration = ['id' => '', 'range' => '', 'min_days' => '', 'max_days' => '', 'rate' => '1.00', 'label' => ''];
}

if ($editing_duration) {
    $formFields = '
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            ' . render_admin_field('Название (Range)', render_admin_input('range', $editing_duration['range'], 'text', 'placeholder-gray-300', 'Weekly', 'required')) . '
            ' . render_admin_field('Мин. дней', render_admin_input('min_days', $editing_duration['min_days'], 'number', '', '', 'required')) . '
            ' . render_admin_field('Макс. дней', render_admin_input('max_days', $editing_duration['max_days'], 'number', '', '', 'required')) . '
            ' . render_admin_field('Коэф. (Rate)', render_admin_input('rate', $editing_duration['rate'], 'text', 'placeholder-gray-300', '0.90', 'required')) . '
            ' . render_admin_field('Ярлык (Label)', render_admin_input('label', $editing_duration['label'], 'text', 'placeholder-gray-300', '-10% off')) . '
        </div>
    ';
    
    $deleteHtml = $editing_duration['id'] ? render_admin_danger_button('Удалить', 'onclick="confirmDelete(\'' . $editing_duration['id'] . '\', this)"') : '';
    $formFooter = render_admin_form_actions('save-duration-button', $deleteHtml);
    $formHtml = '
        <form id="duration-form" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
            <input type="hidden" name="save_duration" value="1">
            <input type="hidden" name="duration_id" value="' . $editing_duration['id'] . '">
            ' . $formFields . '
        </form>
        <form id="delete-form-' . $editing_duration['id'] . '" method="POST" class="delete-form" data-id="' . $editing_duration['id'] . '" style="display:none;">
            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
            <input type="hidden" name="delete_duration" value="1">
            <input type="hidden" name="duration_id" value="' . $editing_duration['id'] . '">
        </form>
    ';
    $content = render_admin_form_card($editing_duration['id'] ? 'Редактировать' : 'Новый период', '/admin/durations', $formHtml, $formFooter);
    $scripts = render_admin_form_scripts('duration-form', 'save-duration-button', '/admin/durations?success=saved') . render_admin_delete_script('duration');
} else {
    ob_start();
    foreach ($durations as $d) {
        ?>
        <tr class="group hover:bg-blue-50/30 transition-all duration-300">
            <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo htmlspecialchars($d['range']); ?></td>
            <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo (int)$d['min_days']; ?> — <?php echo (int)$d['max_days']; ?></td>
            <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo htmlspecialchars($d['rate']); ?></td>
            <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo htmlspecialchars($d['label'] ?: '—'); ?></td>
            <td class="px-6 py-4 text-right align-middle">
                <?php echo render_admin_edit_button('/admin/durations?edit=' . $d['id']); ?>
            </td>
        </tr>
        <?php
    }
    $rowsHtml = ob_get_clean();
    $content = render_admin_table(['Название', 'Дни', 'Коэф.', 'Ярлык', ''], $rowsHtml);
    $scripts = '';
}

render_admin_page(
    'durations',
    'Длительность аренды',
    'Длительность аренды',
    render_admin_button('Добавить период', '/admin/durations?add=1', 'primary', 'flex items-center gap-2', 'link', get_icon('plus', 'w-4 h-4')),
    $content,
    $scripts
);
