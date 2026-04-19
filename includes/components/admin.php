<?php
declare(strict_types=1);


/**
 * Standardizes the 4-column grid layout for admin forms
 */
function render_admin_form_grid(string $contentHtml) {
    return '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">' . $contentHtml . '</div>';
}

/**
 * Standardizes the spacing for admin form sections
 */

/**
 * Standardizes the spacing for admin form sections
 */
function render_admin_form_body(string $contentHtml) {
    return '<div class="space-y-10">' . $contentHtml . '</div>';
}

/**
 * Renders a standardized data table with schema
 */

/**
 * Renders a standardized data table with schema
 */
function render_admin_data_table(array $headers, array $data, callable $rowRenderer) {
    ob_start();
    foreach ($data as $item) {
        echo $rowRenderer($item);
    }
    $rowsHtml = ob_get_clean();
    
    return render_admin_table($headers, $rowsHtml);
}

/**
 * Renders a compact admin edit button
 */

/**
 * Renders a compact admin edit button
 */
function render_admin_edit_button($link, $text = 'Изменить') {
    return render_admin_button($text, $link, 'primary');
}

/**
 * Renders a compact admin delete button for tables
 */

/**
 * Renders a compact admin delete button for tables
 */
function render_admin_table_delete_button($id, $actionTitle = 'Заявку') {
    return render_admin_button('Удалить', 'javascript:void(0)', 'secondary', 'text-red-500 border-red-100 hover:bg-red-50 hover:text-red-600', 'button', '', 'onclick="confirmDelete(\'' . $id . '\', this)"');
}

/**
 * Renders a compact admin danger button
 */

/**
 * Renders a compact admin danger button
 */
function render_admin_danger_button($text = 'Удалить', $attrs = '') {
    return render_admin_button($text, '#', 'secondary', 'text-red-500 border-red-100 hover:bg-red-50 hover:text-red-600', 'button', '', $attrs);
}

/**
 * Renders a compact admin submit button with optional loader markup
 */

/**
 * Renders a compact admin submit button with optional loader markup
 */
function render_admin_submit_button($text = 'Сохранить', $attrs = '', $textClass = 'button-text', $loaderClass = 'loading-spinner hidden', $loading = true) {
    $loaderHtml = $loading ? '<div class="' . $loaderClass . '">' . get_icon('clock', 'w-4 h-4 animate-spin') . '</div>' : '';
    return '<button type="submit"' . ($attrs ? ' ' . $attrs : '') . ' class="inline-flex items-center justify-center gap-3 bg-yellow-400 text-gray-800 px-8 py-4 rounded-2xl font-black transition-all active:scale-95 text-[10px] uppercase tracking-widest">' . '<span class="' . $textClass . '">' . $text . '</span>' . $loaderHtml . '</button>';
}

/**
 * Renders a compact admin file picker label
 */

/**
 * Renders a compact admin file picker label
 */
function render_admin_file_button($text = 'Выбрать файл', $inputName = 'image', $onchange = 'previewImage(this)') {
    return '<label class="inline-flex items-center justify-center bg-gray-800 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-700 transition-all active:scale-95 cursor-pointer text-center">' . $text . '<input type="file" name="' . $inputName . '" onchange="' . $onchange . '" class="hidden"></label>';
}

/**
 * Renders a small admin section header with icon
 */

/**
 * Renders a small admin section header with icon
 */
