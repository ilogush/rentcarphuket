<?php
declare(strict_types=1);


/**
 * Short helper for htmlspecialchars to prevent XSS
 */
function e($text) {
    return htmlspecialchars((string)($text ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Returns the public URL for a local image asset.
 */

/**
 * Returns the public URL for a local image asset.
 */
function asset_image_url(string $file): string {
    return '/assets/images/' . ltrim($file, '/');
}

/**
 * Returns the public URL for the shared placeholder image.
 */

/**
 * Returns the public URL for the shared placeholder image.
 */
function placeholder_image_url(): string {
    return '/assets/images/placeholder.webp';
}

/**
 * Standardizes the 4-column grid layout for admin forms
 */

/**
 * Reusable UI Components for Rent Car Phuket
 */

/**
 * Renders a large client-side link button with icon support
 */
function render_client_button($text, $link = '#', $icon = '', $variant = 'primary', $extraClasses = '', $target = '', $attrs = '') {
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-white text-gray-800 border border-gray-100 hover:bg-gray-50',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $iconHtml = $icon ? '<span class="shrink-0">' . $icon . '</span>' : '';
    $targetAttr = $target ? ' target="' . $target . '"' : '';
    $extraAttrs = $attrs ? ' ' . $attrs : '';

    return '<a href="' . $link . '"' . $targetAttr . $extraAttrs . ' class="inline-flex items-center justify-center gap-3 px-10 py-5 rounded-2xl font-black transition-all active:scale-95 uppercase tracking-widest text-xs ' . $variantClass . ' ' . $extraClasses . '">' . $iconHtml . '<span>' . $text . '</span></a>';
}

/**
 * Renders a large client-side submit button with icon support
 */

/**
 * Renders a large client-side submit button with icon support
 */
function render_client_submit_button($text, $icon = '', $variant = 'primary', $extraClasses = '', $attrs = '') {
    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-white text-gray-800 border border-gray-100 hover:bg-gray-50',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $iconHtml = $icon ? '<span class="bg-white/20 p-1.5 rounded-full group-hover:translate-x-2 transition-transform duration-500 relative z-10 font-black">' . $icon . '</span>' : '';
    $extraAttrs = $attrs ? ' ' . $attrs : '';

    return '<button type="submit"' . $extraAttrs . ' class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition-all shadow-2xl shadow-blue-600/30 active:scale-[0.98] flex items-center justify-center gap-4 uppercase tracking-widest text-sm group overflow-hidden relative ' . $extraClasses . '"><span class="relative z-10">' . $text . '</span>' . $iconHtml . '</button>';
}

/**
 * Renders a large client-side link button with icon arrow
 */

/**
 * Renders a large client-side link button with icon arrow
 */
function render_client_link_button($text, $link, $icon = '', $extraClasses = '', $target = '', $attrs = '') {
    $iconHtml = $icon ? '<span class="bg-white/20 p-1.5 rounded-full group-hover:translate-x-2 transition-transform duration-500 relative z-10 font-black">' . $icon . '</span>' : '';
    $targetAttr = $target ? ' target="' . $target . '"' : '';
    $extraAttrs = $attrs ? ' ' . $attrs : '';

    return '<a href="' . $link . '"' . $targetAttr . $extraAttrs . ' class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition-all shadow-2xl shadow-blue-600/30 active:scale-[0.98] flex items-center justify-center gap-4 uppercase tracking-widest text-sm group overflow-hidden relative ' . $extraClasses . '"><span class="relative z-10">' . $text . '</span>' . $iconHtml . '</a>';
}

/**
 * Renders a flat admin button with two colors only
 */

/**
 * Renders a flat admin button with two colors only
 */
function render_admin_button($text, $link = '#', $variant = 'primary', $extraClasses = '', $type = 'link', $icon = '', $attrs = '') {
    $variants = [
        'primary' => 'bg-yellow-400 text-gray-800',
        'secondary' => 'bg-white text-gray-800 hover:bg-gray-50 border border-gray-100',
    ];
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $iconHtml = $icon ? '<span class="shrink-0">' . $icon . '</span>' : '';
    $extraAttrs = $attrs ? ' ' . $attrs : '';

    if ($type === 'button') {
        return '<button type="button"' . $extraAttrs . ' class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-colors ' . $variantClass . ' ' . $extraClasses . '">' . $iconHtml . '<span>' . $text . '</span></button>';
    }

    return '<a href="' . $link . '"' . $extraAttrs . ' class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-colors ' . $variantClass . ' ' . $extraClasses . '">' . $iconHtml . '<span>' . $text . '</span></a>';
}

/**
 * Short helper for htmlspecialchars to prevent XSS
 */

/**
 * Renders a reusable admin status toggle
 */
function render_status_toggle($checked, $name = 'status', $value = 'active', $onchange = '') {
    ob_start();
    ?>
    <label class="admin-status-toggle relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="<?php echo $name; ?>" value="<?php echo $value; ?>" class="sr-only peer" <?php echo $onchange ? 'onchange="' . $onchange . '"' : ''; ?> <?php echo $checked ? 'checked' : ''; ?>>
        <div class="admin-status-track w-11 h-6 bg-gray-200 rounded-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
    </label>
    <?php
    return ob_get_clean();
}

/**
 * Renders a small text badge
 */

/**
 * Renders a small text badge
 */
function render_text_badge($text, $variant = 'gray') {
    $classes = [
        'gray' => 'inline-flex items-center justify-center bg-gray-50/80 px-3 py-1 rounded-lg text-[10px] font-black text-gray-800 border border-gray-100',
        'blue' => 'inline-flex items-center justify-center bg-blue-50 px-3 py-1 rounded-lg text-[10px] font-black text-blue-600 border border-blue-100',
        'green' => 'inline-flex items-center justify-center bg-green-50 px-3 py-1 rounded-lg text-[10px] font-black text-green-600 border border-green-100',
        'red' => 'inline-flex items-center justify-center bg-red-50 px-3 py-1 rounded-lg text-[10px] font-black text-red-600 border border-red-100',
    ];

    $class = $classes[$variant] ?? $classes['gray'];

    return '<span class="' . $class . '">' . $text . '</span>';
}

/**
 * Renders a simple table wrapper
 */

/**
 * Renders a simple table wrapper
 */
function render_table(array $headers, string $rowsHtml) {
    ob_start();
    ?>
    <div class="overflow-x-auto bg-white rounded-2xl">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-50">
                    <?php foreach ($headers as $header): ?>
                        <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-[0.2em]"><?php echo $header['label'] ?? ''; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php echo $rowsHtml; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders common scripts for the home page (datepicker, location search, sorting)
 */

/**
 * Renders a centered notice block for client pages
 */
function render_centered_notice($iconHtml, $title, $message, $actionsHtml, $iconClasses = 'bg-red-50 text-red-500', $iconSizeClasses = 'w-32 h-32', $titleClasses = 'text-6xl font-black text-gray-800 tracking-tighter mb-6', $messageClasses = 'text-xl md:text-2xl font-bold text-gray-400 mb-12') {
    ob_start();
    ?>
    <div class="max-w-2xl mx-auto py-32 px-4 text-center">
        <div class="mb-10 inline-flex items-center justify-center <?php echo $iconSizeClasses; ?> rounded-3xl <?php echo $iconClasses; ?>">
            <?php echo $iconHtml; ?>
        </div>
        <h1 class="<?php echo $titleClasses; ?>"><?php echo $title; ?></h1>
        <p class="<?php echo $messageClasses; ?>"><?php echo $message; ?></p>
        <div class="flex flex-col sm:flex-row gap-6 justify-center">
            <?php echo $actionsHtml; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a user table row for admin
 */

/**
 * Renders a standard centered section container
 */
function render_container($content, $padding = 'py-20') {
    return "<div class=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 $padding\">$content</div>";
}

/**
 * Renders a page header for internal pages
 */

/**
 * Renders a page header for internal pages
 */
function render_page_header($title, $subtitle = '') {
    ob_start();
    ?>
    <section class="mc-page-header">
        <div class="mc-container">
            <h1><?php echo e($title); ?></h1>
            <?php if($subtitle): ?>
                <p><?php echo e($subtitle); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Renders a basic text block container
 */

/**
 * Renders a basic text block container
 */
function render_content_block($content) {
    ob_start();
    ?>
    <div class="max-w-4xl mx-auto py-16 px-4">
        <div class="prose prose-lg prose-blue max-w-none text-gray-600 leading-relaxed font-medium">
            <?php echo $content; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a progress stepper (paginator) for the booking flow
 */

/**
 * Renders the Toast component (hidden by default)
 */
function render_toast() {
    ob_start();
    ?>
    <div id="toast-container" class="fixed bottom-8 right-8 z-[9999] flex flex-col gap-3 pointer-events-none"></div>
    
    <style>
        @keyframes toast-in {
            from { transform: translateY(100%) scale(0.9); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes toast-out {
            from { transform: translateY(0) scale(1); opacity: 1; }
            to { transform: translateY(100%) scale(0.9); opacity: 0; }
        }
        .animate-toast-in { animation: toast-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .animate-toast-out { animation: toast-out 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>

    <script>
    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `flex items-center gap-4 px-6 py-4 rounded-2xl bg-gray-800 border border-white/5 pointer-events-auto cursor-pointer animate-toast-in transition-all active:scale-95`;
        
        const icons = {
            success: `<div class="text-green-400"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg></div>`,
            error: `<div class="text-red-400"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></div>`,
            info: `<div class="text-blue-400"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg></div>`
        };

        toast.innerHTML = `
            ${icons[type] || icons.success}
            <span class="font-black text-xs uppercase text-white flex-1">${message}</span>
            <div class="text-gray-400 hover:text-white transition-colors ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        `;
        
        toast.onclick = () => {
            toast.classList.replace('animate-toast-in', 'animate-toast-out');
            setTimeout(() => toast.remove(), 500);
        };

        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.classList.replace('animate-toast-in', 'animate-toast-out');
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    };
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Renders quick public contact actions.
 */

/**
 * Renders quick public contact actions.
 */
function render_public_contact_bar(array $contactInfo = []) {
    $telegram = $contactInfo['socialMedia']['telegram'] ?? '';
    $whatsapp = $contactInfo['socialMedia']['whatsapp'] ?? '';
    $phone = $contactInfo['phone'] ?? '';

    if (!$telegram && !$whatsapp && !$phone) {
        return '';
    }

    ob_start();
    ?>
    <div class="mc-contact-fab" aria-label="Быстрые контакты">
        <?php if ($whatsapp): ?>
            <a href="<?php echo e($whatsapp); ?>" aria-label="WhatsApp"><?php echo get_icon('whatsapp', 'w-5 h-5'); ?><span>WhatsApp</span></a>
        <?php endif; ?>
        <?php if ($telegram): ?>
            <a href="<?php echo e($telegram); ?>" aria-label="Telegram"><?php echo get_icon('telegram', 'w-5 h-5'); ?><span>Telegram</span></a>
        <?php endif; ?>
        <?php if ($phone): ?>
            <a href="tel:<?php echo e($phone); ?>" aria-label="Позвонить"><?php echo get_icon('phone', 'w-5 h-5'); ?><span>Позвонить</span></a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a small inline toast trigger script
 */

/**
 * Renders a small inline toast trigger script
 */
function render_toast_script($message, $type = 'success') {
    $message = json_encode((string)$message, JSON_UNESCAPED_UNICODE);
    $type = json_encode((string)$type, JSON_UNESCAPED_UNICODE);

    return "<script>document.addEventListener('DOMContentLoaded', () => showToast($message, $type));</script>";
}

/**
 * Renders a confirm modal component
 */

/**
 * Renders a confirm modal component
 */
function render_confirm_modal($id = 'confirm-modal') {
    ob_start();
    ?>
    <div id="<?php echo $id; ?>" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all">
        <div class="bg-white rounded-[40px] w-full max-w-sm overflow-hidden animate-fade-in-down relative">
            <!-- Close Button -->
            <button onclick="closeConfirmModal('<?php echo $id; ?>')" class="absolute top-6 right-6 text-gray-300 hover:text-gray-800 transition-colors p-2 z-10">
                <?php echo get_icon('x', 'w-6 h-6'); ?>
            </button>
            
            <div class="px-8 pt-10 pb-10 flex flex-col items-center text-center">
                <!-- Icon -->
                <div class="bg-red-50 w-20 h-20 rounded-[32px] flex items-center justify-center text-red-500 mb-8" id="<?php echo $id; ?>-icon-wrapper">
                    <?php echo get_icon('trash', 'w-8 h-8'); ?>
                </div>
                
                <!-- Content -->
                <h3 class="text-2xl font-black text-gray-800 tracking-tight leading-tight mb-4" id="<?php echo $id; ?>-title">Подтвердите удаление</h3>
                <p class="text-gray-500 font-bold text-sm leading-relaxed mb-10" id="<?php echo $id; ?>-message">Вы уверены, что хотите выполнить это действие? Данное действие необратимо.</p>
                
                <!-- Buttons -->
                <div class="grid grid-cols-2 gap-4 w-full">
                    <button onclick="closeConfirmModal('<?php echo $id; ?>')" class="bg-gray-100 text-gray-800 px-4 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all active:scale-95">
                        Отмена
                    </button>
                    <button id="<?php echo $id; ?>-confirm-btn" class="bg-[#F85353] text-white px-4 py-5 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all active:scale-95">
                        Удалить сейчас
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
    function openConfirmModal(id, title, message, onConfirm, confirmBtnLabel = null) {
        const modal = document.getElementById(id);
        const titleEl = document.getElementById(id + '-title');
        const messageEl = document.getElementById(id + '-message');
        const confirmBtn = document.getElementById(id + '-confirm-btn');
        
        if (title) titleEl.innerText = title;
        if (message) messageEl.innerText = message;
        if (confirmBtnLabel) confirmBtn.innerText = confirmBtnLabel;
        else if (title.toLowerCase().includes('удалить')) confirmBtn.innerText = 'Удалить сейчас';
        else confirmBtn.innerText = 'Подтвердить';
        
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        newConfirmBtn.onclick = () => {
            onConfirm();
            closeConfirmModal(id);
        };
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeConfirmModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Renders the reusable price calculation summary block for sidebars.
 */

/**
 * Renders a white card container with rounded corners
 */
function render_card($contentHtml, $padding = 'p-10 md:p-12', $rounded = 'rounded-3xl', $extraClasses = '') {
    return '<div class="bg-white ' . $rounded . ' border border-gray-100 overflow-hidden shadow-2xl shadow-gray-200/40 ' . $extraClasses . '"><div class="' . $padding . '">' . $contentHtml . '</div></div>';
}

/**
 * Renders a form input field with label
 */

/**
 * Renders a form input field with label
 */
function render_form_input($name, $label, $type = 'text', $placeholder = '', $required = false, $value = '', $extraClasses = '') {
    $requiredAttr = $required ? ' required' : '';
    $valueAttr = $value ? ' value="' . htmlspecialchars($value) . '"' : '';
    ob_start();
    ?>
    <div class="space-y-2">
        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-4"><?php echo htmlspecialchars($label); ?></label>
        <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" class="w-full bg-blue-50/50 border-none rounded-2xl px-6 py-4 font-bold text-gray-800 placeholder-gray-300 focus:ring-2 focus:ring-blue-600/20 transition-all outline-none <?php echo $extraClasses; ?>" placeholder="<?php echo htmlspecialchars($placeholder); ?>"<?php echo $requiredAttr . $valueAttr; ?>>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a password input field with toggle visibility
 */

/**
 * Renders a password input field with toggle visibility
 */
function render_password_input($name, $label, $placeholder = '••••••••', $required = false, $inputId = '') {
    $requiredAttr = $required ? ' required' : '';
    $idAttr = $inputId ? ' id="' . $inputId . '"' : '';
    $toggleId = $inputId ?: 'password-' . uniqid();
    ob_start();
    ?>
    <div class="space-y-2">
        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-4"><?php echo htmlspecialchars($label); ?></label>
        <div class="relative">
            <input type="password"<?php echo $idAttr; ?> name="<?php echo $name; ?>" class="w-full bg-blue-50/50 border-none rounded-2xl px-6 py-4 font-bold text-gray-800 placeholder-gray-300 focus:ring-2 focus:ring-blue-600/20 transition-all outline-none pr-12" placeholder="<?php echo htmlspecialchars($placeholder); ?>"<?php echo $requiredAttr; ?>>
            <button type="button" onclick="togglePasswordField('<?php echo $toggleId; ?>')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-all">
                <span class="eye-open-<?php echo $toggleId; ?>"><?php echo get_icon('eye', 'w-5 h-5'); ?></span>
                <span class="eye-closed-<?php echo $toggleId; ?> hidden"><?php echo get_icon('eye-slash', 'w-5 h-5'); ?></span>
            </button>
        </div>
    </div>
    <script>
    function togglePasswordField(id) {
        const input = document.getElementById(id) || document.querySelector('input[type="password"]');
        const open = document.querySelector('.eye-open-' + id);
        const closed = document.querySelector('.eye-closed-' + id);
        if (input.type === 'password') {
            input.type = 'text';
            open?.classList.add('hidden');
            closed?.classList.remove('hidden');
        } else {
            input.type = 'password';
            open?.classList.remove('hidden');
            closed?.classList.add('hidden');
        }
    }
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Renders a form container with title and optional icon
 */

/**
 * Renders a form container with title and optional icon
 */
function render_form_container($title, $subtitle, $formHtml, $footerHtml = '', $iconHtml = '') {
    ob_start();
    ?>
    <div class="text-center mb-10">
        <?php if ($iconHtml): ?>
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl mb-6">
                <?php echo $iconHtml; ?>
            </div>
        <?php endif; ?>
        <h1 class="text-3xl font-black text-gray-800 tracking-tighter"><?php echo htmlspecialchars($title); ?></h1>
        <?php if ($subtitle): ?>
            <p class="text-gray-400 font-bold mt-2"><?php echo htmlspecialchars($subtitle); ?></p>
        <?php endif; ?>
    </div>
    <?php echo $formHtml; ?>
    <?php if ($footerHtml): ?>
        <div class="mt-8 text-center">
            <?php echo $footerHtml; ?>
        </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

/**
 * Renders an empty state placeholder
 */

/**
 * Renders an empty state placeholder
 */
function render_empty_state($iconName, $title, $description, $actionHtml = '') {
    ob_start();
    ?>
    <div class="bg-white rounded-[40px] p-20 text-center border border-gray-100 shadow-xl shadow-gray-200/30">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-gray-50 text-gray-200 rounded-full mb-10">
            <?php echo get_icon($iconName, 'w-12 h-12'); ?>
        </div>
        <h3 class="text-3xl font-black text-gray-800 mb-4 tracking-tight"><?php echo htmlspecialchars($title); ?></h3>
        <p class="text-gray-400 font-bold mb-10 max-w-sm mx-auto"><?php echo htmlspecialchars($description); ?></p>
        <?php echo $actionHtml; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a profile sidebar card
 */

/**
 * Renders a profile sidebar card
 */
function render_profile_sidebar($user) {
    ob_start();
    ?>
    <div class="bg-white rounded-[40px] p-8 border border-gray-100 shadow-2xl shadow-gray-200/40 sticky top-12 overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full pointer-events-none"></div>
        
        <div class="relative text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 text-white rounded-3xl mx-auto flex items-center justify-center font-black text-2xl mb-4 group transition-transform hover:scale-110">
                <?php echo strtoupper(mb_substr($user['name'], 0, 1)); ?>
            </div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tighter"><?php echo htmlspecialchars($user['name']); ?></h2>
            <p class="text-gray-400 font-bold text-sm mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="space-y-4">
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl group transition-all hover:bg-blue-50">
                <div class="text-blue-600"><?php echo get_icon('phone', 'w-5 h-5'); ?></div>
                <div class="text-sm font-black text-gray-800"><?php echo htmlspecialchars($user['phone']); ?></div>
            </div>
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl group transition-all hover:bg-red-50">
                <div class="text-red-500"><?php echo get_icon('log-out', 'w-5 h-5'); ?></div>
                <a href="/logout" class="text-sm font-black text-red-500">Выйти из системы</a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a booking history card
 */

/**
 * Renders a page section header
 */
function render_page_section_header($title, $subtitle = '') {
    ob_start();
    ?>
    <header class="mb-10">
        <h1 class="text-4xl font-black text-gray-800 tracking-tighter mb-4"><?php echo htmlspecialchars($title); ?></h1>
        <?php if ($subtitle): ?>
            <p class="text-gray-500 font-medium"><?php echo htmlspecialchars($subtitle); ?></p>
        <?php endif; ?>
        <div class="w-24 h-2 bg-blue-600 rounded-full mt-4"></div>
    </header>
    <?php
    return ob_get_clean();
}

/**
 * Renders a form submit button
 */

/**
 * Renders a form submit button
 */
function render_form_submit_button($text, $extraClasses = '') {
    return '<button type="submit" class="w-full bg-blue-600 text-white py-5 rounded-2xl font-black hover:bg-blue-700 transition-all active:scale-95 shadow-xl shadow-blue-600/20 uppercase tracking-widest text-xs ' . $extraClasses . '">' . htmlspecialchars($text) . '</button>';
}

/**
 * Renders a form footer with link
 */

/**
 * Renders a form footer with link
 */
function render_form_footer($text, $linkText, $linkUrl) {
    return '<p class="text-gray-400 text-sm font-bold">' . htmlspecialchars($text) . ' <a href="' . htmlspecialchars($linkUrl) . '" class="text-blue-600 font-black hover:underline">' . htmlspecialchars($linkText) . '</a></p>';
}

/**
 * Renders a feature/stat card with icon or number
 */

/**
 * Renders a feature/stat card with icon or number
 */
function render_feature_card($title, $description, $bgClass = 'bg-white/10', $extraClasses = '') {
    ob_start();
    ?>
    <div class="<?php echo $bgClass; ?> p-6 rounded-3xl backdrop-blur-md <?php echo $extraClasses; ?>">
        <div class="text-2xl font-black mb-2"><?php echo htmlspecialchars($title); ?></div>
        <p class="text-white/70"><?php echo htmlspecialchars($description); ?></p>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a hero banner with gradient background
 */

/**
 * Renders a hero banner with gradient background
 */
function render_hero_banner($title, $contentHtml, $bgClass = 'bg-blue-600', $extraClasses = '') {
    ob_start();
    ?>
    <div class="<?php echo $bgClass; ?> rounded-[40px] p-12 text-white mb-12 relative overflow-hidden <?php echo $extraClasses; ?>">
        <div class="relative z-10">
            <h2 class="text-3xl font-black mb-6"><?php echo htmlspecialchars($title); ?></h2>
            <?php echo $contentHtml; ?>
        </div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders an info message card
 */

/**
 * Renders an info message card
 */
function render_info_message($message, $extraClasses = '') {
    return '<div class="bg-white p-12 rounded-[40px] shadow-sm border border-gray-100 italic text-gray-500 text-center ' . $extraClasses . '">' . htmlspecialchars($message) . '</div>';
}

/**
 * Renders an info card with icon
 */

/**
 * Renders an info card with icon
 */
function render_info_card($iconName, $title, $description, $iconBgClass = 'bg-blue-50', $iconTextClass = 'text-blue-600') {
    ob_start();
    ?>
    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-blue-50 transition-colors">
        <div class="<?php echo $iconBgClass; ?> <?php echo $iconTextClass; ?> p-3 rounded-xl shrink-0">
            <?php echo get_icon($iconName, 'w-6 h-6'); ?>
        </div>
        <div class="text-left">
            <div class="font-black text-gray-800 mb-1"><?php echo htmlspecialchars($title); ?></div>
            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($description); ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a list of features with icons
 */

/**
 * Renders a list of features with icons
 */
function render_feature_list($items, $columns = 2) {
    $colClass = 'grid-cols-1';
    if ($columns === 2) $colClass = 'grid-cols-1 md:grid-cols-2';
    if ($columns === 3) $colClass = 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3';
    if ($columns === 4) $colClass = 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4';
    
    ob_start();
    ?>
    <div class="grid <?php echo $colClass; ?> gap-6">
        <?php foreach ($items as $item): ?>
            <?php echo render_info_card(
                $item['icon'] ?? 'check',
                $item['title'],
                $item['description'],
                $item['iconBgClass'] ?? 'bg-green-50',
                $item['iconTextClass'] ?? 'text-green-500'
            ); ?>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a price card with details
 */

/**
 * Renders a price card with details
 */
function render_price_card($price, $period, $features = [], $highlighted = false, $buttonText = 'Выбрать', $buttonLink = '#') {
    $cardClass = $highlighted ? 'bg-blue-600 text-white border-blue-600 scale-105' : 'bg-white text-gray-800 border-gray-100';
    $priceClass = $highlighted ? 'text-white' : 'text-gray-800';
    $featureClass = $highlighted ? 'text-white/80' : 'text-gray-600';
    $buttonClass = $highlighted ? 'bg-white text-blue-600 hover:bg-gray-100' : 'bg-blue-600 text-white hover:bg-blue-700';
    
    ob_start();
    ?>
    <div class="<?php echo $cardClass; ?> rounded-[32px] p-8 border shadow-xl transition-all hover:scale-105">
        <div class="text-center mb-8">
            <div class="text-5xl font-black <?php echo $priceClass; ?> mb-2">฿<?php echo (int)$price; ?></div>
            <div class="text-sm font-bold <?php echo $featureClass; ?> uppercase tracking-widest"><?php echo htmlspecialchars($period); ?></div>
        </div>
        
        <?php if (!empty($features)): ?>
            <ul class="space-y-4 mb-8">
                <?php foreach ($features as $feature): ?>
                    <li class="flex items-center gap-3">
                        <div class="shrink-0"><?php echo get_icon('check', 'w-5 h-5 ' . ($highlighted ? 'text-white' : 'text-green-500')); ?></div>
                        <span class="<?php echo $featureClass; ?> text-sm"><?php echo htmlspecialchars($feature); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="block text-center <?php echo $buttonClass; ?> px-6 py-4 rounded-2xl font-black transition-all active:scale-95 uppercase tracking-widest text-xs">
            <?php echo htmlspecialchars($buttonText); ?>
        </a>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a stat card with icon and number
 */

/**
 * Renders a stat card with icon and number
 */
function render_stat_card($value, $label, $iconName = '', $bgClass = 'bg-blue-50', $iconTextClass = 'text-blue-600', $valueClass = 'text-gray-800') {
    ob_start();
    ?>
    <div class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm hover:shadow-md transition-all">
        <?php if ($iconName): ?>
            <div class="<?php echo $bgClass; ?> <?php echo $iconTextClass; ?> w-12 h-12 rounded-2xl flex items-center justify-center mb-4">
                <?php echo get_icon($iconName, 'w-6 h-6'); ?>
            </div>
        <?php endif; ?>
        <div class="text-3xl font-black <?php echo $valueClass; ?> mb-1"><?php echo htmlspecialchars($value); ?></div>
        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest"><?php echo htmlspecialchars($label); ?></div>
    </div>
    <?php
    return ob_get_clean();
}
