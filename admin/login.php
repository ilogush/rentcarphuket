<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/components.php';
require_once __DIR__ . '/auth.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

use function Components\component;

// If already logged in, go to admin
if (is_logged_in()) {
    header("Location: /admin");
    exit;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (empty($csrfToken) || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['login_error'] = 'Ошибка безопасности. Обновите страницу и попробуйте снова.';
        header("Location: /admin/login");
        exit;
    }
    
    if (check_auth($_POST['username'] ?? '', $_POST['password'] ?? '')) {
        header("Location: /admin");
        exit;
    } else {
        if (is_login_locked()) {
            $retryAfter = ceil((($_SESSION['login_locked_until'] ?? 0) - time()) / 60);
            $_SESSION['login_error'] = "Слишком много неудачных попыток входа. Попробуйте через {$retryAfter} мин.";
        } else {
            $attemptsLeft = MAX_LOGIN_ATTEMPTS - ($_SESSION['login_attempts'] ?? 0);
            $_SESSION['login_error'] = "Неверный логин или пароль. Осталось попыток: {$attemptsLeft}";
        }
        header("Location: /admin/login");
        exit;
    }
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" href="/assets/css/tailwind.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8">
            <!-- Logo/Title -->
            <div class="text-center">
                <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight">
                    Вход в админ-панель
                </h1>
                <p class="mt-2 text-sm text-gray-500 font-medium">
                    Введите свои учетные данные для доступа
                </p>
            </div>
            
            <!-- Error Alert -->
            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4" role="alert">
                    <div class="flex items-start gap-3">
                        <div class="text-red-800 shrink-0">
                            <?php echo get_icon('alert-circle', 'w-5 h-5'); ?>
                        </div>
                        <p class="text-red-800 text-sm font-medium">
                            <?php echo htmlspecialchars($error); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Login Form Card -->
            <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-8">
                    <form action="/admin/login" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <!-- Username Field -->
                        <div class="space-y-2">
                            <label for="username" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                Логин
                            </label>
                            <div class="relative">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400">
                                    <?php echo get_icon('user', 'w-5 h-5'); ?>
                                </div>
                                <input 
                                    type="text" 
                                    id="username"
                                    name="username"
                                    required
                                    autocomplete="username"
                                    class="w-full bg-gray-100 border border-gray-200 rounded-2xl pl-14 pr-6 py-4 font-bold text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all"
                                    placeholder="Введите логин"
                                >
                            </div>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password" class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                Пароль
                            </label>
                            <div class="relative">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400">
                                    <?php echo get_icon('lock', 'w-5 h-5'); ?>
                                </div>
                                <input 
                                    type="password" 
                                    id="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    class="w-full bg-gray-100 border border-gray-200 rounded-2xl pl-14 pr-14 py-4 font-bold text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all"
                                    placeholder="Введите пароль"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePasswordVisibility()"
                                    class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                                    aria-label="Показать пароль"
                                >
                                    <span id="eye-open" class="block">
                                        <?php echo get_icon('eye', 'w-5 h-5'); ?>
                                    </span>
                                    <span id="eye-closed" class="hidden">
                                        <?php echo get_icon('eye-slash', 'w-5 h-5'); ?>
                                    </span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl transition-all shadow-lg shadow-blue-600/30 active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-sm"
                            >
                                <span>Войти</span>
                                <span class="bg-white/20 p-1.5 rounded-full">
                                    <?php echo get_icon('arrow-right', 'w-4 h-4'); ?>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Back Link -->
            <div class="text-center">
                <a href="/" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800 font-medium transition-colors">
                    <?php echo get_icon('arrow-left', 'w-4 h-4'); ?>
                    <span>Вернуться на главную</span>
                </a>
            </div>
        </div>
    </div>
    
    <script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
    
    // Focus first input on load
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('username')?.focus();
    });
    </script>
</body>
</html>
