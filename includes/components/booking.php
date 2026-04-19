<?php
declare(strict_types=1);


/**
 * Renders a progress stepper (paginator) for the booking flow
 */
function render_stepper($currentStep = 1, $carId = null) {
    $steps = [
        ['number' => 1, 'label' => 'Выбор', 'url' => "/"],
        ['number' => 2, 'label' => 'Детали', 'url' => $carId ? "/car/$carId" : "/"],
        ['number' => 3, 'label' => 'Оформление', 'url' => $carId ? "/booking?id=$carId" : null],
        ['number' => 4, 'label' => 'Готово', 'url' => null]
    ];
    ob_start();
    ?>
    <div class="mc-container pt-2 md:pt-4 pb-8 md:pb-12">
        <div class="hidden md:flex items-center justify-center">
            <div class="flex items-center">
                <?php foreach($steps as $index => $step): ?>
                    <?php 
                    $isActive = $step['number'] <= $currentStep; 
                    $isCurrent = $step['number'] == $currentStep;
                    $isClickable = $isActive && $step['url'] && !$isCurrent;
                    ?>
                    
                    <!-- Line -->
                    <?php if($index > 0): ?>
                        <div class="w-12 h-0.5 rounded-full <?php echo $isActive ? 'bg-yellow-400' : 'bg-white/25'; ?> mx-3"></div>
                    <?php endif; ?>

                    <div class="relative">
                        <?php if($isClickable): ?>
                            <a href="<?php echo $step['url']; ?>" class="group flex items-center gap-3 px-3 py-2 rounded-2xl hover:bg-white transition-all bg-transparent">
                        <?php else: ?>
                            <div class="flex items-center gap-3 px-3 py-2">
                        <?php endif; ?>

                        <div class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center font-black transition-all <?php echo $isCurrent ? 'bg-blue-600 text-white scale-110' : ($isActive ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400'); ?> <?php if($isClickable) echo 'group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white'; ?>">
                            <?php if($isActive && !$isCurrent): ?>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            <?php else: ?>
                                <?php echo $step['number']; ?>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs font-black uppercase tracking-widest transition-colors whitespace-nowrap <?php echo $isCurrent ? 'text-gray-800' : ($isActive ? 'text-blue-600' : 'text-gray-400'); ?> <?php if($isClickable) echo 'group-hover:text-blue-600'; ?>"><?php echo $step['label']; ?></span>

                        <?php if($isClickable): ?>
                            </a>
                        <?php else: ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders the Toast component (hidden by default)
 */

/**
 * Renders the reusable price calculation summary block for sidebars.
 */
function render_price_summary_block($car, $pickupDate = null, $returnDate = null, $pickupArea = '', $dropoffArea = '', $extraClasses = '') {
    if (!$pickupDate) $pickupDate = date('d.m.Y');
    if (!$returnDate) $returnDate = date('d.m.Y', strtotime('+3 days'));
    
    $pricing = new \App\Services\PricingService();
    $priceInfo = $pricing->calculate($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea);
    
    ob_start();
    ?>
    <div id="price-summary-container" class="mb-6 <?php echo $extraClasses; ?>">
        <h3 class="text-xl font-black text-gray-800 mb-4 tracking-tight">Расчёт стоимости</h3>
        
        <!-- Детальный расчет -->
        <div class="space-y-1">
            <!-- Базовая цена -->
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-600 font-bold text-sm">Базовая цена</span>
                <span class="font-black text-gray-800">฿<?php echo (int)$priceInfo['daily_base']; ?></span>
            </div>
            
            <!-- Количество дней -->
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-600 font-bold text-sm">Количество дней</span>
                <span class="font-black text-gray-800 text-lg" id="calc-days"><?php echo $priceInfo['days']; ?></span>
            </div>
        </div>
        
        <!-- Доставка и возврат -->
        <div class="space-y-1 mt-1">
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-600 font-bold text-sm">Подача</span>
                <span class="font-black text-gray-800" id="calc-delivery">฿<?php echo (int)$priceInfo['delivery_price']; ?></span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-600 font-bold text-sm">Возврат</span>
                <span class="font-black text-gray-800" id="calc-return">฿<?php echo (int)$priceInfo['return_price']; ?></span>
            </div>
        </div>
        
        <!-- Дополнительная информация -->
        <div class="space-y-1 mt-1">
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-600 font-bold text-sm">Депозит</span>
                <span class="font-black text-gray-800">฿<?php echo (int)$priceInfo['deposit']; ?></span>
            </div>
            <div class="flex justify-between items-center py-1">
                <span class="text-gray-600 font-bold text-sm">Страховка</span>
                <span class="font-black text-green-600 uppercase text-[10px] tracking-widest">По договору</span>
            </div>
        </div>
        
        <!-- Итого -->
        <div class="mt-2 pt-2">
            <div class="flex justify-between items-baseline">
                <span class="font-black text-gray-800 text-sm uppercase tracking-wider">Итого:</span>
                <span class="text-3xl font-black text-blue-600" id="calc-total">฿<?php echo (int)$priceInfo['total']; ?></span>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders the selected car summary for booking sidebar
 */

/**
 * Renders the selected car summary for booking sidebar
 */
function render_booking_car_summary($car) {
    ob_start();
    ?>
    <div class="mb-6 -mx-6 -mt-6">
        <div class="mc-car-photo rounded-t-3xl">
            <img src="<?php echo asset_image_url($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>"
                 onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
        </div>
    </div>
    <div class="text-center mb-10 space-y-2">
        <div class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Ваш выбор</div>
        <h2 class="text-4xl font-black text-gray-800 tracking-tighter"><?php echo htmlspecialchars($car['name']); ?></h2>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders the trip details (dates and locations) for booking sidebar
 */

/**
 * Renders the trip details (dates and locations) for booking sidebar
 */
function render_booking_trip_details($labels, $data) {
    ob_start();
    ?>
    <div class="space-y-6 mb-10 py-8 border-y-2 border-dashed border-gray-100">
        <?php foreach($labels as $key => $label): 
            $info = $data[$key] ?? null;
            if (!$info) continue;
        ?>
        <div class="flex items-start gap-5">
            <div class="bg-blue-50 p-3 rounded-2xl text-blue-600"><?php echo get_icon($info['icon'] ?? 'map-pin', 'w-5 h-5'); ?></div>
            <div>
                <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1"><?php echo $label; ?></div>
                <div class="text-base font-black text-gray-800 leading-tight"><?php echo htmlspecialchars($info['location'] ?? ''); ?></div>
                <div class="text-sm font-bold text-blue-600 mt-1"><?php echo htmlspecialchars($info['date'] ?? ''); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a standardized admin table with a header
 */


/**
 * Renders the Booking Form Section
 */
function render_booking_form($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea, $totalPrice) {
    ob_start();
    ?>
    <div class="lg:col-span-2 space-y-8 animate-fadeIn" style="animation-delay: 100ms;">
        <div class="mc-form-panel bg-white rounded-3xl p-6 relative overflow-hidden">
            <div class="relative z-10">
                 <div class="mb-6">
                    <div class="text-xs font-black text-blue-600 uppercase tracking-[0.3em] mb-2">Бронирование</div>
                    <h2 class="text-3xl font-black text-gray-800 tracking-tighter">Оформление заказа</h2>
                    <p class="text-gray-400 font-medium mt-1">Оставьте контакты. Менеджер подтвердит авто, время подачи и условия договора.</p>
                </div>

                 <form action="/success" method="post" class="space-y-6" id="booking-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="car_id" value="<?php echo $selectedCar['id']; ?>">
                    <input type="hidden" name="pickup_date" value="<?php echo htmlspecialchars($pickupDate); ?>">
                    <input type="hidden" name="return_date" value="<?php echo htmlspecialchars($returnDate); ?>">
                    <input type="hidden" name="pickup_area" value="<?php echo htmlspecialchars($pickupArea); ?>">
                    <input type="hidden" name="dropoff_area" value="<?php echo htmlspecialchars($dropoffArea); ?>">
                    <input type="hidden" name="total_price" value="<?php echo $totalPrice; ?>">
                    
                    <!-- User Info Section -->
                     <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-1 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Контактная информация</h3>
                        </div>

                        <div class="grid gap-6 sm:grid-cols-2">
                            <?php echo render_booking_input('client_name', 'Ваше имя', 'Алексей', 'user', 'text', true); ?>
                            <?php echo render_booking_input('client_phone', 'Телефон или WhatsApp', '+7 или +66', 'phone', 'tel', true, '+7'); ?>
                        </div>
                    </div>

                    <!-- Comment Section -->
                     <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Комментарий</label>
                        <textarea name="notes" rows="3" placeholder="Отель, рейс, детское кресло или другое пожелание" class="w-full bg-blue-50 border-2 border-transparent rounded-[32px] px-8 py-6 text-gray-800 font-medium outline-none focus:bg-white focus:border-blue-600/20 transition-all min-h-[120px] placeholder-gray-300"></textarea>
                    </div>


                </form>
            </div>
        </div>

        <?php echo render_booking_instructions(); ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders user instructions block shown below the booking form
 */

/**
 * Renders user instructions block shown below the booking form
 */
function render_booking_instructions() {
    $steps = [
        ['icon' => 'edit',      'title' => 'Заполните данные',      'text' => 'Укажите имя и номер телефона. В комментарии опишите пожелания: детское кресло, сим-карта, доставка в отель.'],
        ['icon' => 'phone',     'title' => 'Подтверждение заявки',  'text' => 'В течение 15 минут менеджер свяжется с вами в мессенджере или по телефону, уточнит детали и забронирует авто.'],
        ['icon' => 'car',       'title' => 'Получение автомобиля',  'text' => 'Встречаем вас в выбранной точке подачи. Пожалуйста, подготовьте паспорт и водительское удостоверение.'],
        ['icon' => 'shield',    'title' => 'Залог и страховка',     'text' => 'Залог указан в расчёте. Покрытие, франшиза и исключения прописываются в договоре до оплаты.'],
        ['icon' => 'calendar',  'title' => 'Во время аренды',       'text' => 'Поддержка 24/7 в Telegram и WhatsApp. Топливо возвращается на том же уровне, что при получении.'],
        ['icon' => 'check',     'title' => 'Возврат',               'text' => 'В назначенную дату встречаемся в точке возврата, проверяем авто и возвращаем залог в полном объёме.'],
    ];
    ob_start();
    ?>
    <div class="bg-white rounded-3xl p-6 md:p-8 animate-fadeIn" style="animation-delay: 200ms;">
        <div class="mb-6">
            <div class="text-xs font-black text-blue-600 uppercase tracking-[0.3em] mb-2">Инструкция</div>
            <h2 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tighter">Что будет после оформления</h2>
            <p class="text-gray-500 font-medium mt-1">Шесть простых шагов — от заявки до возврата автомобиля.</p>
        </div>
        <ol class="space-y-4">
            <?php foreach ($steps as $i => $step): ?>
                <li class="flex items-start gap-4 p-4 rounded-2xl bg-gray-50 hover:bg-blue-50/60 transition-colors">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center shadow-lg shadow-blue-600/20"><?php echo $i + 1; ?></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-black text-gray-800 text-base leading-tight"><?php echo htmlspecialchars($step['title']); ?></h3>
                        </div>
                        <p class="text-sm text-gray-600 font-medium leading-relaxed"><?php echo htmlspecialchars($step['text']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <div class="mt-6 p-5 rounded-2xl bg-blue-600/5 border border-blue-600/10 flex items-start gap-3">
            <div class="text-blue-600 mt-0.5"><?php echo get_icon('info', 'w-5 h-5'); ?></div>
            <div class="text-sm text-gray-700 font-medium leading-relaxed">
                Нужна помощь или хотите изменить условия заявки? Напишите нам в Telegram или WhatsApp — ответим в течение нескольких минут.
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a stylized input for the booking form
 */

/**
 * Renders a stylized input for the booking form
 */
function render_booking_input($name, $label, $placeholder, $icon, $type = 'text', $required = false, $value = '') {
    $requiredAttr = $required ? ' required' : '';
    $valueAttr = $value !== '' ? ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' : '';
    $dataAttr = $name === 'client_phone' ? ' data-phone="1"' : '';
    ob_start();
    ?>
    <div class="space-y-2">
        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4"><?php echo $label; ?></label>
        <div class="relative group">
            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-600 transition-colors"><?php echo get_icon($icon, 'w-5 h-5'); ?></div>
            <input type="<?php echo $type; ?>" name="<?php echo $name; ?>" placeholder="<?php echo $placeholder; ?>" class="w-full bg-blue-50 border-2 border-transparent rounded-2xl pl-16 pr-8 py-5 text-gray-800 font-bold outline-none focus:bg-white focus:border-blue-600/20 transition-all placeholder-gray-300"<?php echo $requiredAttr . $valueAttr . $dataAttr; ?>>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders the Booking Summary Sidebar
 */

/**
 * Renders the Booking Summary Sidebar
 */
function render_booking_summary_sidebar($selectedCar, $pickupArea, $dropoffArea, $pickupDate, $returnDate) {
    global $contactInfo;
    $whatsapp = $contactInfo['socialMedia']['whatsapp'] ?? '#';
    $pricing = new \App\Services\PricingService();
    $priceInfo = $pricing->calculate($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea);
    ob_start();
    ?>
    <aside class="space-y-8 mb-12">
        <div class="mc-side-panel bg-white rounded-3xl p-6 shadow-2xl overflow-hidden relative group animate-fadeIn" style="animation-delay: 150ms;">
            <div class="absolute top-0 right-0 w-48 h-48 bg-blue-600/10 rounded-bl-[100%] transition-transform group-hover:scale-150 duration-1000"></div>
            
            <div class="relative z-10">
                <?php echo render_booking_car_summary($selectedCar); ?>

                <?php 
                echo render_booking_trip_details(
                    ['pickup' => 'Получение', 'return' => 'Возврат'],
                    [
                        'pickup' => ['location' => $pickupArea, 'date' => $pickupDate, 'icon' => 'map-pin'],
                        'return' => ['location' => $dropoffArea, 'date' => $returnDate, 'icon' => 'map-pin']
                    ]
                ); 
                ?>

                <?php echo render_price_summary_block($selectedCar, $pickupDate, $returnDate, $pickupArea, $dropoffArea, ''); ?>


                <?php echo render_client_submit_button('Подтвердить заказ', get_icon('arrow-right', 'w-5 h-5'), 'primary', '', 'form="booking-form"'); ?>
                <a class="mc-help-link" href="<?php echo e($whatsapp); ?>">Задать вопрос перед заявкой</a>
            </div>
        </div>
    </aside>
    <div class="mc-mobile-cta" aria-label="Подтверждение заказа">
        <div>
            <span><?php echo (int)$priceInfo['days']; ?> дн.</span>
            <strong>฿<?php echo number_format((int)$priceInfo['total'], 0, '.', ' '); ?></strong>
        </div>
        <button type="submit" form="booking-form" class="mc-btn mc-btn-yellow">Подтвердить</button>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders the "Car Not Found" state for the booking page.
 */

/**
 * Renders the "Car Not Found" state for the booking page.
 */
function render_booking_not_found() {
    ob_start();
    ?>
    <div class="text-center py-20">
        <div class="mb-8 flex justify-center">
            <div class="bg-gray-100 p-8 rounded-full">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <h2 class="text-4xl font-black text-gray-800 mb-4 tracking-tighter">Машина не найдена</h2>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">К сожалению, мы не смогли найти выбранный автомобиль. Пожалуйста, выберите другую модель в нашем каталоге.</p>
        <?php echo render_client_button('Все автомобили', '/', get_icon('car', 'w-4 h-4')); ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders the scripts required for the booking page (validation, etc.)
 */

/**
 * Renders the scripts required for the booking page (validation, etc.)
 */
function render_booking_scripts() {
    ob_start();
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('#booking-form');
        if (!form) return;

        // Phone auto-format: +7 default, auto-switch to +66 for Thai numbers
        const phoneInput = form.querySelector('input[data-phone]');
        if (phoneInput) {
            phoneInput.addEventListener('input', () => {
                let v = phoneInput.value;
                if (/^8/.test(v)) {
                    phoneInput.value = '+7' + v.slice(1);
                } else if (/^66/.test(v)) {
                    phoneInput.value = '+' + v;
                }
            });
            phoneInput.addEventListener('focus', () => {
                if (phoneInput.value === '') phoneInput.value = '+7';
            });
        }

        const inputs = form.querySelectorAll('input[required]');
        
        const validateField = (input) => {
            const value = input.value.trim();
            let isValid = true;
            let error = '';
            
            if (value === '') {
                isValid = false;
                error = 'Поле обязательно для заполнения';
            } else if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                isValid = false;
                error = 'Некорректный email';
            } else if (input.type === 'tel' && value.length < 10) {
                isValid = false;
                error = 'Введите корректный номер';
            } else if (input.name === 'client_name' && value.length < 2) {
                isValid = false;
                error = 'Слишком короткое имя';
            }
            
            const container = input.closest('.relative');
            let errorDisplay = container.querySelector('.field-error');
            
            if (!isValid) {
                input.classList.add('border-red-500/50', 'bg-red-50/50');
                input.classList.remove('bg-blue-50/50');
                if (!errorDisplay) {
                    errorDisplay = document.createElement('div');
                    errorDisplay.className = 'field-error absolute -bottom-5 left-4 text-[9px] font-black text-red-500 uppercase tracking-widest';
                    container.appendChild(errorDisplay);
                }
                errorDisplay.textContent = error;
            } else {
                input.classList.remove('border-red-500/50', 'bg-red-50/50');
                input.classList.add('bg-blue-50/50');
                if (errorDisplay) errorDisplay.remove();
            }
            return isValid;
        };

        inputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('border-red-500/50')) {
                    validateField(input);
                }
            });
        });

        form.addEventListener('submit', (e) => {
            let allValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) allValid = false;
            });
            if (!allValid) {
                e.preventDefault();
                showToast('Пожалуйста, исправьте ошибки в форме', 'error');
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}

/**
 * Renders a white card container with rounded corners
 */

/**
 * Renders a booking history card
 */
function render_booking_card($booking, $car, $statusConfig = []) {
    $defaultStatuses = [
        'pending' => ['class' => 'bg-orange-50 text-orange-600', 'label' => 'В обработке'],
        'confirmed' => ['class' => 'bg-green-50 text-green-600', 'label' => 'Подтверждено'],
        'cancelled' => ['class' => 'bg-red-50 text-red-600', 'label' => 'Отменено'],
        'completed' => ['class' => 'bg-blue-50 text-blue-600', 'label' => 'Завершено']
    ];
    $statuses = array_merge($defaultStatuses, $statusConfig);
    $status = $booking['status'] ?? 'pending';
    $statusClass = $statuses[$status]['class'] ?? 'bg-gray-100 text-gray-800';
    $statusLabel = $statuses[$status]['label'] ?? 'Неизвестно';
    
    ob_start();
    ?>
    <div class="bg-white rounded-[40px] border border-gray-100 p-8 shadow-2xl shadow-gray-200/30 relative overflow-hidden group hover:border-blue-200 transition-all">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none -mr-16 -mt-16"></div>
        
        <div class="flex flex-col md:flex-row md:items-center gap-10">
            <!-- Car Image & Status -->
            <div class="w-full md:w-48 lg:w-48 shrink-0">
                <div class="aspect-[16/10] bg-gray-50/50 rounded-3xl p-4 flex items-center justify-center mb-4 border border-gray-100/50">
                    <img src="<?php echo asset_image_url($car['image']); ?>" class="max-w-full max-h-full object-contain group-hover:scale-110 transition-transform duration-700" onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
                </div>
                <div class="inline-flex px-4 py-1.5 rounded-xl <?php echo $statusClass; ?> text-[10px] font-black uppercase tracking-widest whitespace-nowrap">
                    <?php echo $statusLabel; ?>
                </div>
            </div>

            <!-- Trip Details -->
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-8 py-2">
                <div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Автомобиль</div>
                    <div class="text-xl font-black text-gray-800"><?php echo htmlspecialchars($car['name']); ?></div>
                    <div class="text-xs font-bold text-gray-400 mt-1"><?php echo $booking['id']; ?> · <?php echo date('d.m.Y', strtotime($booking['created_at'])); ?></div>
                </div>

                <div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Даты и место</div>
                    <div class="text-sm font-black text-gray-800 leading-tight">
                        <?php echo $booking['start_date']; ?> — <?php echo $booking['end_date']; ?>
                    </div>
                    <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest mt-1">
                        <?php echo htmlspecialchars($booking['pickup_location'] ?? ''); ?>
                        <?php if (!empty($booking['return_location'] ?? '')): ?>
                            <span class="text-gray-300 mx-1">→</span>
                            <?php echo htmlspecialchars($booking['return_location']); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Price -->
            <div class="w-full md:w-32 lg:w-32 text-left md:text-right">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Итого</div>
                <div class="text-2xl font-black text-gray-800 tracking-tighter">฿<?php echo (int)$booking['total_price']; ?></div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Renders a page section header
 */
