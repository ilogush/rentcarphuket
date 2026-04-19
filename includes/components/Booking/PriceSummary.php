<?php
declare(strict_types=1);

namespace Components\Booking;

use App\Services\PricingService;

/**
 * Компонент расчета стоимости аренды
 */
class PriceSummary {
    private PricingService $pricingService;

    public function __construct() {
        $this->pricingService = new PricingService();
    }

    public function render(array $car, string $pickupDate, string $returnDate, string $pickupArea = '', string $dropoffArea = ''): string {
        $priceInfo = $this->pricingService->calculate($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea);

        ob_start();
        ?>
        <div id="price-summary-container" class="bg-gray-100 rounded-[32px] p-4">
            <h3 class="text-xl font-black text-gray-800 mb-4 tracking-tight">Расчёт стоимости</h3>
            
            <div class="space-y-1 text-sm">
                <?php echo $this->renderSimpleRow('Базовая цена', '฿' . number_format($priceInfo['daily_base']) . '/день'); ?>
                <?php echo $this->renderSimpleRow('Количество дней', $priceInfo['days']); ?>
                <?php echo $this->renderSimpleRow('Подача', '฿' . number_format($priceInfo['delivery_price'])); ?>
                <?php echo $this->renderSimpleRow('Возврат', '฿' . number_format($priceInfo['return_price'])); ?>
                <?php echo $this->renderSimpleRow('Депозит', '฿' . number_format($priceInfo['deposit'])); ?>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-bold">Страховка</span>
                    <span class="font-black text-green-600 uppercase text-[10px] tracking-widest">По договору</span>
                </div>
                
                <div class="pt-2 mt-2 flex justify-between items-baseline text-blue-600">
                    <span class="font-black text-gray-800">Итого:</span>
                    <span class="text-3xl font-black" id="calc-total">
                        ฿<?php echo number_format($priceInfo['total']); ?>
                    </span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderPriceRow(string $label, array $priceInfo): string {
        ob_start();
        ?>
        <div class="flex justify-between items-center">
            <span class="text-gray-500 font-bold"><?php echo htmlspecialchars($label); ?></span>
            <div id="calc-daily" class="text-right">
                <?php if($priceInfo['daily_final'] < $priceInfo['daily_base']): ?>
                    <div class="font-black text-gray-800 leading-none">
                        ฿<?php echo number_format($priceInfo['daily_final']); ?>
                    </div>
                    <div class="text-[10px] text-gray-400 line-through">
                        ฿<?php echo number_format($priceInfo['daily_base']); ?>
                    </div>
                <?php else: ?>
                    <div class="font-black text-gray-800">
                        ฿<?php echo number_format($priceInfo['daily_base']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderSimpleRow(string $label, $value): string {
        return sprintf(
            '<div class="flex justify-between items-center"><span class="text-gray-500 font-bold">%s</span><span class="font-black text-gray-800" id="calc-%s">%s</span></div>',
            htmlspecialchars($label),
            strtolower(str_replace(' ', '-', $label)),
            htmlspecialchars((string)$value)
        );
    }
}
