<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/icons.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/sidebar.php';

$userHandler = __DIR__ . '/actions/handle_users.php';
require_once $userHandler;

$bookingRepo = new \App\Repositories\BookingRepository();

$bookings = $bookingRepo->getAll();

$userBookings = [];
foreach($bookings as $b) {
    if(!isset($userBookings[$b['user_id']])) $userBookings[$b['user_id']] = 0;
    $userBookings[$b['user_id']]++;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи | Rent Car Phuket</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
</head>
<body class="flex flex-col min-h-screen bg-gray-100 antialiased" style="font-family: system-ui, -apple-system, sans-serif;">
    <div class="flex h-screen overflow-hidden bg-gray-50">
        <?php render_sidebar('users'); ?>

        <div class="flex-1 overflow-y-auto">
            <?php render_topbar('Поиск по пользователям...'); ?>

            <main class="p-6">
                <div class="max-w-[1600px] mx-auto">
                    <?php echo render_admin_page_header('Пользователи'); ?>

                <?php echo render_admin_toast_messages([]); ?>

                    <?php 
                    ob_start();
                    foreach (array_reverse($users) as $user) {
                        $bookingCount = $userBookings[$user['id']] ?? 0;
                        echo render_admin_user_row($user, $bookingCount);
                    }
                    $rowsHtml = ob_get_clean();

                    echo render_admin_table(
                        ['Клиент', 'Контакты', 'История', 'Регистрация', ''],
                        $rowsHtml
                    );
                    ?>
            </main>
        </div>
    </div>

    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; border: 3px solid transparent; background-clip: content-box; }
        ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; border-radius: 10px; border: 3px solid transparent; background-clip: content-box; }
    </style>

    <?php
        $userModalBody = '
            <form id="user-form" class="space-y-6">
                <input type="hidden" name="user_id" id="field-user-id">
                <input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">
                <input type="hidden" name="save_user" value="1">
                <input type="hidden" name="registered_at" id="field-registered-at">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    ' . render_admin_field('ФИО Клиента', '<input type="text" name="name" id="field-name" class="w-full bg-gray-50/50 border border-gray-100 rounded-3xl px-6 py-4 font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all" required>') . '
                    ' . render_admin_field('Телефон', '<input type="tel" name="phone" id="field-phone" class="w-full bg-gray-50/50 border border-gray-100 rounded-3xl px-6 py-4 font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all" required>') . '
                    ' . render_admin_field('Email', '<input type="email" name="email" id="field-email" class="w-full bg-gray-50/50 border border-gray-100 rounded-3xl px-6 py-4 font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all" required>', 'md:col-span-2 space-y-2') . '
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-6">
                    ' . render_admin_danger_button('Удалить клиента', 'onclick="deleteUser()"') . '
                    <div class="flex-1"></div>
                    ' . render_admin_submit_button('Сохранить', 'id="save-user-btn" class="px-10 py-4 min-h-[52px]"', 'btn-text', 'btn-loader hidden', true) . '
                </div>
            </form>
        ';
        echo render_admin_modal_shell('user-modal', 'Параметры клиента', '<button onclick="closeUserModal()" class="text-gray-300 hover:text-gray-800 transition-colors p-2">' . get_icon('x', 'w-6 h-6') . '</button>', $userModalBody);
    ?>

    <script>
        let currentUserRow = null;

        function editUser(button) {
            const user = JSON.parse(button.dataset.user);
            document.getElementById('field-user-id').value = user.id;
            document.getElementById('field-name').value = user.name;
            document.getElementById('field-phone').value = user.phone;
            document.getElementById('field-email').value = user.email;
            document.getElementById('field-registered-at').value = user.registered_at;
            currentUserRow = button.closest('tr');

            document.getElementById('user-modal').classList.remove('hidden');
            document.getElementById('user-modal').classList.add('flex');
        }

        function closeUserModal() {
            document.getElementById('user-modal').classList.add('hidden');
            document.getElementById('user-modal').classList.remove('flex');
            currentUserRow = null;
        }

        async function handleAction(formData) {
            const btn = document.getElementById('save-user-btn');
            const text = btn.querySelector('.btn-text');
            const loader = btn.querySelector('.btn-loader');
            
            btn.disabled = true;
            text.classList.add('hidden');
            loader.classList.remove('hidden');

            try {
                const response = await fetch(window.location.pathname, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message || 'Данные клиента сохранены.', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.error || 'Не удалось сохранить клиента.', 'error');
                }
            } catch (error) { console.error(error); showToast('Не удалось подключиться к серверу.', 'error'); }
            finally {
                btn.disabled = false;
                text.classList.remove('hidden');
                loader.classList.add('hidden');
                closeUserModal();
            }
        }

        document.getElementById('user-form').addEventListener('submit', (e) => {
            e.preventDefault();
            handleAction(new FormData(e.target));
        });

        async function deleteUser() {
            openConfirmModal('confirm-modal', 'Удалить клиента?', 'Вы уверены? Это удалит клиента из базы.', () => {
                const formData = new FormData();
                formData.append('delete_user', '1');
                formData.append('user_id', document.getElementById('field-user-id').value);
                formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
                handleAction(formData);
            });
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeUserModal(); });
    </script>

    <style>
        @keyframes fade-in-down {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fade-in-down 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    </style>
    <?php echo render_confirm_modal(); ?>
</body>
</html>
