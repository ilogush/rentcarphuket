<?php
declare(strict_types=1);

namespace Components\Car;

/**
 * Компонент карточки автомобиля
 */
class CarCard {
    public function render(array $car): string {
        return \render_car_card($car);
    }

    private function isDiscountActive(array $car): bool {
        if (empty($car['discount'])) return false;
        if (empty($car['discount_start']) || empty($car['discount_end'])) return true;
        
        try {
            $today = new \DateTime('today');
            $start = \DateTime::createFromFormat('d.m.Y', $car['discount_start']);
            $end = \DateTime::createFromFormat('d.m.Y', $car['discount_end']);

            if (!$start || !$end) return true;
            
            $end->setTime(23, 59, 59);
            return $today >= $start && $today <= $end;
        } catch (\Exception $e) {
            return true;
        }
    }
}
