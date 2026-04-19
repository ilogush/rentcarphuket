<?php
declare(strict_types=1);

namespace App\Services;

/**
 * RentalPriceCalculator
 * 
 * Standalone service for rental price calculation with detailed breakdown.
 * Suitable for similar projects requiring transparent pricing formula and flexible coefficients.
 * 
 * Features:
 * - Base daily price × rental days
 * - Season multiplier (high/low season)
 * - Car class multiplier (economy/premium)
 * - Long-term rental discounts
 * - Extra options (child seat, GPS, delivery, etc.)
 * - Platform fee (commission)
 * - Deposit handling
 * - Configurable rounding and minimum totals
 * 
 * @example
 * ```php
 * $calculator = new RentalPriceCalculator([
 *     'min_total' => 1500,
 *     'round_to' => 1,
 *     'platform_fee_percent' => 7,
 * ]);
 * 
 * $result = $calculator->calculate([
 *     'daily_price' => 2500,
 *     'days' => 10,
 *     'season_multiplier' => 1.2,
 *     'class_multiplier' => 1.1,
 *     'extras' => [
 *         'child_seat' => 300,
 *         'delivery' => 500,
 *     ],
 *     'deposit' => 10000,
 *     'include_deposit_in_total' => false,
 * ]);
 * ```
 */
final class RentalPriceCalculator
{
    /**
     * @param array{
     *   min_total?: int,
     *   round_to?: int,
     *   long_term_discounts?: array<int, array{min_days:int, percent:int}>,
     *   platform_fee_percent?: int
     * } $config
     */
    public function __construct(private array $config = [])
    {
        $this->config = array_merge([
            'min_total' => 0,               // Minimum final price
            'round_to' => 1,                // Rounding: 1 = to baht, 10 = to tens
            'long_term_discounts' => [      // Long-term rental discounts
                ['min_days' => 7,  'percent' => 5],
                ['min_days' => 14, 'percent' => 10],
                ['min_days' => 30, 'percent' => 15],
            ],
            'platform_fee_percent' => 0,    // Platform commission if needed
        ], $this->config);
    }

    /**
     * Calculate rental price with full breakdown
     * 
     * @param array{
     *   daily_price: int|float,
     *   days: int,
     *   season_multiplier?: float,
     *   class_multiplier?: float,
     *   extras?: array<string, int|float>,
     *   deposit?: int|float,
     *   include_deposit_in_total?: bool
     * } $input
     * 
     * @return array{
     *   base: int,
     *   season_adjusted: int,
     *   class_adjusted: int,
     *   extras_total: int,
     *   discount: int,
     *   platform_fee: int,
     *   deposit: int,
     *   total: int,
     *   final_total: int,
     *   breakdown: array<string, mixed>
     * }
     */
    public function calculate(array $input): array
    {
        $dailyPrice = (float)($input['daily_price'] ?? 0);
        $days = max(1, (int)($input['days'] ?? 1));
        $seasonMultiplier = (float)($input['season_multiplier'] ?? 1.0);
        $classMultiplier = (float)($input['class_multiplier'] ?? 1.0);
        $extras = $input['extras'] ?? [];
        $deposit = (float)($input['deposit'] ?? 0);
        $includeDepositInTotal = (bool)($input['include_deposit_in_total'] ?? false);

        // 1. Base price
        $base = $dailyPrice * $days;

        // 2. Season coefficient
        $seasonAdjusted = $base * $seasonMultiplier;

        // 3. Car class coefficient
        $classAdjusted = $seasonAdjusted * $classMultiplier;

        // 4. Extra options
        $extrasTotal = 0.0;
        foreach ($extras as $price) {
            $extrasTotal += (float)$price;
        }

        // 5. Long-term rental discount
        $discountPercent = $this->resolveDiscountPercent($days);
        $discount = ($classAdjusted * $discountPercent) / 100;

        // 6. Platform fee
        $platformFeePercent = (int)$this->config['platform_fee_percent'];
        $platformFee = (($classAdjusted - $discount + $extrasTotal) * $platformFeePercent) / 100;

        // 7. Subtotal
        $total = $classAdjusted - $discount + $extrasTotal + $platformFee;

        // 8. Deposit
        $finalTotal = $includeDepositInTotal ? ($total + $deposit) : $total;

        // 9. Minimum threshold
        $minTotal = (float)$this->config['min_total'];
        if ($finalTotal < $minTotal) {
            $finalTotal = $minTotal;
        }

        // 10. Rounding
        $roundTo = max(1, (int)$this->config['round_to']);
        $base = $this->roundAmount($base, $roundTo);
        $seasonAdjusted = $this->roundAmount($seasonAdjusted, $roundTo);
        $classAdjusted = $this->roundAmount($classAdjusted, $roundTo);
        $extrasTotal = $this->roundAmount($extrasTotal, $roundTo);
        $discount = $this->roundAmount($discount, $roundTo);
        $platformFee = $this->roundAmount($platformFee, $roundTo);
        $deposit = $this->roundAmount($deposit, $roundTo);
        $total = $this->roundAmount($total, $roundTo);
        $finalTotal = $this->roundAmount($finalTotal, $roundTo);

        return [
            'base' => $base,
            'season_adjusted' => $seasonAdjusted,
            'class_adjusted' => $classAdjusted,
            'extras_total' => $extrasTotal,
            'discount' => $discount,
            'platform_fee' => $platformFee,
            'deposit' => $deposit,
            'total' => $total,
            'final_total' => $finalTotal,
            'breakdown' => [
                'daily_price' => $dailyPrice,
                'days' => $days,
                'season_multiplier' => $seasonMultiplier,
                'class_multiplier' => $classMultiplier,
                'discount_percent' => $discountPercent,
                'platform_fee_percent' => $platformFeePercent,
                'include_deposit_in_total' => $includeDepositInTotal,
                'extras' => $extras,
            ],
        ];
    }

    /**
     * Returns discount percentage based on rental duration
     */
    private function resolveDiscountPercent(int $days): int
    {
        $bestPercent = 0;
        foreach ($this->config['long_term_discounts'] as $tier) {
            if ($days >= (int)$tier['min_days']) {
                $bestPercent = max($bestPercent, (int)$tier['percent']);
            }
        }
        return $bestPercent;
    }

    /**
     * Round to nearest value
     * Examples:
     * - roundTo = 1  => to baht
     * - roundTo = 10 => to tens
     */
    private function roundAmount(float $value, int $roundTo): int
    {
        return (int)(round($value / $roundTo) * $roundTo);
    }
}
