<?php
declare(strict_types=1);

namespace Components\Booking;

use App\Services\PricingService;

/**
 * Визуальный калькулятор с детализацией расчета стоимости
 */
class PriceBreakdown {
    private PricingService $pricingService;

    public function __construct() {
        $this->pricingService = new PricingService();
    }

    public function render(array $car, string $pickupDate, string $returnDate, string $pickupArea = '', string $dropoffArea = ''): string {
        $priceInfo = $this->pricingService->calculate($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea);

        ob_start();
        ?>
        <div id="price-breakdown-container" class="bg-gray-50 rounded-2xl p-5">
            <h3 class="text-lg font-black text-gray-800 mb-4">Расчёт стоимости</h3>
            
            <div class="space-y-1 text-sm">
                <?php echo $this->renderRow('Базовая цена', '฿' . number_format($priceInfo['daily_base']) . ' / день'); ?>
                <?php echo $this->renderRow('Количество дней', $priceInfo['days'] . ' дн.'); ?>

                <?php if ($priceInfo['delivery_price'] > 0): ?>
                    <?php echo $this->renderRow('Доставка (' . htmlspecialchars($pickupArea) . ')', '฿' . number_format($priceInfo['delivery_price'])); ?>
                <?php endif; ?>

                <?php if ($priceInfo['return_price'] > 0): ?>
                    <?php echo $this->renderRow('Возврат (' . htmlspecialchars($dropoffArea) . ')', '฿' . number_format($priceInfo['return_price'])); ?>
                <?php endif; ?>

                <div class="flex justify-between items-center py-2">
                    <span>Депозит</span>
                    <span class="font-bold">฿<?php echo number_format($priceInfo['deposit']); ?></span>
                </div>

                <div class="flex justify-between items-center py-2">
                    <span>Страховка</span>
                    <span class="text-green-600 font-semibold">По договору</span>
                </div>

                <div class="flex justify-between items-center py-2">
                    <span class="font-black text-gray-800">Итого:</span>
                    <span class="text-2xl font-black text-blue-600" id="calc-total">
                        ฿<?php echo number_format($priceInfo['total']); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderRow(string $label, string $value, string $extraClass = ''): string {
        return sprintf(
            '<div class="flex justify-between items-center %s"><span class="text-gray-600">%s</span><span class="text-gray-800 font-semibold">%s</span></div>',
            $extraClass,
            htmlspecialchars($label),
            $value
        );
    }
}
