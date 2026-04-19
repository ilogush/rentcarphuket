<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/layout.php';

render_layout('Калькулятор стоимости аренды', function($data) {
    $cars = $data['cars'];
    $locations = $data['locations'];
    ?>
    
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-black text-gray-800 mb-4 tracking-tight">
                    Калькулятор стоимости аренды
                </h1>
                <p class="text-gray-600 text-lg">
                    Рассчитайте точную стоимость аренды автомобиля с учетом всех параметров
                </p>
            </div>

            <!-- Calculator Form -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-8 mb-8">
                <form id="calculator-form" class="space-y-6">
                    
                    <!-- Car Selection -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Выберите автомобиль
                        </label>
                        <select id="car-select" name="car_id" required 
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:outline-none transition-colors">
                            <option value="">-- Выберите автомобиль --</option>
                            <?php foreach ($cars as $car): ?>
                                <option value="<?php echo $car['id']; ?>" 
                                        data-price="<?php echo $car['price']; ?>"
                                        data-discount="<?php echo $car['discount'] ?? 0; ?>">
                                    <?php echo htmlspecialchars($car['name']); ?> 
                                    (฿<?php echo number_format($car['price']); ?>/день)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Дата получения
                            </label>
                            <input type="date" id="pickup-date" name="pickup_date" required
                                   value="<?php echo date('Y-m-d'); ?>"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Дата возврата
                            </label>
                            <input type="date" id="return-date" name="return_date" required
                                   value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <!-- Locations -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Место подачи
                            </label>
                            <select id="pickup-area" name="pickup_area"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:outline-none transition-colors">
                                <option value="">Без доставки</option>
                                <?php foreach ($locations as $loc): ?>
                                    <?php $deliveryPrice = (float)($loc['delivery_price'] ?? 0); ?>
                                    <option value="<?php echo htmlspecialchars($loc['name']); ?>"
                                            data-price="<?php echo $deliveryPrice; ?>">
                                        <?php echo htmlspecialchars($loc['name']); ?>
                                        <?php if ($deliveryPrice > 0): ?>
                                            (+฿<?php echo number_format($deliveryPrice); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                Место возврата
                            </label>
                            <select id="dropoff-area" name="dropoff_area"
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:outline-none transition-colors">
                                <option value="">Без доставки</option>
                                <?php foreach ($locations as $loc): ?>
                                    <?php $deliveryPrice = (float)($loc['delivery_price'] ?? 0); ?>
                                    <option value="<?php echo htmlspecialchars($loc['name']); ?>"
                                            data-price="<?php echo $deliveryPrice; ?>">
                                        <?php echo htmlspecialchars($loc['name']); ?>
                                        <?php if ($deliveryPrice > 0): ?>
                                            (+฿<?php echo number_format($deliveryPrice); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Calculate Button -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                        Рассчитать стоимость
                    </button>
                </form>
            </div>

            <!-- Results -->
            <div id="calculation-results" class="hidden">
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-200">
                    <h2 class="text-2xl font-black text-gray-800 mb-6">Детализация расчета</h2>
                    
                    <div id="breakdown-content" class="space-y-3 text-sm">
                        <!-- Будет заполнено через JS -->
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('calculator-form');
        const resultsDiv = document.getElementById('calculation-results');
        const breakdownContent = document.getElementById('breakdown-content');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const params = new URLSearchParams();
            
            params.append('car_id', formData.get('car_id'));
            
            // Конвертируем даты из YYYY-MM-DD в DD.MM.YYYY
            const pickupDate = new Date(formData.get('pickup_date'));
            const returnDate = new Date(formData.get('return_date'));
            params.append('pickup_date', formatDate(pickupDate));
            params.append('return_date', formatDate(returnDate));
            params.append('pickup_area', formData.get('pickup_area'));
            params.append('dropoff_area', formData.get('dropoff_area'));

            try {
                const response = await fetch(`/api/calculate_price.php?${params.toString()}`);
                const data = await response.json();

                if (data.error) {
                    notifyError('Ошибка: ' + data.error);
                    return;
                }

                displayResults(data, formData);
                resultsDiv.classList.remove('hidden');
                resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } catch (error) {
                console.error('Calculation error:', error);
                notifyError('Произошла ошибка при расчете. Попробуйте еще раз.');
            }
        });

        function notifyError(message) {
            if (typeof showToast === 'function') {
                showToast(message, 'error');
                return;
            }

            console.error(message);
        }

        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}.${month}.${year}`;
        }

        function displayResults(data, formData) {
            const pickupArea = formData.get('pickup_area');
            const dropoffArea = formData.get('dropoff_area');

            let html = '';

            // Базовая цена
            html += renderRow('Базовая цена', `฿${formatNumber(data.daily_base)} / день`);

            // Коэффициент длительности (если есть)
            if (data.duration_rate && Number(data.duration_rate) !== 1 && data.duration_label) {
                html += renderRow('Длительность', data.duration_label, Number(data.duration_rate) < 1 ? 'text-green-600' : 'text-orange-600');
            }

            // Сезонный коэффициент (если есть)
            if (data.season_rate && Number(data.season_rate) !== 1 && data.season_label) {
                html += renderRow('Сезон', data.season_label, Number(data.season_rate) > 1 ? 'text-orange-600' : '');
            }

            // Скидка автомобиля (если есть)
            if (data.discount_pct > 0) {
                html += renderRow('Скидка авто', `-${data.discount_pct}%`, 'text-green-600');
            }

            html += '<div class="border-t border-gray-300 my-3"></div>';

            // Цена за день после скидок
            html += renderRow('Цена за день', `฿${formatNumber(data.daily_price)}`, 'font-bold');
            
            // Количество дней
            html += renderRow('Количество дней', `${data.days} дн.`);

            // Доставка
            if (data.delivery_price > 0) {
                html += renderRow(`Доставка (${pickupArea})`, `฿${formatNumber(data.delivery_price)}`);
            }

            // Возврат
            if (data.return_price > 0) {
                html += renderRow(`Возврат (${dropoffArea})`, `฿${formatNumber(data.return_price)}`);
            }

            html += '<div class="border-t-2 border-gray-300 my-4"></div>';

            // Итого
            html += `
                <div class="flex justify-between items-center py-3 bg-blue-50 px-4 rounded-xl">
                    <span class="font-black text-gray-800 text-lg">Итого</span>
                    <span class="text-3xl font-black text-blue-600">฿${formatNumber(data.total)}</span>
                </div>
            `;

            // Депозит
            html += `
                <div class="flex justify-between items-center text-sm text-gray-600 mt-3 px-2">
                    <span>Депозит (возвращается)</span>
                    <span class="font-bold">฿${formatNumber(data.deposit)}</span>
                </div>
            `;

            // Страховка
            html += `
                <div class="text-sm text-green-600 font-semibold mt-3 px-2">
                    ✓ Страховка по договору
                </div>
            `;

            breakdownContent.innerHTML = html;
        }

        function renderRow(label, value, extraClass = '') {
            return `
                <div class="flex justify-between items-center ${extraClass}">
                    <span class="text-gray-600">${label}</span>
                    <span class="text-gray-800 font-semibold">${value}</span>
                </div>
            `;
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('ru-RU').format(num);
        }
    });
    </script>

    <?php
});
