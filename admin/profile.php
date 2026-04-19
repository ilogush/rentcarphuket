<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';
require_once __DIR__ . '/actions/handle_profile.php';

$fields = 
    render_admin_field('Имя администратора', render_admin_input('name', $admin['name'] ?? '', 'text', '', '', 'required')) .
    render_admin_field('Email компании', render_admin_input('email', $admin['email'] ?? '', 'email', '', '', 'required')) .
    render_admin_field('Телефон компании', render_admin_input('phone', $admin['phone'] ?? '', 'text', '', '', 'required')) .
    render_admin_field('Telegram', render_admin_input('telegram', $admin['telegram'] ?? '', 'text', '', 'https://t.me/ilogush', '')) .
    render_admin_field('Логин админа', render_admin_input('username', $admin['username'] ?? '', 'text', '', '', 'required readonly', 'style="background-color: rgba(249, 250, 251, 0.5); opacity: 0.6; cursor: not-allowed;"')) .
    
    render_admin_field('Адрес (в футере)', render_admin_input('address', $admin['address'] ?? '', 'text', '', '', 'required')) .
    render_admin_password_field('new_password', 'Новый пароль', 'new_password') .
    render_admin_password_field('confirm_password', 'Подтверждение пароля', 'confirm_password') .
    
    '';

$formContent = '
    <form id="profile-form" method="POST">
        <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
        <input type="hidden" name="save_profile" value="1">
        ' . render_admin_form_body(render_admin_form_grid($fields)) . '
        ' . render_admin_form_actions('save-profile-btn', '', 'Сохранить') . '
    </form>
';

$footerHtml = '';
$cardHtml = render_admin_form_card('Настройки профиля', '/admin', $formContent, $footerHtml);

$footerScripts = '
<script>
    document.getElementById("profile-form").addEventListener("submit", async (e) => {
        e.preventDefault();
        const btn = document.getElementById("save-profile-btn");
        const text = btn?.querySelector(".save-profile-btn-text");
        const loader = btn?.querySelector(".save-profile-btn-loader");
        
        if (btn) btn.disabled = true;
        if (text) text.classList.add("hidden");
        if (loader) loader.classList.remove("hidden");

        try {
            const response = await fetch(window.location.pathname, {
                method: "POST",
                body: new FormData(e.target),
                headers: { "X-Requested-With": "XMLHttpRequest" }
            });
            const result = await response.json();
            if (result.success) {
                showToast(result.message || "Профиль сохранён.", "success");
            } else {
                showToast(result.error || "Не удалось сохранить профиль.", "error");
            }
        } catch (error) { 
            console.error(error); 
            showToast("Не удалось подключиться к серверу.", "error");
        } finally {
            if (btn) btn.disabled = false;
            if (text) text.classList.remove("hidden");
            if (loader) loader.classList.add("hidden");
        }
    });

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const open = btn.querySelector(".eye-open");
        const closed = btn.querySelector(".eye-closed");
        if (input.type === "password") {
            input.type = "text";
            open.classList.add("hidden");
            closed.classList.remove("hidden");
        } else {
            input.type = "password";
            open.classList.remove("hidden");
            closed.classList.add("hidden");
        }
    }
</script>
';

render_admin_page(
    'profile',
    'Профиль администратора',
    'Настройки профиля',
    '',
    $cardHtml,
    $footerScripts,
    [
        'error' => [
            'csrf' => ['message' => 'Ошибка проверки безопасности. Повторите действие.', 'type' => 'error'],
            'save' => ['message' => 'Не удалось сохранить профиль.', 'type' => 'error'],
        ],
        'success' => [
            'saved' => ['message' => 'Профиль сохранён.', 'type' => 'success'],
        ],
    ]
);
