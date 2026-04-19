# Rental Price Calculator Documentation

Прозрачный и гибкий калькулятор стоимости аренды с детальной разбивкой цены.

## Возможности

- ✅ Базовая цена × количество дней
- ✅ Сезонные коэффициенты (высокий/низкий сезон)
- ✅ Коэффициенты класса авто (эконом/премиум)
- ✅ Скидки за длительную аренду (7/14/30+ дней)
- ✅ Дополнительные опции (детское кресло, GPS, доставка)
- ✅ Комиссия платформы
- ✅ Обработка залога
- ✅ Настраиваемое округление и минимальные суммы
- ✅ Детальная разбивка расчета

## Установка

### PHP версия

```php
require_once __DIR__ . '/vendor/autoload.php';
use App\Services\RentalPriceCalculator;
```

### JavaScript версия

```html
<script src="assets/js/rental-price-calculator.js"></script>
```

## Использование

### PHP

#### Базовый пример

```php
$calculator = new RentalPriceCalculator([
    'min_total' => 1500,
    'round_to' => 1,
]);

$result = $calculator->calculate([
    'daily_price' => 2500,
    'days' => 3,
]);

echo "Total: {$result['final_total']} ฿\n";
```

#### Полный пример с опциями

```php
$calculator = new RentalPriceCalculator([
    'min_total' => 1500,
    'round_to' => 1,
    'platform_fee_percent' => 7,
    'long_term_discounts' => [
        ['min_days' => 7,  'percent' => 5],
        ['min_days' => 14, 'percent' => 10],
        ['min_days' => 30, 'percent' => 15],
    ],
]);

$result = $calculator->calculate([
    'daily_price' => 2500,
    'days' => 10,
    'season_multiplier' => 1.2,      // Высокий сезон +20%
    'class_multiplier' => 1.1,       // Премиум класс +10%
    'extras' => [
        'child_seat' => 300,
        'gps' => 200,
        'delivery' => 500,
    ],
    'deposit' => 10000,
    'include_deposit_in_total' => false,
]);

print_r($result);
```

### JavaScript

```javascript
const calculator = new RentalPriceCalculator({
  minTotal: 1500,
  roundTo: 1,
  platformFeePercent: 7,
});

const result = calculator.calculate({
  dailyPrice: 2500,
  days: 10,
  seasonMultiplier: 1.2,
  classMultiplier: 1.1,
  extras: {
    childSeat: 300,
    delivery: 500,
  },
  deposit: 10000,
  includeDepositInTotal: false,
});

console.log('Total:', result.finalTotal);
```

## Конфигурация

### PHP

```php
[
    'min_total' => 0,               // Минимальная итоговая цена
    'round_to' => 1,                // Округление: 1 = до рубля, 10 = до десятков
    'long_term_discounts' => [      // Скидки за длительную аренду
        ['min_days' => 7,  'percent' => 5],
        ['min_days' => 14, 'percent' => 10],
        ['min_days' => 30, 'percent' => 15],
    ],
    'platform_fee_percent' => 0,    // Комиссия платформы
]
```

### JavaScript

```javascript
{
  minTotal: 0,
  roundTo: 1,
  longTermDiscounts: [
    { minDays: 7, percent: 5 },
    { minDays: 14, percent: 10 },
    { minDays: 30, percent: 15 },
  ],
  platformFeePercent: 0,
}
```

## Входные параметры

| Параметр | Тип | Обязательный | Описание |
|----------|-----|--------------|----------|
| `daily_price` | float | ✅ | Базовая цена за день |
| `days` | int | ✅ | Количество дней аренды |
| `season_multiplier` | float | ❌ | Сезонный коэффициент (по умолчанию 1.0) |
| `class_multiplier` | float | ❌ | Коэффициент класса авто (по умолчанию 1.0) |
| `extras` | array/object | ❌ | Дополнительные опции {название: цена} |
| `deposit` | float | ❌ | Сумма залога |
| `include_deposit_in_total` | bool | ❌ | Включить залог в итоговую сумму |

## Возвращаемые данные

