<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Repositories\CarRepository;
use App\Repositories\LocationRepository;

/**
 * Презентер для страницы деталей автомобиля
 */
class CarDetailPresenter {
    private CarRepository $carRepo;
    private LocationRepository $locationRepo;

    public function __construct() {
        $this->carRepo = new CarRepository();
        $this->locationRepo = new LocationRepository();
    }

    /**
     * Получает данные для страницы деталей автомобиля
     */
    public function getData(int $carId, array $params = []): array {
        $car = $this->carRepo->getById($carId);
        
        if (!$car) {
            return ['error' => 'Car not found'];
        }

        $locations = $this->locationRepo->getAll();
        
        return [
            'car' => $car,
            'locations' => $locations,
            'params' => $this->normalizeParams($params, $locations),
            'similarCars' => $this->getSimilarCars($car),
        ];
    }

    /**
     * Нормализует параметры поиска
     */
    private function normalizeParams(array $params, array $locations): array {
        $defaultLocation = !empty($locations) ? $locations[0]['name'] : '';
        
        return [
            'pickupDate' => !empty($params['pickup_date']) ? $params['pickup_date'] : date('d.m.Y'),
            'returnDate' => !empty($params['return_date']) ? $params['return_date'] : date('d.m.Y', strtotime('+3 days')),
            'pickupArea' => !empty($params['pickup_area']) ? $params['pickup_area'] : $defaultLocation,
            'dropoffArea' => !empty($params['dropoff_area']) ? $params['dropoff_area'] : (!empty($params['pickup_area']) ? $params['pickup_area'] : $defaultLocation),
        ];
    }

    /**
     * Получает похожие автомобили
     */
    private function getSimilarCars(array $car): array {
        $allCars = array_filter($this->carRepo->getAll(), function($c) {
            return ($c['status'] ?? 'active') === 'active';
        });

        // Сначала ищем автомобили того же типа
        $similarCars = array_filter($allCars, function($c) use ($car) {
            return $c['id'] !== $car['id'] && $c['type'] === $car['type'];
        });

        $similarCars = array_slice($similarCars, 0, 4);

        // Если недостаточно, добавляем другие
        if (count($similarCars) < 4) {
            $additionalCars = array_filter($allCars, function($c) use ($car, $similarCars) {
                return $c['id'] !== $car['id'] && !in_array($c['id'], array_column($similarCars, 'id'));
            });
            
            $similarCars = array_merge($similarCars, array_slice($additionalCars, 0, 4 - count($similarCars)));
        }

        return $similarCars;
    }

    /**
     * Рендерит страницу деталей автомобиля
     */
    public function render(int $carId, array $params = []): string {
        $data = $this->getData($carId, $params);
        
        if (isset($data['error'])) {
            return $this->renderNotFound();
        }

        return $this->renderCarDetail($data);
    }

    private function renderNotFound(): string {
        ob_start();
        ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <div class="mb-8 flex justify-center">
                    <div class="bg-gray-100 p-8 rounded-full">
                        <?php echo get_icon('car', 'w-20 h-20 text-gray-300'); ?>
                    </div>
                </div>
                <h2 class="text-4xl font-black text-gray-800 mb-4 tracking-tighter">Машина не найдена</h2>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    К сожалению, мы не смогли найти выбранный автомобиль.
                </p>
                <?php echo \Components\component()->button([
                    'text' => 'Все автомобили',
                    'href' => '/',
                    'icon' => get_icon('car', 'w-4 h-4'),
                ]); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderCarDetail(array $data): string {
        $car = $data['car'];
        $params = $data['params'];
        $locations = $data['locations'];
        $similarCars = $data['similarCars'];

        ob_start();
        ?>
        <!-- Stepper -->
        <?php echo $this->renderStepper(2, (int)$car['id']); ?>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left: Car Details -->
                <div class="lg:col-span-2">
                    <?php echo $this->renderCarInfo($car); ?>
                    <?php echo $this->renderCarSpecs($car); ?>
                    <?php echo $this->renderIncludedFeatures(); ?>
                </div>

                <!-- Right: Booking Sidebar -->
                <div class="lg:col-span-1">
                    <div class="sticky top-8">
                        <?php echo $this->renderBookingSidebar($car, $params, $locations); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Cars -->
        <?php if (!empty($similarCars)): ?>
            <section class="py-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-2xl font-black text-gray-800 mb-6">Похожие автомобили</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php foreach($similarCars as $similar): ?>
                            <?php echo \Components\component()->carCard($similar); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php echo $this->renderScripts((int)$car['id']); ?>
        <?php
        return ob_get_clean();
    }

    private function renderStepper(int $currentStep, int $carId): string {
        // Simplified stepper implementation
        return '<div class="max-w-7xl mx-auto px-4 py-8"><!-- Stepper --></div>';
    }

    private function renderCarInfo(array $car): string {
        ob_start();
        ?>
        <div class="mb-8">
            <div class="bg-gray-50 rounded-3xl overflow-hidden mb-4 border border-gray-100/50">
                <div class="aspect-[4/3] flex items-center justify-center relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                    <img src="<?php echo asset_image_url($car['image']); ?>" 
                         alt="<?php echo htmlspecialchars($car['name']); ?>" 
                         class="w-full h-full object-contain p-8" 
                         onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
                </div>
            </div>
        </div>

        <div class="mb-12">
            <h1 class="text-3xl md:text-6xl font-black text-gray-800 mb-2 tracking-tighter">
                <?php echo htmlspecialchars($car['name']); ?>
            </h1>
            <div class="flex items-center gap-4 mb-10">
                <?php echo \Components\component()->badge([
                    'text' => $car['type'],
                    'variant' => 'blue',
                ]); ?>
                <span class="text-sm font-bold text-gray-500"><?php echo $car['year']; ?> год</span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderCarSpecs(array $car): string {
        return '<div class="mb-12"><h3 class="text-2xl font-black text-gray-800 mb-6">Характеристики</h3><!-- Specs grid --></div>';
    }

    private function renderIncludedFeatures(): string {
        return '<div class="mb-12"><h2 class="text-2xl font-black text-gray-800 mb-6">Включено в стоимость</h2><!-- Features --></div>';
    }

    private function renderBookingSidebar(array $car, array $params, array $locations): string {
        ob_start();
        ?>
        <div class="bg-white rounded-[32px] p-6 mb-6">
            <h3 class="text-xl font-black text-gray-800 mb-6">Период аренды</h3>
            
            <?php echo \Components\component()->priceSummary(
                $car, 
                $params['pickupDate'], 
                $params['returnDate'], 
                $params['pickupArea'], 
                $params['dropoffArea']
            ); ?>

            <?php echo \Components\component()->button([
                'text' => 'Далее',
                'href' => '/booking?id=' . $car['id'],
                'icon' => get_icon('arrow-right', 'w-4 h-4'),
                'iconPosition' => 'right',
                'size' => 'lg',
                'class' => 'w-full mt-6',
            ]); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderScripts(int $carId): string {
        return '<script>/* Car detail scripts */</script>';
    }
}