function render_admin_section_header($icon, $title, $iconBgClass, $iconTextClass) {
    ob_start();
    ?>
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-yellow-400 text-gray-800 flex items-center justify-center">
            <?php echo get_icon($icon, 'w-5 h-5'); ?>
        </div>
        <h2 class="text-lg font-black text-gray-800 uppercase tracking-tight"><?php echo $title; ?></h2>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a reusable admin form card shell (without header)
 */

/**
 * Renders a reusable admin form card shell (without header)
 */
function render_admin_form_card($title, $closeUrl, string $contentHtml, string $footerHtml = '') {
    ob_start();
    ?>
    <div class="rounded-3xl border border-gray-200 overflow-hidden w-full">
        <div class="p-4">
            <?php echo $contentHtml; ?>
            <?php if ($footerHtml !== ''): ?>
                <?php echo $footerHtml; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a reusable admin page header with an optional action area
 */

/**
 * Renders a reusable admin page header with an optional action area
 */
function render_admin_page_header($title, $actionHtml = '') {
    ob_start();
    ?>
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-8">
        <div>
            <h1 class="text-2xl font-black text-gray-800"><?php echo $title; ?></h1>
        </div>
        <?php if ($actionHtml !== ''): ?>
            <div><?php echo $actionHtml; ?></div>
        <?php endif; ?>
    </header>
    <?php
    return ob_get_clean();
}

/**
 * Renders a reusable admin white panel wrapper
 */

/**
 * Renders a reusable admin white panel wrapper
 */
function render_admin_panel(string $contentHtml, $classes = 'bg-gray-100 rounded-3xl border border-gray-200 overflow-hidden p-4') {
    return '<div class="' . $classes . '">' . $contentHtml . '</div>';
}

/**
 * Renders common admin toast notifications based on query params
 */

/**
 * Renders common admin toast notifications based on query params
 */
function render_admin_toast_messages(array $messages = []) {
    $defaultMessages = [
        'error' => [
            'csrf' => ['message' => 'Ошибка проверки безопасности. Повторите действие.', 'type' => 'error'],
        ],
        'success' => [
            'saved' => ['message' => 'Данные успешно сохранены.', 'type' => 'success'],
            'deleted' => ['message' => 'Запись успешно удалена.', 'type' => 'success'],
        ],
    ];
    
    $merged = array_replace_recursive($defaultMessages, $messages);
    
    $html = render_toast();
    foreach ($merged as $key => $messageMap) {
        if (!isset($_GET[$key])) continue;
        $value = $_GET[$key];
        foreach ($messageMap as $match => $config) {
            if ((string)$value === (string)$match) {
                $html .= render_toast_script($config['message'], $config['type']);
            }
        }
    }
    return $html;
}

/**
 * Standardizes the footer of an admin form (save/delete buttons)
 */

/**
 * Standardizes the footer of an admin form (save/delete buttons)
 */
function render_admin_form_actions($saveId = 'save-button', $deleteHtml = '', $saveText = 'Сохранить') {
    $btnTextClass = $saveId . '-text';
    $loaderClass = $saveId . '-loader';
    return '
        <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
            ' . $deleteHtml . '
            ' . render_admin_submit_button($saveText, 'id="' . $saveId . '"', $btnTextClass, $loaderClass . ' hidden', true) . '
        </div>
    ';
}

/**
 * Standardizes AJAX form submission for admin forms
 */

/**
 * Standardizes AJAX form submission for admin forms
 */
function render_admin_form_scripts($formId, $saveBtnId = 'save-button', $successUrl = null) {
    $successUrl = $successUrl ? "'$successUrl'" : "window.location.pathname + '?success=saved'";
    ob_start();
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('<?php echo $formId; ?>');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            if (e.submitter && (e.submitter.type === 'button' || e.submitter.onclick)) return;
            
            e.preventDefault();
            const saveBtn = document.getElementById('<?php echo $saveBtnId; ?>');
            const btnText = saveBtn?.querySelector('.<?php echo $saveBtnId; ?>-text') || saveBtn?.querySelector('.button-text');
            const spinner = saveBtn?.querySelector('.<?php echo $saveBtnId; ?>-loader') || saveBtn?.querySelector('.loading-spinner');

            if (saveBtn) {
                saveBtn.disabled = true;
                btnText?.classList.add('hidden');
                spinner?.classList.remove('hidden');
            }

            const formData = new FormData(form);
            if (e.submitter && e.submitter.name) formData.append(e.submitter.name, e.submitter.value);

            try {
                const response = await fetch(window.location.pathname, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();
                if (result.success) {
                    showToast(result.message || 'Данные сохранены.', 'success');
                    setTimeout(() => window.location.href = <?php echo $successUrl; ?>, 1000);
                } else {
                    showToast(result.error || 'Ошибка при сохранении.', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        btnText?.classList.remove('hidden');
                        spinner?.classList.add('hidden');
                    }
                }
            } catch (error) {
                console.error(error);
                form.submit();
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Renders the script for standardized deletion with confirmation
 */

/**
 * Renders the script for standardized deletion with confirmation
 */
function render_admin_delete_script($actionName, $confirmTitle = 'Удаление', $confirmMessage = 'Вы уверены?') {
    ob_start();
    ?>
    <script>
    let currentDeleteId = null;
    let currentDeleteRow = null;

    function confirmDelete(id, button) {
        currentDeleteId = id;
        currentDeleteRow = button.closest('tr');
        openConfirmModal('confirm-modal', '<?php echo $confirmTitle; ?>', '<?php echo $confirmMessage; ?>', executeDelete);
    }

    async function executeDelete() {
        if (!currentDeleteId) return;
        
        const form = document.querySelector(`.delete-form[data-id="${currentDeleteId}"]`) || document.querySelector(`.delete-<?php echo $actionName; ?>-form[data-id="${currentDeleteId}"]`);
        if (!form) return;
        const formData = new FormData(form);
        
        const confirmBtn = document.getElementById('confirm-modal-confirm-btn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<?php echo get_icon('clock', 'w-4 h-4 animate-spin'); ?>';
        }

        try {
            const response = await fetch(window.location.pathname, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            const result = await response.json();
            if (result.success) {
                if (typeof showToast === 'function') showToast(result.message || 'Удалено.', 'success');
                if (currentDeleteRow) {
                    currentDeleteRow.classList.add('opacity-0', '-translate-x-4');
                    setTimeout(() => currentDeleteRow.remove(), 500);
                }
            } else {
                if (typeof showToast === 'function') showToast(result.error || 'Ошибка удаления.', 'error');
            }
        } catch (error) {
            console.error(error);
            form.submit();
        } finally {
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Удалить сейчас';
            }
        }
    }
    </script>
    <?php
    return ob_get_clean();
}


/**
 * Renders a standardized status badge
 */


/**
 * Renders a standardized status badge
 */
function render_admin_status_badge($status, $activeText = 'Активно', $inactiveText = 'Неактивно') {
    $isActive = $status === 'active' || $status === true || $status === 1 || $status === '1';
    $color = $isActive ? 'green' : 'gray';
    $text = $isActive ? $activeText : $inactiveText;
    return render_text_badge($text, $color);
}

/**
 * Renders the full admin page skeleton to ensure design consistency
 */

/**
 * Renders the full admin page skeleton to ensure design consistency
 */
function render_admin_page($activeMenu, $title, $pageHeaderTitle, $actionHtml, $contentHtml, $footerScripts = '', $toastConfig = []) {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?> | Rent Car Phuket</title>
        <link rel="stylesheet" href="/assets/css/tailwind.min.css">
        <style>
            ::-webkit-scrollbar { width: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; border: 3px solid transparent; background-clip: content-box; }
            ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; border-radius: 10px; border: 3px solid transparent; background-clip: content-box; }
            .admin-status-toggle input:checked + .admin-status-track { background: #f5c518; }
            .admin-status-toggle input:checked + .admin-status-track::after { transform: translateX(20px); }
        </style>
    </head>
    <body class="flex flex-col min-h-screen bg-gray-100 antialiased" style="font-family: system-ui, -apple-system, sans-serif;">
        <div class="flex h-screen overflow-hidden bg-gray-50">
            <?php if (function_exists('render_sidebar')) render_sidebar($activeMenu); ?>

            <div class="flex-1 overflow-y-auto">
                <?php if (function_exists('render_topbar')) render_topbar('Поиск...'); ?>

                <main class="p-6">
                    <div class="max-w-[1600px] mx-auto">
                        <?php echo render_admin_page_header($pageHeaderTitle, $actionHtml); ?>
                        <?php echo render_toast(); ?>
                        <?php echo render_admin_toast_messages($toastConfig); ?>
                        <?php echo $contentHtml; ?>
                    </div>
                </main>
            </div>
        </div>
        <?php echo render_confirm_modal(); ?>
        <?php echo $footerScripts; ?>
    </body>
    </html>
    <?php
    echo ob_get_clean();
}

/**
 * Renders a single car row for admin inventory table
 */

/**
 * Renders a single car row for admin inventory table
 */
function render_admin_car_row(array $car) {
    ob_start();
    ?>
    <tr class="group hover:bg-gray-50 transition-all duration-300">
        <td class="px-6 py-4 align-middle">
            <a href="/admin?edit=<?php echo $car['id']; ?>" class="flex items-center gap-3 group/car">
                <div class="w-24 h-16 bg-gray-50/80 rounded-2xl overflow-hidden flex items-center justify-center border border-gray-100 p-2 group-hover/car:bg-white transition-all">
                    <img src="<?php echo asset_image_url($car['image']); ?>" class="max-w-full max-h-full object-contain transition-transform group-hover/car:scale-110 duration-500" loading="lazy" onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
                </div>
                <div>
                    <div class="font-black text-gray-800 text-sm leading-tight mb-1 transition-colors"><?php echo $car['name']; ?></div>
                    <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest">
                        <span class="text-gray-400"><?php echo $car['year']; ?> год</span>
                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                        <span class="text-yellow-600 font-black"><?php echo $car['id']; ?></span>
                    </div>
                </div>
            </a>
        </td>
        <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo $car['type']; ?></td>
        <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">฿<?php echo (int)$car['price']; ?></td>
        <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle">฿<?php echo (int)($car['deposit'] ?? 0); ?></td>
        <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo $car['engine']; ?>л</td>
        <td class="px-6 py-4 font-bold text-gray-800 text-sm align-middle"><?php echo $car['transmission']; ?></td>
        <td class="px-6 py-4 align-middle">
            <?php if (!empty($car['discount'])): ?>
                <div class="font-bold text-gray-800 text-sm"><?php echo $car['discount']; ?>%</div>
                <?php if (!empty($car['discount_start']) && !empty($car['discount_end'])): ?>
                    <div class="flex items-center gap-3 text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                        <span><?php echo $car['discount_start']; ?></span>
                        <span class="w-1 h-1 bg-gray-200 rounded-full"></span>
                        <span><?php echo $car['discount_end']; ?></span>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <span class="text-gray-400 text-sm">—</span>
            <?php endif; ?>
        </td>
        <td class="px-6 py-4 align-middle">
            <?php echo render_status_toggle(($car['status'] ?? 'active') === 'active', 'status', 'active', "toggleCarStatus('{$car['id']}', this.checked)"); ?>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}

/**
 * Renders mobile admin bottom navigation
 */

/**
 * Renders mobile admin bottom navigation
 */
function render_admin_mobile_nav($items) {
    ob_start();
    ?>
    <div class="lg:hidden fixed bottom-6 left-6 right-6 bg-white/80 backdrop-blur-2xl border border-white/20 rounded-[32px] p-2 z-[9999] flex justify-around items-center">
        <?php foreach ($items as $item): ?>
            <a href="<?php echo $item['href']; ?>" class="flex flex-col items-center gap-1 p-4 <?php echo $item['active'] ? 'rounded-3xl bg-yellow-400 text-gray-800' : 'text-gray-400'; ?>">
                <?php echo get_icon($item['icon'], 'w-5 h-5'); ?>
                <span class="text-[8px] font-black uppercase"><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a reusable admin form field block
 */

/**
 * Renders a reusable admin form field block
 */
function render_admin_field($label, $controlHtml, $wrapperClass = 'space-y-2', $labelClass = 'text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1', $extraLabel = '') {
    $extraLabelHtml = $extraLabel ? ' <span class="normal-case font-normal text-gray-300">(' . e($extraLabel) . ')</span>' : '';
    ob_start();
    ?>
    <div class="<?php echo $wrapperClass; ?>">
        <label class="<?php echo $labelClass; ?>"><?php echo $label; ?><?php echo $extraLabelHtml; ?></label>
        <?php echo $controlHtml; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders an admin money input with currency prefix
 */

/**
 * Renders an admin money input with currency prefix
 */
function render_admin_money_input($name, $value = '', $required = true, $placeholder = '') {
    $requiredAttr = $required ? ' required' : '';
    $placeholderAttr = $placeholder !== '' ? ' placeholder="' . e($placeholder) . '"' : '';
    return '<div class="relative"><div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-black">฿</div><input type="number" name="' . $name . '" value="' . e((string)$value) . '" class="w-full bg-gray-100 border border-gray-200 rounded-2xl pl-12 pr-6 py-4 font-bold text-gray-800 focus:bg-white focus:border-yellow-400 outline-none transition-all"' . $placeholderAttr . $requiredAttr . '></div>';
}

/**
 * Renders a readonly date input used in admin forms
 */

/**
 * Renders a readonly date input used in admin forms
 */
function render_admin_readonly_date_input($name, $value = '', $placeholder = 'дд.мм.гггг', $id = '') {
    $idAttr = $id !== '' ? ' id="' . $id . '"' : '';
    return '<input type="text"' . $idAttr . ' name="' . $name . '" value="' . e((string)$value) . '" class="w-full bg-gray-100 border border-gray-200 rounded-2xl px-6 py-4 font-bold text-gray-800 placeholder-gray-400 focus:bg-white focus:border-yellow-400 outline-none transition-all" placeholder="' . e($placeholder) . '" readonly>';
}


function render_admin_input($name, $value, $type = 'text', $classes = '', $placeholder = '', $attrs = '', $containerClasses = '') {
    $placeholderAttr = $placeholder ? ' placeholder="' . e($placeholder) . '"' : '';
    $valueAttr = $value !== '' ? ' value="' . e($value) . '"' : '';
    return '<div class="w-full ' . $containerClasses . '">
        <input type="' . $type . '" name="' . $name . '"' . $valueAttr . $placeholderAttr . ' ' . $attrs . ' class="w-full bg-gray-100 border border-gray-200 rounded-2xl px-6 py-4 text-sm font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:border-yellow-400 transition-all ' . $classes . '">
    </div>';
}

/**
 * Renders a status block with toggle
 */

/**
 * Renders a status block with toggle
 */
function render_admin_status_block($label, $description, $checked, $toggleAttrs = '') {
    return '<div class="px-8 py-6 bg-gray-100 rounded-[32px] border border-gray-200 flex items-center justify-between min-h-[132px]"><div><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">' . e($description) . '</p></div>' . render_status_toggle($checked, 'status', 'active', $toggleAttrs) . '</div>';
}

/**
 * Renders a file picker block with preview
 */

/**
 * Renders a file picker block with preview
 */
function render_admin_image_picker($image = '', $fileLabel = 'Файл не выбран') {
    $preview = $image
        ? '<div class="relative group/img"><img id="car-image-preview" src="' . asset_image_url((string)$image) . '" class="w-20 h-20 object-contain rounded-2xl bg-white border border-gray-200 p-2"></div>'
        : '<div id="car-image-placeholder" class="w-20 h-20 rounded-2xl bg-gray-200 flex items-center justify-center text-gray-400">' . get_icon('camera', 'w-8 h-8') . '</div><img id="car-image-preview" src="" class="hidden w-20 h-20 object-contain rounded-2xl bg-white border border-gray-200 p-2">';

    return '<div class="flex items-center gap-6 p-4 bg-gray-100 rounded-[32px] border-2 border-dashed border-gray-200 transition-all group min-h-[132px]">' . $preview . '<div class="flex flex-col gap-2">' . render_admin_file_button('Выберите файл') . '<div id="file-name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">' . e($fileLabel) . '</div></div></div>';
}

/**
 * Renders a reusable admin section card with title block and content
 */

/**
 * Renders a reusable admin section card with title block and content
 */
function render_admin_section_card($icon, $title, $iconBgClass, $iconTextClass, string $contentHtml) {
    ob_start();
    ?>
    <section>
        <?php echo render_admin_section_header($icon, $title, $iconBgClass, $iconTextClass); ?>
        <?php echo $contentHtml; ?>
    </section>
    <?php
    return ob_get_clean();
}


/**
 * Renders a password field with visibility toggle
 */


/**
 * Renders a password field with visibility toggle
 */
function render_admin_password_field($name, $label, $inputId, $placeholder = '••••••••') {
    ob_start();
    ?>
    <div class="space-y-2">
        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1"><?php echo $label; ?></label>
        <div class="relative">
            <?php echo render_admin_input($name, '', 'password', 'pr-12', $placeholder, 'id="' . $inputId . '"'); ?>
            <button type="button" onclick="togglePassword('<?php echo $inputId; ?>', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-all">
                <span class="eye-open"><?php echo get_icon('eye', 'w-5 h-5'); ?></span>
                <span class="eye-closed hidden"><?php echo get_icon('eye-slash', 'w-5 h-5'); ?></span>
            </button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders admin car form scripts
 */

/**
 * Renders admin car form scripts
 */
function render_admin_car_form_script($csrfToken) {
    ob_start();
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form[method="POST"]');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            if (!e.submitter || (!e.submitter.id?.includes('save-button') && !e.submitter.type?.includes('submit'))) return;
            if (e.target.id === 'delete-form') return;

            e.preventDefault();

            const saveBtn = document.getElementById('save-button');
            const btnText = saveBtn?.querySelector('.button-text');
            const spinner = saveBtn?.querySelector('.loading-spinner');

            if (saveBtn) {
                saveBtn.disabled = true;
                btnText?.classList.add('hidden');
                spinner?.classList.remove('hidden');
            }

            const formData = new FormData(form);
            if (e.submitter.name) formData.append(e.submitter.name, e.submitter.value);

            // Inline validation
            const name = formData.get('name')?.toString().trim();
            const year = parseInt(formData.get('year')?.toString() || '0');
            const price = parseFloat(formData.get('price')?.toString() || '0');

            let errors = [];
            if (!name || name.length < 2) errors.push('Укажите название модели');
            if (year < 2000 || year > new Date().getFullYear() + 1) errors.push('Укажите корректный год');
            if (price <= 0) errors.push('Цена должна быть больше 0');

            if (errors.length > 0) {
                showToast(errors[0], 'error');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    btnText?.classList.remove('hidden');
                    spinner?.classList.add('hidden');
                }
                return;
            }

            try {
                const response = await fetch(window.location.pathname, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                const result = await response.json();
                if (result.success) {
                    showToast(result.message || 'Автопарк сохранён.', 'success');
                    setTimeout(() => window.location.href = '/admin?success=saved', 1000);
                } else {
                    showToast(result.error || 'Не удалось сохранить автопарк.', 'error');
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        btnText?.classList.remove('hidden');
                        spinner?.classList.add('hidden');
                    }
                }
            } catch (error) {
                console.error(error);
                form.submit();
            }
        });
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const fileNameDisplay = document.getElementById('file-name');
            if (fileNameDisplay) fileNameDisplay.textContent = fileName;

            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('car-image-preview');
                var placeholder = document.getElementById('car-image-placeholder');
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const config = {
            locale: "ru",
            dateFormat: "d.m.Y",
            allowInput: true,
            minDate: "today",
            monthSelectorType: "static"
        };
        if (document.getElementById('discount_start')) flatpickr("#discount_start", config);
        if (document.getElementById('discount_end')) flatpickr("#discount_end", config);
    });

    async function toggleCarStatus(carId, isActive) {
        const formData = new FormData();
        formData.append('toggle_status', '1');
        formData.append('car_id', carId);
        formData.append('status', isActive ? 'active' : 'inactive');
        formData.append('csrf_token', '<?php echo $csrfToken; ?>');

        try {
            const response = await fetch(window.location.pathname, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            if (result.success) {
                showToast(result.message || 'Статус автомобиля обновлён.', 'success');
            } else {
                if (typeof showToast === 'function') showToast(result.error || 'Не удалось изменить статус автомобиля.', 'error');
                window.location.reload();
            }
        } catch (error) {
            console.error(error);
            window.location.reload();
        }
    }
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Renders a reusable modal shell for admin dialogs
 */

/**
 * Renders a reusable modal shell for admin dialogs
 */
function render_admin_modal_shell($id, $title, string $headerRightHtml, string $bodyHtml, $maxWidthClass = 'max-w-xl') {
    ob_start();
    ?>
    <div id="<?php echo $id; ?>" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all overflow-y-auto">
        <div class="bg-white rounded-[40px] w-full <?php echo $maxWidthClass; ?> overflow-hidden animate-fade-in-down mb-auto mt-20">
            <div class="p-8 pb-4 flex justify-between items-center">
                <h3 class="text-2xl font-black text-gray-800 tracking-tight leading-tight"><?php echo $title; ?></h3>
                <?php echo $headerRightHtml; ?>
            </div>
            <div class="px-8 pb-8">
                <?php echo $bodyHtml; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a single admin sidebar navigation item
 */

/**
 * Renders a single admin sidebar navigation item
 */
function render_admin_sidebar_item(array $item, array $counts, $active) {
    $isActive = $active === $item['id'];
    $isLogout = $item['id'] === 'logout';
    $itemClass = $isActive ? 'bg-gray-800 text-white font-black' : ($isLogout ? 'text-red-500 hover:bg-red-50 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800 font-bold text-sm');
    $iconClass = $isActive ? 'bg-yellow-400 text-gray-800' : ($isLogout ? 'bg-red-50 text-red-400 group-hover:bg-red-100' : 'bg-gray-100 text-gray-400 group-hover:bg-gray-50');
    $badge = '';
    if (isset($counts[$item['id']]) && $counts[$item['id']] > 0) {
        $badgeClass = $isActive ? 'bg-yellow-400 text-gray-800' : 'bg-yellow-50 text-yellow-600';
        $badge = '<div class="' . $badgeClass . ' text-[10px] font-black px-2 py-1 rounded-lg">' . $counts[$item['id']] . '</div>';
    }

    return '<a href="' . $item['url'] . '" class="flex items-center gap-4 p-3 rounded-2xl transition-all active:scale-95 ' . $itemClass . ' group"><div class="w-8 h-8 rounded-xl flex items-center justify-center transition-all ' . $iconClass . '">' . get_icon($item['icon'], 'w-4 h-4') . '</div><div class="flex-1">' . $item['name'] . '</div>' . $badge . '</a>';
}

/**
 * Renders a single admin dropdown item
 */

/**
 * Renders a single admin dropdown item
 */
function render_admin_dropdown_item($url, $icon, $label, $labelClass, $iconWrapperClass, $iconClass = 'w-4 h-4') {
    return '<a href="' . $url . '" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-all group"><div class="w-8 h-8 rounded-lg ' . $iconWrapperClass . ' flex items-center justify-center group-hover:bg-gray-50 transition-all">' . get_icon($icon, $iconClass) . '</div><span class="text-sm font-black ' . $labelClass . '">' . $label . '</span></a>';
}

/**
 * Renders a centered notice block for client pages
 */

/**
 * Renders a user table row for admin
 */
function render_admin_user_row(array $user, int $bookingCount) {
    $userJson = htmlspecialchars(json_encode($user, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
    ob_start();
    ?>
    <tr class="group hover:bg-gray-50 transition-all duration-300">
        <td class="px-6 py-4 align-middle"><div class="text-sm font-black text-gray-800 tracking-tight leading-tight"><?php echo $user['name']; ?></div></td>
        <td class="px-6 py-4 align-middle"><div class="text-sm font-bold text-gray-700"><?php echo $user['phone']; ?></div><div class="text-[10px] font-bold text-yellow-600 uppercase tracking-widest mt-0.5"><?php echo $user['email']; ?></div></td>
        <td class="px-6 py-4 align-middle"><div class="flex items-center gap-2"><?php echo render_text_badge($bookingCount); ?><span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">заказов</span></div></td>
        <td class="px-6 py-4 align-middle"><div class="text-sm font-bold text-gray-800"><?php echo date('d.m.Y', strtotime($user['registered_at'])); ?></div></td>
        <td class="px-6 py-4 text-right align-middle"><?php echo render_admin_button('Изменить', '#', 'primary', '', 'button', '', 'data-user="' . $userJson . '" onclick="editUser(this)"'); ?></td>
    </tr>
    <?php
    return ob_get_clean();
}

/**
 * Renders a booking table row for admin
 */

/**
 * Renders a booking table row for admin
 */
function render_admin_booking_row(array $booking, ?array $user, ?array $car, string $csrfToken) {
    ob_start();
    ?>
    <tr class="group hover:bg-gray-50 transition-all duration-300">
        <td class="px-6 py-4 align-middle"><div class="text-sm font-black text-gray-800 leading-tight">#<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1"><?php echo date('d.m.Y', strtotime($booking['created_at'])); ?></div></td>
        <td class="px-6 py-4 align-middle"><div class="text-sm font-bold text-gray-800"><?php echo $user ? $user['name'] : ($booking['client_name'] ?? 'N/A'); ?></div><div class="text-[10px] font-bold text-yellow-600 uppercase tracking-widest mt-0.5"><?php echo $user ? $user['phone'] : ($booking['client_phone'] ?? ''); ?></div></td>
        <td class="px-6 py-4 align-middle"><div class="flex items-center gap-4"><div class="w-16 h-10 bg-gray-50/80 rounded-xl overflow-hidden flex items-center justify-center border border-gray-100 p-1"><?php echo ($car && isset($car['image'])) ? '<img src="' . asset_image_url((string)$car['image']) . '" class="max-w-full max-h-full object-contain" loading="lazy" onerror="this.onerror=null;this.src=\'' . placeholder_image_url() . '\'">' : '<div class="text-gray-300">' . get_icon('car', 'w-4 h-4') . '</div>'; ?></div><div class="text-sm font-black text-gray-800"><?php echo $car ? $car['name'] : 'N/A'; ?></div></div></td>
        <td class="px-6 py-4 align-middle"><div class="text-sm font-bold text-gray-800"><?php echo date('d.m.Y', strtotime($booking['start_date'])); ?> — <?php echo date('d.m.Y', strtotime($booking['end_date'])); ?></div><div class="flex items-center gap-2 mt-1 px-3 py-1 bg-gray-50 rounded-lg w-fit"><?php echo get_icon('map-pin', 'w-3 h-3 text-yellow-600'); ?><span class="text-[10px] font-black text-gray-700 uppercase tracking-tight"><?php echo $booking['pickup_location']; ?></span><span class="text-gray-300 mx-0.5"><?php echo get_icon('chevron-right', 'w-2 h-2'); ?></span><span class="text-[10px] font-black text-gray-700 uppercase tracking-tight"><?php echo $booking['return_location']; ?></span></div></td>
        <td class="px-6 py-4 align-middle"><div class="text-sm font-black text-gray-800">฿<?php echo (int)$booking['total_price']; ?></div></td>
        <td class="px-6 py-4 align-middle"><?php echo render_admin_danger_button('Удалить', 'onclick="confirmDelete(\'' . $booking['id'] . '\', this)"'); ?><form method="POST" class="delete-booking-form hidden" data-id="<?php echo $booking['id']; ?>"><input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>"><input type="hidden" name="delete_booking" value="1"><input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>"></form></td>
    </tr>
    <?php
    return ob_get_clean();
}


/**
 * Renders a standard admin select field
 */


/**
 * Renders a standard admin select field
 */
function render_admin_select($name, array $options, $selected = '', $attrs = '') {
    $attrs = $attrs ? ' ' . $attrs : '';
    ob_start();
    ?>
    <select name="<?php echo $name; ?>" class="w-full bg-gray-100 border border-gray-200 rounded-2xl px-6 py-4 font-bold text-gray-800 focus:bg-white focus:border-yellow-400 outline-none transition-all appearance-none cursor-pointer<?php echo $attrs; ?>">
        <?php foreach ($options as $optionValue => $optionLabel): ?>
            <option value="<?php echo htmlspecialchars((string)$optionValue, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string)$selected === (string)$optionValue) ? 'selected' : ''; ?>><?php echo $optionLabel; ?></option>
        <?php endforeach; ?>
    </select>
    <?php
    return ob_get_clean();
}

/**
 * Renders a standard admin textarea field
 */

/**
 * Renders a standard admin textarea field
 */
function render_admin_textarea($name, $value = '', $rows = 3, $attrs = '', $placeholder = '') {
    $placeholderAttr = $placeholder !== '' ? ' placeholder="' . $placeholder . '"' : '';
    $attrs = $attrs ? ' ' . $attrs : '';
    return '<textarea name="' . $name . '" rows="' . (int)$rows . '"' . $placeholderAttr . ' class="w-full bg-gray-100 border border-gray-200 rounded-[32px] px-8 py-6 text-gray-800 font-medium outline-none focus:bg-white focus:border-yellow-400 transition-all min-h-[120px] placeholder-gray-400' . $attrs . '">' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</textarea>';
}

/**
 * Renders the Hero section
 */

/**
 * Renders a standardized admin table with a header
 */
function render_admin_table(array $headers, string $rowsHtml) {
    ob_start();
    ?>
    <div class="overflow-x-auto bg-white rounded-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <?php foreach ($headers as $header): ?>
                        <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100">
                            <?php echo $header; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php echo $rowsHtml ?: '<tr><td colspan="'.count($headers).'" class="p-10 text-center text-gray-400 font-bold">Ничего не найдено</td></tr>'; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders admin pagination
 */

/**
 * Renders admin pagination
 */
function render_admin_pagination($currentPage, $totalPages, $offset, $perPage, $totalItems) {
    if ($totalPages <= 1) return '';
    ob_start();
    ?>
    <div class="flex flex-col sm:flex-row items-center justify-between mt-8 gap-4 px-2">
        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
            Показано <?php echo $offset + 1; ?>-<?php echo min($offset + $perPage, $totalItems); ?> из <?php echo $totalItems; ?>
        </div>
        <div class="flex items-center gap-2">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?php echo $currentPage - 1; ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-800 transition-all shadow-sm">
                    <?php echo get_icon('chevron-right', 'w-4 h-4 rotate-180'); ?>
                </a>
            <?php endif; ?>
            
            <?php 
            $start = max(1, $currentPage - 2);
            $end = min($totalPages, $currentPage + 2);
            for ($i = $start; $i <= $end; $i++): 
            ?>
                <a href="?page=<?php echo $i; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl font-black text-xs transition-all shadow-sm <?php echo $i === (int)$currentPage ? 'bg-yellow-400 text-gray-800' : 'bg-white border border-gray-100 text-gray-400 hover:text-gray-800'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?php echo $currentPage + 1; ?>" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-800 transition-all shadow-sm">
                    <?php echo get_icon('chevron-right', 'w-4 h-4'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a grid of statistic cards for admin dashboard
 */

/**
 * Renders a grid of statistic cards for admin dashboard
 */
function render_admin_stats_grid(array $stats) {
    ob_start();
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <?php foreach ($stats as $stat): ?>
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-blue-100 transition-all">
                <div class="absolute top-0 right-0 w-24 h-24 bg-<?php echo $stat['color'] ?? 'blue'; ?>-50 rounded-bl-[100%] transition-transform group-hover:scale-110"></div>
                <div class="relative">
                    <div class="text-<?php echo $stat['color'] ?? 'blue'; ?>-600 mb-4"><?php echo get_icon($stat['icon'] ?? 'chart', 'w-8 h-8'); ?></div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1"><?php echo $stat['label']; ?></div>
                    <div class="text-3xl font-black text-gray-800 tracking-tighter"><?php echo $stat['value']; ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}


/**
 * Renders the Booking Form Section
 */