```php
[
    'base' => 25000,                    // Базовая стоимость (daily_price × days)
    'season_adjusted' => 30000,         // После сезонного коэффициента
    'class_adjusted' => 33000,          // После коэффициента класса
    'extras_total' => 1000,             // Сумма дополнительных опций
    'discount' => 1650,                 // Скидка за длительность
    'platform_fee' => 2294,             // Комиссия платформы
    'deposit' => 10000,                 // Залог
    'total' => 34644,                   // Итог без залога
    'final_total' => 34644,             // Итоговая сумма
    'breakdown' => [                    // Детали расчета
        'daily_price' => 2500,
        'days' => 10,
        'season_multiplier' => 1.2,
        'class_multiplier' => 1.1,
        'discount_percent' => 5,
        'platform_fee_percent' => 7,
        'include_deposit_in_total' => false,
        'extras' => [...],
    ],
]
```

## Формула расчета

```
1. base = daily_price × days
2. season_adjusted = base × season_multiplier
3. class_adjusted = season_adjusted × class_multiplier
4. discount = class_adjusted × (discount_percent / 100)
5. extras_total = sum(extras)
6. platform_fee = (class_adjusted - discount + extras_total) × (platform_fee_percent / 100)
7. total = class_adjusted - discount + extras_total + platform_fee
8. final_total = total [+ deposit if included]
9. Apply min_total threshold
10. Round to nearest round_to value
```

## Примеры использования

### Пример 1: Короткая аренда (3 дня)

```php
$result = $calculator->calculate([
    'daily_price' => 2500,
    'days' => 3,
]);
// final_total: 7500 ฿
```

### Пример 2: Недельная аренда со скидкой 5%

```php
$result = $calculator->calculate([
    'daily_price' => 2500,
    'days' => 7,
]);
// base: 17500
// discount: 875 (5%)
// final_total: 16625 ฿
```

### Пример 3: Высокий сезон + премиум класс

```php
$result = $calculator->calculate([
    'daily_price' => 2000,
    'days' => 5,
    'season_multiplier' => 1.3,  // +30%
    'class_multiplier' => 1.2,   // +20%
]);
// base: 10000
// after season: 13000
// after class: 15600
// final_total: 15600 ฿
```

### Пример 4: Месячная аренда (максимальная скидка 15%)

```php
$result = $calculator->calculate([
    'daily_price' => 1800,
    'days' => 30,
]);
// base: 54000
// discount: 8100 (15%)
// final_total: 45900 ฿
```

## Интеграция с существующим PricingService

Новый `RentalPriceCalculator` полностью совместим с существующим `PricingService`. Основные улучшения:

1. **Детальная разбивка** - новое поле `calculation_steps` показывает каждый шаг расчета
2. **Поддержка extras** - можно передавать дополнительные опции
3. **Гибкая конфигурация** - настраиваемые скидки, округление, минимумы
4. **Комиссия платформы** - опциональная комиссия для маркетплейсов

## Демо

Откройте `examples/calculator-demo.html` в браузере для интерактивной демонстрации.

Или запустите PHP пример:

```bash
php examples/pricing-calculator-example.php
```

## TypeScript типы

```typescript
interface CalculatorConfig {
  minTotal?: number;
  roundTo?: number;
  longTermDiscounts?: Array<{ minDays: number; percent: number }>;
  platformFeePercent?: number;
}

interface CalculationInput {
  dailyPrice: number;
  days: number;
  seasonMultiplier?: number;
  classMultiplier?: number;
  extras?: Record<string, number>;
  deposit?: number;
  includeDepositInTotal?: boolean;
}

interface CalculationResult {
  base: number;
  seasonAdjusted: number;
  classAdjusted: number;
  extrasTotal: number;
  discount: number;
  platformFee: number;
  deposit: number;
  total: number;
  finalTotal: number;
  breakdown: {
    dailyPrice: number;
    days: number;
    seasonMultiplier: number;
    classMultiplier: number;
    discountPercent: number;
    platformFeePercent: number;
    includeDepositInTotal: boolean;
    extras: Record<string, number>;
  };
}
```

## Лицензия

MIT
