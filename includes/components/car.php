<?php
declare(strict_types=1);


/**
 * Checks if a car has an active discount based on current date
 */
function is_discount_active($car) {
    if (empty($car['discount'])) return false;
    if (empty($car['discount_start']) || empty($car['discount_end'])) return true;
    
    try {
        $today = new DateTime('today');
        $start = DateTime::createFromFormat('d.m.Y', $car['discount_start']);
        $end = DateTime::createFromFormat('d.m.Y', $car['discount_end']);

        if (!$start || !$end) return true;
        
        $end->setTime(23, 59, 59);
        return $today >= $start && $today <= $end;
    } catch (\Exception $e) {
        return true;
    }
}

/**
 * Reusable UI Components for Rent Car Phuket
 */

/**
 * Renders a large client-side link button with icon support
 */

/**
 * Renders a unified Date Range Picker
 */

/**
 * Renders a section heading
 */
function render_section_header($title, $subtitle = '') {
    $html = "<div>";
    $html .= "<h2 class='text-2xl font-black text-gray-800 mb-6 mb-4'>$title</h2>";
    $html .= "<div class='w-20 h-1.5 bg-blue-600 rounded-full mb-12'></div>";
    $html .= "</div>";
    
    return $html;
}

/**
 * Renders a car card
 */

/**
 * Renders a car card
 */
function render_car_card($car) {
    $isActiveDiscount = is_discount_active($car);
    $basePrice = (int)($car['price'] ?? 0);
    $displayPrice = $isActiveDiscount ? (int)round($basePrice * (1 - ((float)$car['discount'] / 100))) : $basePrice;
    $image = !empty($car['image']) ? asset_image_url($car['image']) : placeholder_image_url();
    $carId = (int)($car['id'] ?? 0);
    $deposit = (int)($car['deposit'] ?? 5000);
    ob_start();
    ?>
    <article class="mc-car" data-card-car-id="<?php echo $carId; ?>">
        <div class="mc-car-photo">
            <div class="mc-car-badges">
                <?php if($isActiveDiscount): ?>
                    <span class="mc-badge mc-badge-coral">-<?php echo e($car['discount']); ?>%</span>
                <?php endif; ?>
                <span class="mc-badge mc-badge-yellow"><?php echo e($car['type'] ?? 'Авто'); ?></span>
            </div>
            <img src="<?php echo $image; ?>" alt="<?php echo e($car['name'] ?? 'Автомобиль'); ?>" loading="lazy" onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
        </div>
        <div class="mc-car-body">
            <div>
                <div class="mc-car-class"><?php echo e($car['type'] ?? 'Автомобиль'); ?></div>
                <div class="mc-car-title-row">
                    <h3 class="mc-car-title"><?php echo e($car['name'] ?? 'Автомобиль'); ?></h3>
                    <span class="mc-car-year"><?php echo (int)($car['year'] ?? date('Y')); ?></span>
                </div>
            </div>

            <div class="mc-car-specs">
                <div class="mc-car-spec"><?php echo get_icon('transmission', 'w-4 h-4'); ?><div class="mc-car-spec-info"><span class="mc-car-spec-label">Коробка</span><span class="mc-car-spec-val"><?php echo e($car['transmission'] ?? 'АКПП'); ?></span></div></div>
                <div class="mc-car-spec"><?php echo get_icon('fuel', 'w-4 h-4'); ?><div class="mc-car-spec-info"><span class="mc-car-spec-label">Топливо</span><span class="mc-car-spec-val"><?php echo e($car['fuel'] ?? 'Бензин'); ?></span></div></div>
                <div class="mc-car-spec"><?php echo get_icon('engine', 'w-4 h-4'); ?><div class="mc-car-spec-info"><span class="mc-car-spec-label">Двигатель</span><span class="mc-car-spec-val"><?php echo e(($car['engine'] ?? '1.5') . 'L'); ?></span></div></div>
                <div class="mc-car-spec"><?php echo get_icon('seat', 'w-4 h-4'); ?><div class="mc-car-spec-info"><span class="mc-car-spec-label">Посадка</span><span class="mc-car-spec-val"><?php echo e(($car['seats'] ?? 5) . ' мест'); ?></span></div></div>
                <div class="mc-car-spec"><?php echo get_icon('calendar', 'w-4 h-4'); ?><div class="mc-car-spec-info"><span class="mc-car-spec-label">Год</span><span class="mc-car-spec-val"><?php echo (int)($car['year'] ?? date('Y')); ?></span></div></div>
                <div class="mc-car-spec"><?php echo get_icon('ac', 'w-4 h-4'); ?><div class="mc-car-spec-info"><span class="mc-car-spec-label">Климат</span><span class="mc-car-spec-val">Кондиционер</span></div></div>
            </div>

            <div class="mc-car-trust">
                <span>Залог: <?php echo number_format($deposit, 0, '.', ' '); ?> ฿</span>
                <span>Без скрытых сборов</span>
            </div>

            <div class="mc-car-foot">
                <div class="mc-car-price-block">
                    <span class="mc-car-price js-card-period-price"><?php echo number_format($displayPrice, 0, '.', ' '); ?> ฿</span>
                    <span class="mc-car-price-sub">
                        <?php if(is_discount_active($car)): ?>
                            <span class="mc-car-price-old"><?php echo number_format($basePrice, 0, '.', ' '); ?> ฿</span>
                        <?php endif; ?>
                        <span class="js-card-price-note">за день</span>
                    </span>
                </div>
                <a href="/car/<?php echo $carId; ?>" class="mc-btn mc-btn-yellow" onclick="if (typeof handleCarSelect === 'function') { handleCarSelect(event, <?php echo $carId; ?>); }">
                    Выбрать <?php echo get_icon('arrow-right', 'w-4 h-4'); ?>
                </a>
            </div>
        </div>
    </article>
<?php
    return ob_get_clean();
}

