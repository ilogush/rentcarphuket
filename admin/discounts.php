<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once 'actions/handle_discounts.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

$editing_discount = null;
if (isset($_GET['edit'])) {
    foreach ($discounts as $d) {
        if ($d['id'] == $_GET['edit']) {
            $editing_discount = $d;
            break;
        }
    }
}
if (isset($_GET['add'])) {
    $editing_discount = ['id' => '', 'code' => '', 'amount' => '', 'type' => 'percent'];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Скидки | Rent Car Phuket</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 antialiased" style="font-family: system-ui, -apple-system, sans-serif;">
    
    <div class="flex h-screen overflow-hidden bg-gray-50">
        <?php render_sidebar('discounts'); ?>

        <div class="flex-1 overflow-y-auto">
            <?php render_topbar('Поиск промокодов...'); ?>

            <main class="p-6">
            <div class="max-w-[1400px] mx-auto">
                    <?php echo render_admin_page_header('Скидки', render_admin_button('Добавить промокод', '/admin/discounts?add=1', 'primary', 'flex items-center gap-2', 'link', get_icon('plus', 'w-4 h-4'))); ?>

                <?php echo render_admin_toast_messages([
                    'error' => ['csrf' => ['message' => 'Ошибка проверки безопасности. Повторите действие.', 'type' => 'error']],
                    'success' => [
                        'saved' => ['message' => 'Промокод обновлён и готов к использованию.', 'type' => 'success'],
                        'deleted' => ['message' => 'Промокод удалён.', 'type' => 'success'],
                    ],
                ]); ?>

                <?php if ($editing_discount): ?>
                <?php
                    $formFields = '
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            ' . render_admin_field('Код (ПРОМОКОД)', render_admin_input('code', $editing_discount['code'], 'text', 'uppercase placeholder-gray-300', 'HAPPY2026', 'required')) . '
                            ' . render_admin_field('Тип скидки', render_admin_select('type', ['percent' => 'Процент (%)', 'fixed' => 'Фиксированная (฿)'], $editing_discount['type'])) . '
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
                            ' . render_admin_field('Размер скидки', render_admin_input('amount', $editing_discount['amount'], 'number', '', '', 'required')) . '
                        </div>
                    ';
                    $formFooter = '
                        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                            ' . ($editing_discount['id'] ? render_admin_danger_button('Удалить', 'onclick="if(confirm(\'Удалить эту скидку?\')) { document.getElementById(\'delete-form\').submit(); }"') : '') . '
                            ' . render_admin_submit_button('Сохранить', '', 'button-text', 'loading-spinner hidden', false) . '
                        </div>
                        <form id="delete-form" method="POST" style="display:none;">
                            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                            <input type="hidden" name="delete_discount" value="1">
                            <input type="hidden" name="discount_id" value="' . $editing_discount['id'] . '">
                        </form>
                    ';
                    $formHtml = '
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                            <input type="hidden" name="save_discount" value="1">
                            <input type="hidden" name="discount_id" value="' . $editing_discount['id'] . '">
                            ' . $formFields . '
                        </form>
                    ';
                    echo render_admin_form_card($editing_discount['id'] ? 'Редактировать' : 'Новый промокод', '/admin/discounts', $formHtml, $formFooter);
                ?>
                <?php else: ?>
                    <?php 
                    ob_start();
                    foreach ($discounts as $d) {
                        ?>
                        <tr class="group hover:bg-blue-50/30 transition-all duration-300">
                            <td class="px-6 py-4 font-bold text-gray-800 text-sm uppercase tracking-widest align-middle"><?php echo htmlspecialchars($d['code']); ?></td>
                            <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo htmlspecialchars($d['amount']); ?><?php echo $d['type'] == 'percent' ? '%' : '฿'; ?></td>
                            <td class="px-6 py-4 text-right align-middle">
                                <?php echo render_admin_edit_button('/admin/discounts?edit=' . $d['id']); ?>
                            </td>
                        </tr>
                        <?php
                    }
                    $rowsHtml = ob_get_clean();

                    echo render_admin_table(
                        ['Промокод', 'Размер', ''],
                        $rowsHtml
                    );
                    ?>
                <?php endif; ?>
            </div>
            </main>
        </div>
    </div>
</body>
</html>
