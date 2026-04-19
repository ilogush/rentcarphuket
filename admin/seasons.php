<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once 'actions/handle_seasons.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

$editing_season = null;
if (isset($_GET['edit'])) {
    foreach ($seasons as $s) {
        if ($s['id'] == $_GET['edit']) {
            $editing_season = $s;
            break;
        }
    }
}
if (isset($_GET['add'])) {
    $editing_season = ['id' => '', 'season' => '', 'start_date' => '', 'end_date' => '', 'multiplier' => '1.00', 'label' => ''];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сезонность | Rent Car Phuket</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 antialiased" style="font-family: system-ui, -apple-system, sans-serif;">
    
    <div class="flex h-screen overflow-hidden bg-gray-50">
        <?php render_sidebar('seasons'); ?>

        <div class="flex-1 overflow-y-auto">
            <?php render_topbar('Поиск по сезонам...'); ?>

            <main class="p-6">
                <div class="max-w-[1600px] mx-auto">
                    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-8">
                        <div>
                            <h1 class="text-2xl font-black text-gray-800">Сезонность</h1>
                        </div>
                        <div>
                            <?php echo render_admin_button('Добавить сезон', '/admin/seasons?add=1', 'primary', 'flex items-center gap-2', 'link', get_icon('plus', 'w-4 h-4')); ?>
                        </div>
                    </header>

                <?php echo render_toast(); ?>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'csrf'): ?>
                    <?php echo render_toast_script('Ошибка проверки безопасности. Повторите действие.', 'error'); ?>
                <?php endif; ?>
                <?php if (isset($_GET['success']) && $_GET['success'] === 'saved'): ?>
                    <?php echo render_toast_script('Сезонность обновлена.', 'success'); ?>
                <?php endif; ?>
                <?php if (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
                    <?php echo render_toast_script('Сезон удалён.', 'success'); ?>
                <?php endif; ?>

                <?php if ($editing_season): ?>
                <?php
                    $formFields = '
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            ' . render_admin_field('Название сезона', render_admin_input('season', $editing_season['season'], 'text', 'placeholder-gray-300', 'Peak Season', 'required')) . '
                            ' . render_admin_field('Начало (MM-DD)', render_admin_input('start_date', $editing_season['start_date'], 'text', 'placeholder-gray-300', '12-15', 'required')) . '
                            ' . render_admin_field('Конец (MM-DD)', render_admin_input('end_date', $editing_season['end_date'], 'text', 'placeholder-gray-300', '01-15', 'required')) . '
                            ' . render_admin_field('Коэф.', render_admin_input('multiplier', $editing_season['multiplier'], 'text', 'placeholder-gray-300', '1.50', 'required')) . '
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                            ' . render_admin_field('Ярлык (Label)', render_admin_input('label', $editing_season['label'], 'text', 'placeholder-gray-300', '+50%')) . '
                        </div>
                    ';
                    $formFooter = '
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                             ' . ($editing_season['id'] ? render_admin_danger_button('Удалить', 'onclick="openConfirmModal(\'confirm-modal\', \'Удалить сезон?\', \'Вы уверены?\', () => document.getElementById(\'delete-form\').submit())"') : '') . '
                            ' . render_admin_submit_button('Сохранить', '', 'button-text', 'loading-spinner hidden', false) . '
                        </div>
                        <form id="delete-form" method="POST" style="display:none;">
                            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                            <input type="hidden" name="delete_season" value="1">
                            <input type="hidden" name="season_id" value="' . $editing_season['id'] . '">
                        </form>
                    ';
                    $formHtml = '
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                            <input type="hidden" name="save_season" value="1">
                            <input type="hidden" name="season_id" value="' . $editing_season['id'] . '">
                            ' . $formFields . '
                        </form>
                    ';
                    echo render_admin_form_card($editing_season['id'] ? 'Редактировать' : 'Новый сезон', '/admin/seasons', $formHtml, $formFooter);
                ?>
                <?php else: ?>
                <?php ob_start(); ?>
                    <?php
                    $months = [
                        '01' => 'янв', '02' => 'фев', '03' => 'мар', '04' => 'апр',
                        '05' => 'мая', '06' => 'июн', '07' => 'июл', '08' => 'авг',
                        '09' => 'сен', '10' => 'окт', '11' => 'ноя', '12' => 'дек'
                    ];

                    $formatSeasonDate = static function ($date) use ($months) {
                        if (!$date) return '—';
                        $parts = explode('-', $date);
                        if (count($parts) !== 2) return $date;
                        return $parts[1] . ' ' . ($months[$parts[0]] ?? $parts[0]);
                    };

                    $rows = '';
                    foreach ($seasons as $s) {
                        $rows .= '<tr class="group hover:bg-blue-50/30 transition-all duration-300">'
                            . '<td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">' . $s['season'] . '</td>'
                            . '<td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">' . $formatSeasonDate($s['start_date']) . ' — ' . $formatSeasonDate($s['end_date']) . '</td>'
                            . '<td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">' . $s['multiplier'] . '</td>'
                            . '<td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">' . ($s['label'] ?: '—') . '</td>'
                            . '<td class="px-6 py-4 text-right align-middle">' . render_admin_edit_button('/admin/seasons?edit=' . $s['id']) . '</td>'
                            . '</tr>';
                    }

                    echo render_table([
                        ['label' => 'Сезон'],
                        ['label' => 'Период'],
                        ['label' => 'Коэф.'],
                        ['label' => 'Ярлык'],
                        ['label' => ''],
                    ], $rows);
                    ?>
                <?php echo ob_get_clean(); ?>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php echo render_confirm_modal(); ?>
</body>
</html>
