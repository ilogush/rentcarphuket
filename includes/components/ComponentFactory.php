<?php
declare(strict_types=1);

namespace Components;

/**
 * Фабрика для создания компонентов
 * Централизованное управление созданием компонентов
 */
class ComponentFactory {
    private static ?self $instance = null;
    private array $components = [];

    private function __construct() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Создает или возвращает существующий экземпляр компонента
     */
    public function make(string $componentClass): object {
        if (!isset($this->components[$componentClass])) {
            $this->components[$componentClass] = new $componentClass();
        }
        return $this->components[$componentClass];
    }

    /**
     * Рендерит компонент с переданными свойствами
     */
    public function render(string $componentClass, array $props = []): string {
        $component = $this->make($componentClass);
        
        if (method_exists($component, 'render')) {
            return $component->render($props);
        }
        
        throw new \Exception("Component $componentClass does not have a render method");
    }

    // Удобные методы для часто используемых компонентов
    
    public function button(array $props): string {
        return $this->render(\Components\UI\Button::class, $props);
    }

    public function card(array $props): string {
        return $this->render(\Components\UI\Card::class, $props);
    }

    public function modal(array $props): string {
        return $this->render(\Components\UI\Modal::class, $props);
    }

    public function badge(array $props): string {
        return $this->render(\Components\UI\Badge::class, $props);
    }

    public function input(array $props): string {
        return $this->render(\Components\UI\Input::class, $props);
    }

    public function carCard(array $car): string {
        return $this->make(\Components\Car\CarCard::class)->render($car);
    }

    public function carGrid(array $cars, array $options = []): string {
        return $this->make(\Components\Car\CarGrid::class)->render($cars, $options);
    }

    public function searchBox(array $locations = []): string {
        return $this->make(\Components\Search\SearchBox::class)->render($locations);
    }

    public function priceSummary(array $car, string $pickupDate, string $returnDate, string $pickupArea = '', string $dropoffArea = ''): string {
        return $this->make(\Components\Booking\PriceSummary::class)->render($car, $pickupDate, $returnDate, $pickupArea, $dropoffArea);
    }

    public function table(array $props): string {
        return $this->render(\Components\UI\Table::class, $props);
    }

    public function form(array $props): string {
        return $this->render(\Components\UI\Form::class, $props);
    }

    public function select(array $props): string {
        return $this->render(\Components\UI\Select::class, $props);
    }

    public function alert(array $props): string {
        return $this->render(\Components\UI\Alert::class, $props);
    }

    public function toast(array $props): string {
        return $this->make(\Components\UI\Alert::class)->toast($props);
    }

    public function stat(array $props): string {
        return $this->render(\Components\UI\Stat::class, $props);
    }
}

/**
 * Глобальная функция-хелпер для доступа к фабрике
 */
function component(): ComponentFactory {
    return ComponentFactory::getInstance();
}
