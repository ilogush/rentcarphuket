<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Centralized pricing calculator with transparent breakdown
 * 
 * Features:
 * - Duration-based multipliers
 * - Seasonal pricing
 * - Car-specific discounts
 * - Delivery/return fees
 * - Configurable rounding and minimums
 * - Detailed price breakdown
 */
class PricingService
{
    private static ?array $durations = null;
    private static ?array $seasons = null;
    private static ?array $locations = null;
    
    private array $config;

    /**
     * @param array{
     *   min_total?: int,
     *   round_to?: int,
     *   platform_fee_percent?: int
     * } $config
     */
    public function __construct(array $config = [])
    {
        // Load data only once per request (static caching)
        if (self::$durations === null) {
            $durationRepo = new \App\Repositories\DurationRepository();
            self::$durations = $durationRepo->getAll();
        }

        if (self::$seasons === null) {
            $seasonRepo = new \App\Repositories\SeasonRepository();
            self::$seasons = $seasonRepo->getAll();
        }

        if (self::$locations === null) {
            $locRepo = new \App\Repositories\LocationRepository();
            self::$locations = $locRepo->getAll();
        }
        
        $this->config = array_merge([
            'min_total' => 0,
            'round_to' => 1,
            'platform_fee_percent' => 0,
        ], $config);
    }

    /**
     * Calculate full rental price with detailed breakdown
     * 
     * @param array $car Car data with price, deposit, discount info
     * @param string $startDate Pickup date (d.m.Y format)
     * @param string $endDate Return date (d.m.Y format)
     * @param string $pickupArea Pickup location name
     * @param string $dropoffArea Dropoff location name
     * @param array $extras Additional options ['name' => price]
     * 
     * @return array{
     *   daily_base: float,
     *   daily_final: float,
     *   days: int,
     *   total: float,
     *   duration_rate: float,
     *   duration_label: string,
     *   season_rate: float,
     *   season_label: string,
     *   discount_pct: float,
     *   delivery_price: float,
     *   return_price: float,
     *   deposit: float,
     *   breakdown: array,
     *   calculation_steps: array
     * }
     */
    public function calculate(
        array $car, 
        string $startDate, 
        string $endDate, 
        string $pickupArea = '', 
        string $dropoffArea = '',
        array $extras = []
    ): array {
        $d1 = \DateTime::createFromFormat('d.m.Y', $startDate);
        $d2 = \DateTime::createFromFormat('d.m.Y', $endDate);

        if (!$d1 || !$d2) {
            return $this->fallback($car, 1);
        }

        $days = max(1, (int)$d1->diff($d2)->days);
        $basePrice = (float)($car['price'] ?? 0);
        $roundTo = max(1, (int)$this->config['round_to']);

        // 1. Base price (daily × days)
        $base = $basePrice * $days;

        // 2. Duration multiplier
        [$durationRate, $durationLabel] = $this->getDurationRate($days);
        $afterDuration = $base * $durationRate;

        // 3. Season multiplier
        [$seasonRate, $seasonLabel] = $this->getSeasonRate($d1);
        $afterSeason = $afterDuration * $seasonRate;

        // 4. Car-specific discount
        $discountPct = $this->getDiscountPercent($car);
        $discountAmount = ($afterSeason * $discountPct) / 100;
        $afterDiscount = $afterSeason - $discountAmount;

        // 5. Extras (child seat, GPS, etc.)
        $extrasTotal = 0.0;
        foreach ($extras as $price) {
            $extrasTotal += (float)$price;
        }

        // 6. Delivery/return fees
        $deliveryPrice = $this->getLocationPrice($pickupArea);
        $returnPrice = $this->getLocationPrice($dropoffArea);

        // 7. Platform fee (if configured)
        $platformFeePercent = (int)$this->config['platform_fee_percent'];
        $platformFee = (($afterDiscount + $extrasTotal) * $platformFeePercent) / 100;

        // 8. Total calculation
        $total = $afterDiscount + $extrasTotal + $deliveryPrice + $returnPrice + $platformFee;

        // 9. Apply minimum threshold
        $minTotal = (float)$this->config['min_total'];
        if ($total < $minTotal) {
            $total = $minTotal;
        }

        // 10. Rounding
        $dailyFinal = $this->roundAmount($afterDiscount / $days, $roundTo);
        $total = $this->roundAmount($total, $roundTo);
        $deposit = $this->roundAmount((float)($car['deposit'] ?? 5000), $roundTo);

        return [
            'daily_base' => $basePrice,
            'daily_final' => $dailyFinal,
            'days' => $days,
            'total' => $total,
            'duration_rate' => $durationRate,
            'duration_label' => $durationLabel,
            'season_rate' => $seasonRate,
            'season_label' => $seasonLabel,
            'discount_pct' => $discountPct,
            'delivery_price' => $deliveryPrice,
            'return_price' => $returnPrice,
            'deposit' => $deposit,
            'extras_total' => $extrasTotal,
            'platform_fee' => $platformFee,
            'breakdown' => $this->buildBreakdown(
                $basePrice, 
                $durationRate, 
                $durationLabel, 
                $seasonRate, 
                $seasonLabel, 
                $discountPct, 
                $dailyFinal, 
                $pickupArea, 
                $deliveryPrice, 
                $dropoffArea, 
                $returnPrice, 
                $days, 
                $total,
                $extras,
                $extrasTotal
            ),
            'calculation_steps' => [
                'base' => $this->roundAmount($base, $roundTo),
                'after_duration' => $this->roundAmount($afterDuration, $roundTo),
                'after_season' => $this->roundAmount($afterSeason, $roundTo),
                'discount_amount' => $this->roundAmount($discountAmount, $roundTo),
                'after_discount' => $this->roundAmount($afterDiscount, $roundTo),
                'extras_total' => $this->roundAmount($extrasTotal, $roundTo),
                'platform_fee' => $this->roundAmount($platformFee, $roundTo),
            ],
        ];
    }