/**
 * Renders the Home page Car Section
 */

/**
 * Renders the Home page Car Section
 */
function render_car_section($cars) {
    $carCount = count($cars);
    $types = [];
    $fuels = [];
    $seatOptions = [];
    foreach ($cars as $car) {
        if (!empty($car['type'])) $types[$car['type']] = true;
        if (!empty($car['fuel'])) $fuels[$car['fuel']] = true;
        if (!empty($car['seats'])) $seatOptions[(int)$car['seats']] = true;
    }
    $types = array_keys($types);
    $fuels = array_keys($fuels);
    $seatOptions = array_keys($seatOptions);
    sort($types);
    sort($fuels);
    sort($seatOptions, SORT_NUMERIC);
    ob_start();
    ?>
    <section class="mc-section" id="cars-section">
        <div class="mc-container">
            <div class="mc-section-head">
                <div>
                    <div class="mc-section-eyebrow">[ 01 ] - АВТОПАРК</div>
                    <h2 class="mc-section-title">Выбери <em>машину</em></h2>
                    <p class="mc-section-sub">Выберите авто под маршрут, даты и бюджет. Цена пересчитается с учётом срока аренды и доставки.</p>
                </div>
                <div class="mc-result-count"><b><?php echo $carCount; ?></b> авто · договор, депозит и поддержка 24/7</div>
            </div>


            <div class="mc-toolbar">
                <div class="mc-result-count"><b id="cars-visible-count"><?php echo min(8, $carCount); ?></b> / <span id="cars-total-count"><?php echo $carCount; ?></span> показано</div>
                <div class="mc-sort-shell">
                        <select id="car-sorter" onchange="sortCars()" aria-label="Сортировка автомобилей">
                            <option value="price" selected>По стоимости</option>
                            <option value="year">По году выпуска</option>
                            <option value="engine">По двигателю</option>
                            <option value="discount">По скидке</option>
                        </select>
                </div>
            </div>

            <div id="cars-grid" class="mc-car-grid">
                <?php $i = 0; foreach($cars as $car): $i++; ?>
                <div class="car-item <?php echo $i > 8 ? 'hidden' : ''; ?>"
                     data-car-id="<?php echo (int)($car['id'] ?? 0); ?>"
                     data-type="<?php echo e($car['type'] ?? ''); ?>"
                     data-fuel="<?php echo e($car['fuel'] ?? ''); ?>"
                     data-seats="<?php echo (int)($car['seats'] ?? 0); ?>"
                     data-year="<?php echo (int)($car['year'] ?? 0); ?>"
                     data-price="<?php echo (int)($car['price'] ?? 0); ?>"
                     data-engine="<?php echo e($car['engine'] ?? 0); ?>"
                     data-discount="<?php echo (int)($car['discount'] ?? 0); ?>">
                    <?php echo render_car_card($car); ?>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($carCount > 8): ?>
                <div id="show-all-container" class="mc-show-more">
                    <button onclick="showAllCars()" class="mc-btn mc-btn-ghost mc-btn-lg">
                        Показать все машины
                        <?php echo get_icon('chevron-down', 'w-4 h-4'); ?>
                    </button>
                </div>
            <?php endif; ?>

            <div id="cars-empty-state" class="mc-empty-state hidden">
                <h3>Под эти фильтры машин не найдено</h3>
                <p>Сбросьте фильтры или увеличьте бюджет. Менеджер также подскажет замену в WhatsApp или Telegram.</p>
                <button type="button" class="mc-btn mc-btn-yellow" onclick="resetCarFilters()">Показать все</button>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Renders trust and service details for the home page.
 */

/**
 * Renders a compact stat tile for car details
 */
function render_car_stat_tile($iconName, $label, $value) {
    ob_start();
    ?>
    <div class="mc-detail-stat-tile">
        <div class="mc-detail-stat-icon"><?php echo get_icon($iconName, 'w-5 h-5'); ?></div>
        <div class="mc-detail-stat-copy">
            <div class="mc-detail-stat-label"><?php echo e($label); ?></div>
            <div class="mc-detail-stat-value"><?php echo e((string)$value); ?></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders an included feature card
 */

/**
 * Renders an included feature card
 */
function render_included_item($title, $description) {
    ob_start();
    ?>
    <div class="flex items-start gap-4 p-6 bg-white rounded-2xl">
        <div class="text-green-500 mt-1 flex-shrink-0"><?php echo get_icon('check', 'w-6 h-6'); ?></div>
        <div class="text-left">
            <div class="font-black text-gray-800 mb-1 text-sm"><?php echo $title; ?></div>
            <p class="text-sm text-gray-500 leading-relaxed"><?php echo $description; ?></p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a reusable admin status toggle
 */
/**
 * Renders car detail page
 */
function render_car_detail($car, $allCars = [], $pickupArea = '', $dropoffArea = '', $pickupDate = '', $returnDate = '') {
    global $locations, $contactInfo;
    
    // Set defaults if not provided
    if (empty($pickupDate)) $pickupDate = date('d.m.Y');
    if (empty($returnDate)) $returnDate = date('d.m.Y', strtotime('+3 days'));
    if (empty($pickupArea) && !empty($locations)) $pickupArea = $locations[0]['name'];
    if (empty($dropoffArea)) $dropoffArea = $pickupArea;
    
    ob_start();
    
    // Calculate discount info – price is now the base price
    $isActiveDiscount = is_discount_active($car);
    $discountedPrice = $isActiveDiscount ? $car['price'] * (1 - $car['discount']/100) : $car['price'];
    $discountAmount = $isActiveDiscount ? $car['price'] - $discountedPrice : 0;
    $pricing = new \App\Services\PricingService();
    $priceInfo = $pricing->calculate($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea);
    $whatsapp = $contactInfo['socialMedia']['whatsapp'] ?? '#';
    $bookingUrl = '/booking?id=' . (int)$car['id'] . '&' . http_build_query([
        'pickup_date' => $pickupDate,
        'return_date' => $returnDate,
        'pickup_area' => $pickupArea,
        'dropoff_area' => $dropoffArea,
    ]);
    
    ?>
    <div class="mc-detail-page">
        <?php echo render_stepper(2, $car['id']); ?>

        <!-- Main Content -->
        <div class="mc-container">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT: Images and Details -->
                <div class="lg:col-span-2">
                    <!-- Image Gallery and Specs -->
                    <div class="mc-side-panel bg-white rounded-[32px] p-6 mb-6">
                        <h1 class="text-3xl md:text-4xl font-black text-gray-800 mb-4 tracking-tighter"><?php echo e($car['name']); ?></h1>
                        <div class="flex flex-col sm:flex-row gap-6 items-start">
                            <div class="mc-car-photo rounded-2xl group cursor-pointer flex-1 min-w-0" id="main-image-container">
                                <?php if($isActiveDiscount): ?>
                                    <div class="absolute top-8 right-8 bg-orange-500 text-white px-6 py-2 rounded-full text-sm font-black z-10 shadow-lg shadow-orange-500/30">
                                        -<?php echo $car['discount']; ?>%
                                    </div>
                                <?php endif; ?>
                                <img id="main-car-image" src="<?php echo asset_image_url($car['image']); ?>" alt="<?php echo e($car['name']); ?>"
                                     class="transition-transform duration-300 group-hover:scale-105"
                                     onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
                            </div>

                            <aside class="mc-detail-specs-panel w-full" aria-labelledby="car-specs-title">
                                <div class="mc-detail-specs-list">
                                    <?php echo render_car_stat_tile('transmission', 'Коробка', $car['transmission']); ?>
                                    <?php echo render_car_stat_tile('fuel', 'Топливо', $car['fuel']); ?>
                                    <?php echo render_car_stat_tile('engine', 'Двигатель', $car['engine'] . 'L'); ?>
                                    <?php echo render_car_stat_tile('seat', 'Посадка', $car['seats'] . ' мест'); ?>
                                    <?php echo render_car_stat_tile('calendar', 'Год выпуска', $car['year'] . ' год'); ?>
                                    <?php echo render_car_stat_tile('ac', 'Климат', 'Кондиционер'); ?>
                                </div>
                            </aside>
                        </div>
                    </div>

                    <!-- What's Included -->
                    <div class="mb-12">
                        <div class="mb-8">
                            <h2 class="text-2xl font-black text-gray-800 mb-6">Включено в стоимость</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php echo render_included_item('Страховка по договору', 'Покрытие, франшиза и исключения фиксируются перед выдачей авто'); ?>
                            <?php echo render_included_item('Топливо без сюрпризов', 'Получение и возврат проходят с одинаковым уровнем топлива'); ?>
                            <?php echo render_included_item('Доставка по району', 'Стоимость подачи и возврата считается сразу в калькуляторе'); ?>
                            <?php echo render_included_item('Поддержка 24/7', 'На связи в Telegram и WhatsApp во время всей аренды'); ?>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Booking Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8">
                        <!-- Price Calculation -->
                        <div class="mc-side-panel bg-white rounded-[32px] p-6 mb-6">
                            <div class="space-y-3 mb-6">
                                <h3 class="text-xl font-black text-gray-800">Получение</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <select id="detail-pickup-area" onchange="updateCarDetailCalculation()" class="w-full bg-gray-50 border border-gray-200 rounded-[16px] px-4 py-3 font-bold text-gray-800 focus:bg-white focus:border-blue-600 transition-all outline-none cursor-pointer">
                                        <?php foreach($locations as $loc): ?>
                                            <option value="<?php echo $loc['name']; ?>" data-price="<?php echo $loc['delivery_price'] ?? 0; ?>" <?php echo $loc['name'] === $pickupArea ? 'selected' : ''; ?>><?php echo $loc['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" id="detail-pickup-date" value="<?php echo htmlspecialchars($pickupDate); ?>"
                                           class="w-full bg-gray-50 border border-gray-200 rounded-[16px] px-4 py-3 font-bold text-gray-800 focus:bg-white focus:border-blue-600 transition-all outline-none cursor-pointer" readonly>
                                </div>
                                <h3 class="text-xl font-black text-gray-800 pt-2">Возврат</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <select id="detail-dropoff-area" onchange="updateCarDetailCalculation()" class="w-full bg-gray-50 border border-gray-200 rounded-[16px] px-4 py-3 font-bold text-gray-800 focus:bg-white focus:border-blue-600 transition-all outline-none cursor-pointer">
                                        <?php foreach($locations as $loc): ?>
                                            <option value="<?php echo $loc['name']; ?>" data-price="<?php echo $loc['delivery_price'] ?? 0; ?>" <?php echo $loc['name'] === $dropoffArea ? 'selected' : ''; ?>><?php echo $loc['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" id="detail-return-date" value="<?php echo htmlspecialchars($returnDate); ?>"
                                           class="w-full bg-gray-50 border border-gray-200 rounded-[16px] px-4 py-3 font-bold text-gray-800 focus:bg-white focus:border-blue-600 transition-all outline-none cursor-pointer" readonly>
                                </div>
                            </div>

                            <!-- Calculation Breakdown -->
                            <?php echo render_price_summary_block($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea); ?>

                            <?php echo render_client_link_button('Далее', $bookingUrl, get_icon('arrow-right', 'w-4 h-4'), '', '', 'id="next-step-btn"'); ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="mc-mobile-cta" aria-label="Быстрое бронирование">
            <div>
                <span id="mobile-detail-days"><?php echo (int)$priceInfo['days']; ?> дн.</span>
                <strong id="mobile-detail-total">฿<?php echo number_format((int)$priceInfo['total'], 0, '.', ' '); ?></strong>
            </div>
            <a id="mobile-detail-btn" class="mc-btn mc-btn-yellow" href="<?php echo e($bookingUrl); ?>">Забронировать</a>
        </div>
    </div>

    <script>
        // Calculate days and total price using Unified Calculator API
        async function updateCarDetailCalculation() {
            const pickupArea = document.getElementById('detail-pickup-area').value;
            const dropoffArea = document.getElementById('detail-dropoff-area').value;
            const pickupDate = document.getElementById('detail-pickup-date').value;
            const returnDate = document.getElementById('detail-return-date').value;

            const res = await RentalCalculator.calculate({
                car_id: <?php echo $car['id']; ?>,
                pickup_date: pickupDate,
                return_date: returnDate,
                pickup_area: pickupArea,
                dropoff_area: dropoffArea
            });

                if (res) {
                    RentalCalculator.updateUI('price-summary-container', res);

                    // Update Next Step Link
                    const nextBtn = document.getElementById('next-step-btn');
                    const mobileBtn = document.getElementById('mobile-detail-btn');
                    if (nextBtn) {
                        const url = new URL(nextBtn.href, window.location.origin);
                        url.searchParams.set('pickup_date', pickupDate);
                        url.searchParams.set('return_date', returnDate);
                        url.searchParams.set('pickup_area', pickupArea);
                        url.searchParams.set('dropoff_area', dropoffArea);
                        nextBtn.href = url.pathname + url.search;
                        if (mobileBtn) mobileBtn.href = nextBtn.href;
                    }
                    const mobileTotal = document.getElementById('mobile-detail-total');
                    const mobileDays = document.getElementById('mobile-detail-days');
                    if (mobileTotal) mobileTotal.textContent = `฿${Number(res.total || 0).toLocaleString('ru-RU')}`;
                    if (mobileDays) mobileDays.textContent = `${res.days || 1} дн.`;
                }
            }
        
        // Change main image
        function changeMainImage(imageSrc) {
            const mainImg = document.getElementById('main-car-image');
            const thumbnails = document.querySelectorAll('.thumbnail-btn');
            
            if (mainImg) {
                mainImg.style.opacity = '0.5';
                mainImg.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    mainImg.src = imageSrc;
                    mainImg.style.opacity = '1';
                    mainImg.style.transform = 'scale(1)';
                }, 150);
            }
            
            // Update active thumbnail
            thumbnails.forEach((thumb, idx) => {
                if (thumb.querySelector('img').src === mainImg.src || 
                    thumb.querySelector('img').alt === mainImg.alt || idx === 0) {
                    thumb.classList.remove('border-gray-200');
                    thumb.classList.add('border-blue-500');
                    thumb.classList.remove('opacity-60');
                } else {
                    thumb.classList.add('border-gray-200');
                    thumb.classList.remove('border-blue-500');
                    thumb.classList.add('opacity-60');
                }
            });
        }

        // Open image in fullscreen modal
        function openImageModal(imageSrc) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4 animate-fadeIn';
            modal.onclick = (e) => {
                if (e.target === modal) modal.remove();
            };
            
            modal.innerHTML = `
                <button onclick="this.parentElement.remove()" class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors z-51">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img src="${imageSrc}" alt="Fullscreen image" class="max-w-full max-h-full object-contain rounded-[20px]">
            `;
            
            document.body.appendChild(modal);
        }

        // Add click handler to main image for fullscreen
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize datepickers for car detail page
            const config = {
                locale: 'ru',
                dateFormat: 'd.m.Y',
                minDate: 'today',
                disableMobile: true,
                allowInput: false,
                monthSelectorType: 'static',
                position: 'auto center',
                appendTo: document.body
            };
            
            const pickupDateInput = document.getElementById('detail-pickup-date');
            const returnDateInput = document.getElementById('detail-return-date');
            
            // Инициализация датапикера для даты подачи с валидацией
            const detailPickupPicker = flatpickr("#detail-pickup-date", { 
                ...config, 
                defaultDate: pickupDateInput?.value || new Date(),
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        // Устанавливаем минимальную дату возврата = дата подачи + 1 день
                        const minReturnDate = new Date(selectedDates[0]);
                        minReturnDate.setDate(minReturnDate.getDate() + 1);
                        detailReturnPicker.set('minDate', minReturnDate);
                        
                        // Если текущая дата возврата меньше новой минимальной, обновляем её
                        const currentReturn = detailReturnPicker.selectedDates[0];
                        if (!currentReturn || currentReturn < minReturnDate) {
                            detailReturnPicker.setDate(minReturnDate);
                        }
                    }
                    updateCarDetailCalculation();
                }
            });
            
            // Инициализация датапикера для даты возврата
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const defaultReturnDate = returnDateInput?.value || new Date().setDate(new Date().getDate() + 3);
            
            const detailReturnPicker = flatpickr("#detail-return-date", { 
                ...config, 
                defaultDate: defaultReturnDate,
                minDate: tomorrow,
                onChange: function(selectedDates, dateStr, instance) {
                    updateCarDetailCalculation();
                }
            });
            
            // Initial calculation with current values
            updateCarDetailCalculation();
            
            const mainImageContainer = document.getElementById('main-image-container');
            if (mainImageContainer) {
                mainImageContainer.onclick = function() {
                    const src = document.getElementById('main-car-image').src;
                    openImageModal(src);
                };
            }
        });
    </script>

    <!-- Similar Cars Section Before Footer -->
    <?php if(count($allCars) > 1): ?>
    <section class="py-16">
        <div class="mc-container">
            <h2 class="text-2xl font-black text-gray-800 mb-6">Похожие автомобили</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                $similarCars = array_slice(array_filter($allCars, function($c) use($car) {
                    return $c['id'] !== $car['id'] && $c['type'] === $car['type'];
                }), 0, 4);
                
                if(count($similarCars) < 4) {
                    $additionalCars = array_slice(array_filter($allCars, function($c) use($car, $similarCars) {
                        return $c['id'] !== $car['id'] && !in_array($c['id'], array_column($similarCars, 'id'));
                    }), 0, 4 - count($similarCars));
                    $similarCars = array_merge($similarCars, $additionalCars);
                }
                
                foreach($similarCars as $similar): 
                ?>
                    <?php echo render_car_card($similar); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}

/**
 * Renders a standard centered section container
 */
