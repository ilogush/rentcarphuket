<?php
declare(strict_types=1);


/**
 * Renders the Hero section
 */
function render_hero($title, $subtitle, $bgImage, $locations = []) {
    global $cars;
    $minPrice = 0;
    if (is_array($cars ?? null) && !empty($cars)) {
        $prices = array_map(static fn($car) => (int)($car['price'] ?? 0), $cars);
        $prices = array_filter($prices);
        $minPrice = !empty($prices) ? min($prices) : 0;
    }
    ob_start();
    ?>
    <section class="mc-hero">
        <div class="mc-hero-bg" style="--mc-hero-image: url('<?php echo e($bgImage); ?>');"></div>
        <div class="mc-hero-fade"></div>
        <div class="mc-container mc-hero-body">
            <div class="mc-hero-grid">
                <div class="mc-hero-copy">
                    <div class="mc-eyebrow"><span class="mc-eyebrow-dot"></span>ПХУКЕТ · АВТО НА КАЖДЫЙ ДЕНЬ</div>
                    <h1 class="mc-hero-title">
                        <?php echo e($title); ?><br>
                        <?php if ($minPrice): ?>
                            от <em><?php echo number_format($minPrice, 0, '.', ' '); ?> ฿</em><br>
                        <?php endif; ?>
                        <span class="mc-stroke">без суеты.</span>
                    </h1>
                    <p class="mc-hero-sub"><?php echo e($subtitle); ?>. Выберите район, даты и авто. Итоговая стоимость пересчитается до заявки.</p>
                    <div class="mc-hero-ctas">
                        <a class="mc-btn mc-btn-yellow mc-btn-lg" href="#cars-section">Выбрать машину <?php echo get_icon('arrow-right', 'w-4 h-4'); ?></a>
                        <a class="mc-btn mc-btn-ghost mc-btn-lg" href="/terms">Условия аренды</a>
                    </div>
                </div>
                <div class="mc-hero-booking">
                    <?php echo render_search_box($locations); ?>
                </div>
            </div>
            <div class="mc-hero-stats">
                <div class="mc-hero-stat">
                    <div class="mc-hero-stat-accent"></div>
                    <div class="num">24/7</div>
                    <div class="lbl">Telegram и WhatsApp</div>
                </div>
                <div class="mc-hero-stat">
                    <div class="mc-hero-stat-accent"></div>
                    <div class="num"><?php echo count($cars ?? []); ?>+</div>
                    <div class="lbl">машин в наличии</div>
                </div>
                <div class="mc-hero-stat">
                    <div class="mc-hero-stat-accent"></div>
                    <div class="num">15</div>
                    <div class="lbl">минут до ответа</div>
                </div>
                <div class="mc-hero-stat">
                    <div class="mc-hero-stat-accent"></div>
                    <div class="num">0 ฿</div>
                    <div class="lbl">скрытых платежей</div>
                </div>
                <div class="mc-hero-stat">
                    <div class="mc-hero-stat-accent"></div>
                    <div class="num">100%</div>
                    <div class="lbl">договор + страховка</div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Renders the Search Box component
 */

/**
 * Renders the Search Box component
 */
function render_search_box($locations = []) {
    if (empty($locations)) {
        global $locations; // fallback
    }
    ob_start();
    ?>
    <section class="mc-book-panel">
        <div class="mc-book-panel-inner">
            <div class="mc-book-panel-head">
                <div class="mc-book-panel-kicker">Поиск авто</div>
                <h2 class="mc-book-panel-title">Подбор за 30 секунд</h2>
                <p class="mc-book-panel-sub">Укажите место и даты. В каталоге появится цена за весь период.</p>
            </div>
            <div class="mc-book-panel-form">
                <div class="mc-book-field" id="location-select-container">
                    <span class="mc-label">Место подачи</span>
                    <div class="mc-book-value mc-location-trigger" onclick="toggleLocationDropdown(event)">
                        <?php echo get_icon('map-pin', 'w-4 h-4'); ?>
                        <span id="selected-location">Выберите место</span>
                        <input type="hidden" name="pickup_location" id="pickup_location_input" value="">
                    </div>
                    <div id="location-dropdown" class="mc-dropdown hidden">
                        <?php foreach($locations as $loc):
                            $locName = is_array($loc) ? $loc['name'] : $loc;
                        ?>
                            <div class="mc-dropdown-item" onclick="selectLocation('<?php echo htmlspecialchars($locName, ENT_QUOTES, 'UTF-8'); ?>')">
                                <?php echo e($locName); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mc-book-field">
                    <span class="mc-label">Дата получения</span>
                    <div class="mc-book-value">
                        <?php echo get_icon('calendar', 'w-4 h-4'); ?>
                        <input type="text" id="pickup_date" value="<?php echo date('d.m.Y'); ?>">
                    </div>
                </div>

                <div class="mc-book-field">
                    <span class="mc-label">Дата возврата</span>
                    <div class="mc-book-value">
                        <?php echo get_icon('calendar', 'w-4 h-4'); ?>
                        <input type="text" id="return_date" value="<?php echo date('d.m.Y', strtotime('+3 days')); ?>">
                    </div>
                </div>

                <div class="mc-book-submit">
                    <button type="button" onclick="handleSearchSubmit()" class="mc-btn mc-btn-yellow mc-btn-lg">
                        <?php echo get_icon('search', 'w-4 h-4'); ?>
                        Найти авто
                    </button>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Renders a unified Date Range Picker
 */

/**
 * Renders a section heading
 */

/**
 * Renders trust and service details for the home page.
 */
function render_home_trust_section() {
    global $contactInfo;
    $whatsapp = $contactInfo['socialMedia']['whatsapp'] ?? '#';
    $telegram = $contactInfo['socialMedia']['telegram'] ?? '#';
    ob_start();
    ?>
    <section class="mc-section mc-trust-section" id="service">
        <div class="mc-container">
            <div class="mc-section-head">
                <div>
                    <div class="mc-section-eyebrow">[ 02 ] - УСЛОВИЯ</div>
                    <h2 class="mc-section-title">Понятная <em>аренда</em></h2>
                    <p class="mc-section-sub">Перед заявкой видно цену, депозит и район доставки. Условия страховки и возврата фиксируются в договоре.</p>
                </div>
                <div class="mc-result-count">Поддержка каждый день · ответы в мессенджерах</div>
            </div>

            <div class="mc-trust-grid">
                <article class="mc-trust-card">
                    <div class="mc-trust-icon"><?php echo get_icon('check', 'w-5 h-5'); ?></div>
                    <h3>Цена до заявки</h3>
                    <p>Калькулятор учитывает даты, скидки, срок аренды, подачу и возврат авто.</p>
                </article>
                <article class="mc-trust-card">
                    <div class="mc-trust-icon"><?php echo get_icon('lock', 'w-5 h-5'); ?></div>
                    <h3>Договор и депозит</h3>
                    <p>Залог указан в карточке. Страховка, франшиза и исключения согласуются до выдачи.</p>
                </article>
                <article class="mc-trust-card">
                    <div class="mc-trust-icon"><?php echo get_icon('phone', 'w-5 h-5'); ?></div>
                    <h3>Связь во время аренды</h3>
                    <p>Если меняется маршрут, время возврата или нужна помощь, менеджер остаётся на связи.</p>
                </article>
            </div>

            <div class="mc-service-strip">
                <img src="<?php echo asset_image_url('about.webp'); ?>" alt="Дорога на Пхукете" loading="lazy" decoding="async">
                <div>
                    <h3>Что подготовить к получению</h3>
                    <p>Паспорт, водительское удостоверение и время встречи. Если нужен детский кресло, отельная подача или встреча в аэропорту, укажите это в комментарии к заявке.</p>
                    <div class="mc-hero-ctas">
                        <a class="mc-btn mc-btn-yellow" href="<?php echo e($whatsapp); ?>"><?php echo get_icon('whatsapp', 'w-4 h-4'); ?> WhatsApp</a>
                        <a class="mc-btn mc-btn-ghost" href="<?php echo e($telegram); ?>"><?php echo get_icon('telegram', 'w-4 h-4'); ?> Telegram</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Renders a compact stat tile for car details
 */

/**
 * Renders common scripts for the home page (datepicker, location search, sorting)
 */
function render_home_scripts() {
    ob_start();
    ?>
    <script>
    // Search params storage
    const SearchParams = {
        save() {
            const params = {
                pickup_location: document.getElementById('pickup_location_input')?.value || '',
                pickup_date: document.getElementById('pickup_date')?.value || '',
                return_date: document.getElementById('return_date')?.value || ''
            };
            sessionStorage.setItem('searchParams', JSON.stringify(params));
            return params;
        },
        load() {
            const stored = sessionStorage.getItem('searchParams');
            return stored ? JSON.parse(stored) : null;
        },
        clear() {
            sessionStorage.removeItem('searchParams');
        }
    };

    function parseRuDate(value) {
        if (!value) return null;
        const parts = value.split('.');
        if (parts.length !== 3) return null;
        const date = new Date(Number(parts[2]), Number(parts[1]) - 1, Number(parts[0]));
        return Number.isNaN(date.getTime()) ? null : date;
    }

    function calculateRentalDays(pickupDate, returnDate) {
        const start = parseRuDate(pickupDate);
        const end = parseRuDate(returnDate);
        if (!start || !end) return 1;
        return Math.max(1, Math.ceil((end - start) / (1000 * 60 * 60 * 24)));
    }

    function formatBaht(amount) {
        return `${Math.round(Number(amount) || 0).toLocaleString('ru-RU')} ฿`;
    }

    function syncCatalogWithSearch() {
        setCatalogSearchSummary();
        updateCatalogPrices();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const hasSearchBox = document.getElementById('pickup_date') && document.getElementById('return_date');
        if (!hasSearchBox || typeof flatpickr !== 'function') {
            const sorter = document.getElementById('car-sorter');
            if (sorter && sorter.value !== 'price') {
                sorter.value = 'price';
            }
            if (sorter && typeof sortCars === 'function') {
                sortCars();
            }
            syncCatalogWithSearch();
            return;
        }

        // Datepicker configuration
        const config = {
            locale: 'ru',
            dateFormat: 'd.m.Y',
            minDate: 'today',
            disableMobile: true,
            allowInput: false,
            monthSelectorType: 'static',
            position: 'auto center',
            appendTo: document.body,
            onChange: function() {
                SearchParams.save();
                syncCatalogWithSearch();
            }
        };
        
        // Инициализация датапикера для даты подачи
        const pickupPicker = flatpickr("#pickup_date", { 
            ...config, 
            defaultDate: new Date(),
            onChange: function(selectedDates) {
                if (selectedDates.length > 0) {
                    // Устанавливаем минимальную дату возврата = дата подачи + 1 день
                    const minReturnDate = new Date(selectedDates[0]);
                    minReturnDate.setDate(minReturnDate.getDate() + 1);
                    returnPicker.set('minDate', minReturnDate);
                    
                    // Если текущая дата возврата меньше новой минимальной, обновляем её
                    const currentReturn = returnPicker.selectedDates[0];
                    if (!currentReturn || currentReturn < minReturnDate) {
                        returnPicker.setDate(minReturnDate);
                    }
                }
                SearchParams.save();
                syncCatalogWithSearch();
            }
        });
        
        // Инициализация датапикера для даты возврата
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const defaultReturnDate = new Date();
        defaultReturnDate.setDate(defaultReturnDate.getDate() + 3);
        
        const returnPicker = flatpickr("#return_date", { 
            ...config, 
            defaultDate: defaultReturnDate,
            minDate: tomorrow
        });
        
        // Устанавливаем первую локацию из справочника по умолчанию.
        const defaultLocation = document.querySelector('#location-dropdown .mc-dropdown-item')?.textContent.trim() || '';
        
        // Load saved params if exist
        const saved = SearchParams.load();
        if (saved && saved.pickup_location) {
            selectLocation(saved.pickup_location);
        } else if (defaultLocation) {
            // Если нет сохраненных данных, устанавливаем аэропорт по умолчанию
            selectLocation(defaultLocation);
        }

        const sorter = document.getElementById('car-sorter');
        if (sorter && sorter.value !== 'price') {
            sorter.value = 'price';
        }
        if (sorter && typeof sortCars === 'function') {
            sortCars();
        }
        syncCatalogWithSearch();
    });

    // Location Dropdown Logic
    function toggleLocationDropdown(e) {
        if (e) e.stopPropagation();
        const dropdown = document.getElementById('location-dropdown');
        dropdown.classList.toggle('hidden');
    }

    function selectLocation(loc) {
        const text = document.getElementById('selected-location');
        const input = document.getElementById('pickup_location_input');
        if(text) text.innerText = loc;
        if(input) input.value = loc;
        const dropdown = document.getElementById('location-dropdown');
        if(dropdown) dropdown.classList.add('hidden');
        SearchParams.save();
        syncCatalogWithSearch();
    }

    function handleSearchSubmit() {
        const params = SearchParams.save();
        
        // Validate location
        if (!params.pickup_location) {
            if (typeof showToast === 'function') {
                showToast('Выберите место подачи автомобиля', 'error');
            } else {
                console.error('Выберите место подачи автомобиля');
            }
            return;
        }

        // Validate dates
        const pickupDate = document.getElementById('pickup_date')?._flatpickr?.selectedDates[0];
        const returnDate = document.getElementById('return_date')?._flatpickr?.selectedDates[0];
        
        if (!pickupDate || !returnDate) {
            if (typeof showToast === 'function') {
                showToast('Выберите даты аренды', 'error');
            } else {
                console.error('Выберите даты аренды');
            }
            return;
        }

        // Check that return date is after pickup date
        if (returnDate <= pickupDate) {
            if (typeof showToast === 'function') {
                showToast('Дата возврата должна быть позже даты подачи', 'error');
            } else {
                console.error('Дата возврата должна быть позже даты подачи');
            }
            return;
        }

        // Check minimum rental period (1 day)
        const diffTime = Math.abs(returnDate - pickupDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (diffDays < 1) {
            if (typeof showToast === 'function') {
                showToast('Минимальный срок аренды: 1 день', 'error');
            } else {
                console.error('Минимальный срок аренды: 1 день');
            }
            return;
        }

        // Scroll to cars section
        const carsSection = document.getElementById('cars-section');
        if (carsSection) {
            carsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        syncCatalogWithSearch();
    }

    function handleCarSelect(event, carId) {
        event.preventDefault();
        const params = SearchParams.load();
        
        let url = `/car/${carId}`;
        if (params && params.pickup_location) {
            const query = new URLSearchParams({
                pickup_area: params.pickup_location,
                dropoff_area: params.pickup_location,
                pickup_date: params.pickup_date,
                return_date: params.return_date
            });
            url += '?' + query.toString();
        }
        
        window.location.href = url;
    }

    document.addEventListener('click', function(e) {
        const container = document.getElementById('location-select-container');
        const dropdown = document.getElementById('location-dropdown');
        if (container && !container.contains(e.target) && dropdown) {
            dropdown.classList.add('hidden');
        }
    });

    function scrollToSearchBox() {
        const panel = document.querySelector('.mc-book-panel');
        if (panel) panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setCatalogSearchSummary() {
        const params = SearchParams.load();
        const summary = document.getElementById('catalog-search-summary');
        const text = document.getElementById('catalog-search-summary-text');
        if (!summary || !text || !params || !params.pickup_location) return;

        const days = calculateRentalDays(params.pickup_date, params.return_date);
        text.textContent = `${params.pickup_location} · ${params.pickup_date} - ${params.return_date} · ${days} дн.`;
        summary.classList.remove('hidden');
    }

    async function updateCatalogPrices() {
        const params = SearchParams.load();
        if (!params || !params.pickup_location || !params.pickup_date || !params.return_date) return;

        const days = calculateRentalDays(params.pickup_date, params.return_date);
        const items = Array.from(document.querySelectorAll('.car-item[data-car-id]'));
        await Promise.all(items.map(async (item) => {
            const carId = item.dataset.carId;
            const card = item.querySelector('.mc-car');
            const priceEl = item.querySelector('.js-card-period-price');
            const noteEl = item.querySelector('.js-card-price-note');
            if (!carId || !priceEl || !noteEl) return;

            try {
                const query = new URLSearchParams({
                    car_id: carId,
                    pickup_area: params.pickup_location,
                    dropoff_area: params.pickup_location,
                    pickup_date: params.pickup_date,
                    return_date: params.return_date
                });
                const response = await fetch(`/api/calculate_price.php?${query.toString()}`, { headers: { 'Accept': 'application/json' } });
                const result = await response.json();
                if (!result || !result.success) return;

                item.dataset.periodTotal = String(result.total || 0);
                if (card) card.dataset.periodTotal = String(result.total || 0);
                priceEl.textContent = formatBaht(result.total);
                noteEl.textContent = `за ${result.days || days} дн. · ${formatBaht(result.daily_price)}/день`;
            } catch (error) {
                console.warn('Price update failed', error);
            }
        }));
    }

    function getActiveFilters() {
        return {
            type: document.getElementById('filter-type')?.value || '',
            seats: document.getElementById('filter-seats')?.value || '',
            fuel: document.getElementById('filter-fuel')?.value || '',
            price: document.getElementById('filter-price')?.value || ''
        };
    }

    function matchesFilters(item, filters) {
        const dailyPrice = Number(item.dataset.price || 0);
        if (filters.type && item.dataset.type !== filters.type) return false;
        if (filters.seats && Number(item.dataset.seats || 0) < Number(filters.seats)) return false;
        if (filters.fuel && item.dataset.fuel !== filters.fuel) return false;
        if (filters.price && dailyPrice > Number(filters.price)) return false;
        return true;
    }

    function applyCatalogDisplay() {
        const items = Array.from(document.querySelectorAll('#cars-grid .car-item'));
        const filters = getActiveFilters();
        const matched = items.filter(item => matchesFilters(item, filters));
        const visibleLimit = allShown ? matched.length : 8;

        items.forEach(item => {
            item.classList.add('hidden');
            item.dataset.filteredOut = matchesFilters(item, filters) ? '0' : '1';
        });

        matched.slice(0, visibleLimit).forEach(item => item.classList.remove('hidden'));

        const visibleCount = document.getElementById('cars-visible-count');
        const totalCount = document.getElementById('cars-total-count');
        const showAll = document.getElementById('show-all-container');
        const empty = document.getElementById('cars-empty-state');
        if (visibleCount) visibleCount.textContent = String(Math.min(visibleLimit, matched.length));
        if (totalCount) totalCount.textContent = String(matched.length);
        if (showAll) showAll.classList.toggle('hidden', allShown || matched.length <= visibleLimit);
        if (empty) empty.classList.toggle('hidden', matched.length > 0);
    }

    // Catalog Logic
    let allShown = false;
    function showAllCars() {
        allShown = true;
        applyCatalogDisplay();
    }

    function filterCars() {
        allShown = false;
        applyCatalogDisplay();
    }

    function resetCarFilters() {
        ['filter-type', 'filter-seats', 'filter-fuel', 'filter-price'].forEach((id) => {
            const field = document.getElementById(id);
            if (field) field.value = '';
        });
        allShown = false;
        applyCatalogDisplay();
    }

    function sortCars() {
        const sorter = document.getElementById('car-sorter');
        const grid = document.getElementById('cars-grid');
        if(!sorter || !grid) return;
        const sortBy = sorter.value;
        
        const items = Array.from(grid.querySelectorAll('.car-item'));
        items.sort((a, b) => {
            const valA = parseFloat(a.dataset[sortBy] || 0);
            const valB = parseFloat(b.dataset[sortBy] || 0);
            return sortBy === 'price' ? valA - valB : valB - valA;
        });
        
        items.forEach((item) => grid.appendChild(item));
        applyCatalogDisplay();
    }
    </script>
    <?php
    return ob_get_clean();
}
/**
 * Renders car detail page
 */