    private function getDurationRate(int $days): array {
        foreach (self::$durations as $dur) {
            if ($days >= (int)$dur['min_days'] && $days <= (int)$dur['max_days']) {
                return [(float)$dur['rate'], $dur['label'] ?? ''];
            }
        }
        return [1.0, ''];
    }

    private function getSeasonRate(\DateTime $date): array {
        $monthDay = $date->format('m-d');
        foreach (self::$seasons as $season) {
            if ($this->isInSeason($monthDay, $season['start_date'], $season['end_date'])) {
                return [
                    (float)$season['multiplier'],
                    $season['season'] . ' (' . ($season['label'] ?? '') . ')'
                ];
            }
        }
        return [1.0, ''];
    }

    private function getDiscountPercent(array $car): float {
        if (empty($car['discount'])) return 0.0;
        
        if (empty($car['discount_start']) || empty($car['discount_end'])) {
            return (float)$car['discount'];
        }
        
        try {
            $today = new \DateTime('today');
            $start = \DateTime::createFromFormat('d.m.Y', $car['discount_start']);
            $end = \DateTime::createFromFormat('d.m.Y', $car['discount_end']);
            
            if ($start && $end) {
                $end->setTime(23, 59, 59);
                if ($today >= $start && $today <= $end) {
                    return (float)$car['discount'];
                }
            }
        } catch (\Exception $e) {
            // Fallback to active discount
        }
        
        return 0.0;
    }

    private function getLocationPrice(string $area): float {
        $area = trim($area);
        if ($area === '') return 0.0;
        
        foreach (self::$locations as $loc) {
            $locationName = (string)($loc['name'] ?? '');
            if ($locationName === $area || $this->isSameKnownLocation($locationName, $area)) {
                return (float)($loc['delivery_price'] ?? 0);
            }
        }
        return 0.0;
    }

    private function isSameKnownLocation(string $storedName, string $inputName): bool {
        $storedName = mb_strtolower($storedName);
        $inputName = mb_strtolower($inputName);

        return str_contains($storedName, 'hkt') && str_contains($inputName, 'hkt');
    }

    private function isInSeason(string $monthDay, string $start, string $end): bool {
        return $start <= $end 
            ? ($monthDay >= $start && $monthDay <= $end)
            : ($monthDay >= $start || $monthDay <= $end);
    }
    
    /**
     * Round amount to nearest value
     * - roundTo = 1  => to baht
     * - roundTo = 10 => to tens
     */
    private function roundAmount(float $value, int $roundTo): float {
        return round($value / $roundTo) * $roundTo;
    }

    private function buildBreakdown(
        float $basePrice, 
        float $durationRate, 
        string $durationLabel, 
        float $seasonRate, 
        string $seasonLabel, 
        float $discountPct, 
        float $dailyFinal, 
        string $pickupArea, 
        float $deliveryPrice, 
        string $dropoffArea, 
        float $returnPrice, 
        int $days, 
        float $total,
        array $extras = [],
        float $extrasTotal = 0.0
    ): array {
        $breakdown = [
            ['label' => 'Базовая цена', 'value' => $basePrice, 'suffix' => '/день'],
        ];
        
        if ($durationRate != 1.0) {
            $breakdown[] = ['label' => 'Длительность', 'value' => $durationLabel, 'suffix' => ''];
        }
        
        if ($seasonRate != 1.0) {
            $breakdown[] = ['label' => 'Сезон', 'value' => $seasonLabel, 'suffix' => ''];
        }
        
        if ($discountPct > 0) {
            $breakdown[] = ['label' => 'Скидка авто', 'value' => "-{$discountPct}%", 'suffix' => ''];
        }
        
        $breakdown[] = ['label' => 'Цена за день', 'value' => $dailyFinal, 'suffix' => '฿'];
        
        if (!empty($extras)) {
            foreach ($extras as $name => $price) {
                $breakdown[] = ['label' => ucfirst($name), 'value' => $price, 'suffix' => '฿'];
            }
        }
        
        if ($deliveryPrice > 0) {
            $breakdown[] = ['label' => "Доставка ({$pickupArea})", 'value' => $deliveryPrice, 'suffix' => '฿'];
        }
        
        if ($returnPrice > 0) {
            $breakdown[] = ['label' => "Возврат ({$dropoffArea})", 'value' => $returnPrice, 'suffix' => '฿'];
        }
        
        $breakdown[] = ['label' => "Итого ({$days} дн.)", 'value' => $total, 'suffix' => '฿'];
        
        return $breakdown;
    }

    private function fallback(array $car, int $days): array {
        $price = (float)($car['price'] ?? 0);
        return [
            'daily_base' => $price,
            'daily_final' => $price,
            'days' => $days,
            'total' => $price * $days,
            'duration_rate' => 1.0,
            'duration_label' => '',
            'season_rate' => 1.0,
            'season_label' => '',
            'discount_pct' => 0,
            'delivery_price' => 0.0,
            'return_price' => 0.0,
            'deposit' => (float)($car['deposit'] ?? 5000),
            'extras_total' => 0.0,
            'platform_fee' => 0.0,
            'breakdown' => [],
            'calculation_steps' => [],
        ];
    }
}
