<?php
declare(strict_types=1);

function sidebar_counts(): array {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = ['cars' => 0, 'bookings' => 0, 'users' => 0, 'locations' => 0, 'durations' => 0, 'seasons' => 0];
    try {
        $repos = [
            'cars'      => \App\Repositories\CarRepository::class,
            'bookings'  => \App\Repositories\BookingRepository::class,
            'locations' => \App\Repositories\LocationRepository::class,
            'durations' => \App\Repositories\DurationRepository::class,
            'seasons'   => \App\Repositories\SeasonRepository::class,
            'users'     => \App\Repositories\UserRepository::class,
        ];
        foreach ($repos as $key => $class) {
            $cache[$key] = count((new $class())->getAll());
        }
    } catch (\Throwable $e) {}
    return $cache;
}

function render_sidebar($active = 'cars') {
    $counts = sidebar_counts();

    $items = [
        ['id' => 'bookings', 'name' => 'Бронирования', 'url' => '/admin/bookings', 'icon' => 'calendar'],
        ['id' => 'cars', 'name' => 'Автопарк', 'url' => '/admin', 'icon' => 'car'],
        ['id' => 'users', 'name' => 'Пользователи', 'url' => '/admin/users', 'icon' => 'users'],
        ['id' => 'locations', 'name' => 'Локации', 'url' => '/admin/locations', 'icon' => 'map-pin'],
        ['id' => 'durations', 'name' => 'Длительность', 'url' => '/admin/durations', 'icon' => 'clock'],
        ['id' => 'seasons', 'name' => 'Сезонность', 'url' => '/admin/seasons', 'icon' => 'sun'],
        ['id' => 'profile', 'name' => 'Профиль', 'url' => '/admin/profile', 'icon' => 'users'],
    ];
    $logoSrc = function_exists('asset_url') ? asset_url('/assets/images/monkey-logo.webp') : '/assets/images/monkey-logo.webp';
    ?>
    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-[90] hidden lg:hidden"></div>

    <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-72 bg-white border-r border-gray-100 flex flex-col gap-2 p-6 shrink-0 text-sm h-screen z-[100] transition-transform duration-300 -translate-x-full lg:translate-x-0 lg:sticky lg:top-0">
        <div class="mb-10 px-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 text-gray-800 block">
                <span class="w-10 h-10 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex items-center justify-center">
                    <img src="<?php echo e($logoSrc); ?>" alt="" width="40" height="40" loading="lazy" decoding="async" class="w-full h-full object-cover">
                </span>
                <span class="flex flex-col leading-none">
                    <span class="text-lg font-black tracking-tight">MONKEYCAR</span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">PHUKET · TH</span>
                </span>
            </a>
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-gray-800 p-2">
                <?php echo get_icon('x', 'w-6 h-6'); ?>
            </button>
        </div>
        <nav class="space-y-1">
            <?php foreach($items as $item): ?>
                <?php 
                $isActive = $active === $item['id']; 
                $isLogout = $item['id'] === 'logout';
                ?>
                <?php echo render_admin_sidebar_item($item, $counts, $active); ?>
            <?php endforeach; ?>
        </nav>
    </aside>
    <?php
}

function render_topbar($searchPlaceholder = 'Поиск данных...') {
    ?>
    <header class="h-20 flex items-center justify-between px-6 z-50">
        <div class="flex items-center gap-6 flex-1">
            <button onclick="toggleSidebar()" class="p-2 text-gray-400 hover:text-gray-800 transition-all active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25" />
                </svg>
            </button>
            <div class="max-w-md w-full relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 transition-colors">
                    <?php echo get_icon('search', 'w-4 h-4'); ?>
                </div>
                <input type="text" id="admin-search-input" placeholder="<?php echo $searchPlaceholder; ?>" onkeyup="filterAdminTable(this.value)" class="w-full bg-white border border-gray-100 rounded-2xl pl-11 pr-6 py-3 font-bold text-sm text-gray-800 placeholder-gray-300 focus:border-yellow-400 transition-all outline-none">
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative" id="user-profile-menu">
                <button onclick="toggleUserDropdown()" class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-gray-50 transition-all active:scale-95 group">
                    <div class="text-left hidden sm:block">
                        <div class="text-sm font-black text-gray-800 leading-none mb-1"><?php echo $_SESSION['admin_name'] ?? 'Админ'; ?></div>
                        <div class="text-[10px] font-black text-gray-400 tracking-wide">
                            <?php echo $_SESSION['admin_email'] ?? ''; ?>
                        </div>
                    </div>
                    <div class="text-gray-300 group-hover:text-gray-600 transition-colors">
                        <?php echo get_icon('chevron-down', 'w-4 h-4'); ?>
                    </div>
                </button>
                
                <div id="user-dropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-[24px] border border-gray-100 shadow-2xl p-2 hidden scale-95 opacity-0 origin-top-right transition-all duration-200">
                    <?php echo render_admin_dropdown_item('/admin/profile', 'users', 'Профиль', 'text-gray-800 group-hover:text-gray-800', 'bg-gray-100 text-gray-400'); ?>
                    <div class="h-px bg-gray-50 my-1 mx-2"></div>
                    <?php echo render_admin_dropdown_item('/admin?logout=1', 'logout', 'Выйти', 'text-red-500', 'bg-red-50 text-red-400'); ?>
                </div>
            </div>
        </div>
    </header>
    
    <script>
    function toggleUserDropdown() {
        const dropdown = document.getElementById('user-dropdown');
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.replace('scale-95', 'scale-100');
                dropdown.classList.replace('opacity-0', 'opacity-100');
            }, 10);
        } else {
            dropdown.classList.replace('scale-100', 'scale-95');
            dropdown.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => dropdown.classList.add('hidden'), 200);
        }
    }
    
    function toggleSidebar() {
        const aside = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (window.innerWidth >= 1024) {
            aside.classList.toggle('lg:hidden');
        } else {
            aside.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }
    }

    function filterAdminTable(query) {
        query = query.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    }

    document.addEventListener('click', (e) => {
        const menu = document.getElementById('user-profile-menu');
        if (menu && !menu.contains(e.target)) {
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) toggleUserDropdown();
        }
    });
    </script>
    <?php
}
?>
