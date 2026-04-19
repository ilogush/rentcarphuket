<?php
declare(strict_types=1);

/**
 * RentalPriceCalculator Usage Examples
 * 
 * This file demonstrates various use cases for the pricing calculator
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\RentalPriceCalculator;

echo "=== RentalPriceCalculator Examples ===\n\n";

// Example 1: Basic calculation
echo "1. Basic Calculation (3 days, no extras)\n";
echo str_repeat('-', 50) . "\n";

$calculator = new RentalPriceCalculator([
    'min_total' => 1500,
    'round_to' => 1,
]);

$result = $calculator->calculate([
    'daily_price' => 2500,
    'days' => 3,
]);

printResult($result);

// Example 2: With season and class multipliers
echo "\n2. High Season + Premium Car (5 days)\n";
echo str_repeat('-', 50) . "\n";

$result = $calculator->calculate([
    'daily_price' => 2000,
    'days' => 5,
    'season_multiplier' => 1.3,  // High season +30%
    'class_multiplier' => 1.2,   // Premium class +20%
]);

printResult($result);

// Example 3: Long-term rental with discount
echo "\n3. Long-term Rental (14 days = 10% discount)\n";
echo str_repeat('-', 50) . "\n";

$result = $calculator->calculate([
    'daily_price' => 2500,
    'days' => 14,
    'season_multiplier' => 1.0,
    'class_multiplier' => 1.0,
]);

printResult($result);

// Example 4: Full featured with extras
echo "\n4. Full Featured (10 days + extras + platform fee)\n";
echo str_repeat('-', 50) . "\n";

$calculatorWithFee = new RentalPriceCalculator([
    'min_total' => 1500,
    'round_to' => 1,
    'platform_fee_percent' => 7,
]);

$result = $calculatorWithFee->calculate([
    'daily_price' => 2500,
    'days' => 10,
    'season_multiplier' => 1.2,
    'class_multiplier' => 1.1,
    'extras' => [
        'child_seat' => 300,
        'gps' => 200,
        'delivery' => 500,
    ],
    'deposit' => 10000,
    'include_deposit_in_total' => false,
]);

printResult($result);

// Example 5: With deposit included in total
echo "\n5. With Deposit Included in Total\n";
echo str_repeat('-', 50) . "\n";

$result = $calculator->calculate([
    'daily_price' => 2000,
    'days' => 7,
    'deposit' => 5000,
    'include_deposit_in_total' => true,
]);

printResult($result);

// Example 6: 30-day rental (maximum discount)
echo "\n6. Monthly Rental (30 days = 15% discount)\n";
echo str_repeat('-', 50) . "\n";

$result = $calculator->calculate([
    'daily_price' => 1800,
    'days' => 30,
    'season_multiplier' => 0.9,  // Low season -10%
]);

printResult($result);

/**
 * Helper function to print results
 */
function printResult(array $result): void
{
    echo "Base Price:           {$result['base']} ฿\n";
    echo "After Season:         {$result['season_adjusted']} ฿\n";
    echo "After Class:          {$result['class_adjusted']} ฿\n";
    
    if ($result['discount'] > 0) {
        echo "Discount:             -{$result['discount']} ฿ ({$result['breakdown']['discount_percent']}%)\n";
    }
    
    if ($result['extras_total'] > 0) {
        echo "Extras:               +{$result['extras_total']} ฿\n";
        foreach ($result['breakdown']['extras'] as $name => $price) {
            echo "  - {$name}: {$price} ฿\n";
        }
    }
    
    if ($result['platform_fee'] > 0) {
        echo "Platform Fee:         +{$result['platform_fee']} ฿ ({$result['breakdown']['platform_fee_percent']}%)\n";
    }
    
    echo "Subtotal:             {$result['total']} ฿\n";
    
    if ($result['deposit'] > 0) {
        $included = $result['breakdown']['include_deposit_in_total'] ? 'included' : 'separate';
        echo "Deposit:              {$result['deposit']} ฿ ({$included})\n";
    }
    
    echo "\nFINAL TOTAL:          {$result['final_total']} ฿\n";
    echo "Daily Rate:           " . round($result['final_total'] / $result['breakdown']['days']) . " ฿/day\n";
}
