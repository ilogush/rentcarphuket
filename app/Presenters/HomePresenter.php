<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Repositories\CarRepository;
use App\Repositories\LocationRepository;

/**
 * Презентер для главной страницы
 * Отвечает за подготовку данных и логику отображения
 */
class HomePresenter {
    private CarRepository $carRepo;
    private LocationRepository $locationRepo;

    public function __construct() {
        $this->carRepo = new CarRepository();
        $this->locationRepo = new LocationRepository();
    }

    /**
     * Получает данные для главной страницы
     */
    public function getData(): array {
        return [
            'cars' => $this->getActiveCars(),
            'locations' => $this->locationRepo->getAll(),
            'hero' => $this->getHeroData(),
        ];
    }

    /**
     * Получает только активные автомобили
     */
    private function getActiveCars(): array {
        return array_filter($this->carRepo->getAll(), function($car) {
            return ($car['status'] ?? 'active') === 'active';
        });
    }

    /**
     * Получает данные для hero-секции
     */
    private function getHeroData(): array {
        return [
            'title' => 'Аренда авто Пхукет',
            'subtitle' => 'Лучшие условия аренды и премиальный сервис на острове',
            'bgImage' => 'https://images.unsplash.com/photo-1519451241324-20b4ea2c4220?auto=format&fit=crop&q=80&w=2070',
        ];
    }

    /**
     * Рендерит главную страницу
     */
    public function render(): string {
        $data = $this->getData();
        
        ob_start();
        ?>
        <?php
        echo \render_hero(
            $data['hero']['title'],
            $data['hero']['subtitle'],
            $data['hero']['bgImage'],
            $data['locations']
        );
        echo \render_car_section($data['cars']);
        echo \render_home_scripts();
        ?>
        <?php
        return ob_get_clean();
    }
}
