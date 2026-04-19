<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Repositories\CarRepository;
use App\Repositories\LocationRepository;
use App\Services\PricingService;

/**
 * Презентер для страницы бронирования
 */
class BookingPresenter {
    private CarRepository $carRepo;
    private LocationRepository $locationRepo;
    private PricingService $pricingService;

    public function __construct() {
        $this->carRepo = new CarRepository();
        $this->locationRepo = new LocationRepository();
        $this->pricingService = new PricingService();
    }

    /**
     * Получает данные для страницы бронирования
     */
    public function getData(int $carId, array $params = []): array {
        $car = $this->carRepo->getById($carId);
        
        if (!$car) {
            return ['error' => 'Car not found'];
        }

        $locations = $this->locationRepo->getAll();
        $normalizedParams = $this->normalizeParams($params, $locations);
        
        $priceInfo = $this->pricingService->calculate(
            $car,
            $normalizedParams['pickupDate'],
            $normalizedParams['returnDate'],
            $normalizedParams['pickupArea'],
            $normalizedParams['dropoffArea']
        );

        return [
            'car' => $car,
            'locations' => $locations,
            'params' => $normalizedParams,
            'priceInfo' => $priceInfo,
        ];
    }

    /**
     * Нормализует параметры бронирования
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
     * Рендерит страницу бронирования
     */
    public function render(int $carId, array $params = []): string {
        $data = $this->getData($carId, $params);
        
        if (isset($data['error'])) {
            return $this->renderNotFound();
        }

        return $this->renderBookingPage($data);
    }

    private function renderNotFound(): string {
        ob_start();
        ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h2 class="text-4xl font-black text-gray-800 mb-4">Машина не найдена</h2>
                <p class="text-gray-500 mb-8">Выберите другой автомобиль из каталога</p>
                <?php echo \Components\component()->button([
                    'text' => 'Все автомобили',
                    'href' => '/',
                ]); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderBookingPage(array $data): string {
        $car = $data['car'];
        $params = $data['params'];
        $priceInfo = $data['priceInfo'];

        ob_start();
        ?>
        <!-- Stepper -->
        <div class="max-w-7xl mx-auto px-4 py-8"><!-- Stepper step 3 --></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Booking Form -->
                <div class="lg:col-span-2">
                    <?php echo $this->renderBookingForm($car, $params, $priceInfo); ?>
                </div>

                <!-- Summary Sidebar -->
                <aside class="space-y-8">
                    <?php echo $this->renderSummarySidebar($car, $params, $priceInfo); ?>
                </aside>
            </div>
        </div>

        <?php echo $this->renderScripts(); ?>
        <?php
        return ob_get_clean();
    }

    private function renderBookingForm(array $car, array $params, array $priceInfo): string {
        ob_start();
        ?>
        <div class="bg-white rounded-[32px] p-6 shadow-2xl shadow-gray-200/40 border border-gray-100">
            <div class="mb-6">
                <div class="text-xs font-black text-blue-600 uppercase tracking-[0.3em] mb-2">Бронирование</div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tighter">Оформление заказа</h2>
                <p class="text-gray-400 font-medium mt-1">Заполните данные для подготовки автомобиля.</p>
            </div>

            <form action="/success" method="post" id="booking-form" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                <input type="hidden" name="pickup_date" value="<?php echo htmlspecialchars($params['pickupDate']); ?>">
                <input type="hidden" name="return_date" value="<?php echo htmlspecialchars($params['returnDate']); ?>">
                <input type="hidden" name="pickup_area" value="<?php echo htmlspecialchars($params['pickupArea']); ?>">
                <input type="hidden" name="dropoff_area" value="<?php echo htmlspecialchars($params['dropoffArea']); ?>">
                <input type="hidden" name="total_price" value="<?php echo $priceInfo['total']; ?>">
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-1 bg-blue-600 rounded-full"></div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Контактная информация</h3>
                    </div>
                    
                    <div class="grid gap-6 sm:grid-cols-2">
                        <?php echo \Components\component()->input([
                            'name' => 'client_name',
                            'label' => 'Ваше имя',
                            'placeholder' => 'Игорь Иванов',
                            'icon' => get_icon('user', 'w-5 h-5'),
                            'required' => true,
                        ]); ?>
                        
                        <?php echo \Components\component()->input([
                            'name' => 'client_phone',
                            'label' => 'Телефон',
                            'type' => 'tel',
                            'placeholder' => '+7 (___) ___-__-__',
                            'icon' => get_icon('phone', 'w-5 h-5'),
                            'required' => true,
                        ]); ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-4">Комментарий</label>
                    <textarea name="notes" rows="3" placeholder="Детское кресло, сим-карта или другие пожелания..." class="w-full bg-blue-50 border-2 border-transparent rounded-[32px] px-8 py-6 text-gray-800 font-medium outline-none focus:bg-white focus:border-blue-600/20 transition-all min-h-[120px] placeholder-gray-300"></textarea>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderSummarySidebar(array $car, array $params, array $priceInfo): string {
        ob_start();
        ?>
        <div class="bg-white rounded-[32px] p-6 shadow-2xl shadow-gray-200/40 border border-gray-100">
            <!-- Car Summary -->
            <div class="text-center mb-10">
                <div class="mb-6">
                    <img src="<?php echo asset_image_url($car['image']); ?>" 
                         alt="<?php echo htmlspecialchars($car['name']); ?>" 
                         class="w-full max-w-[280px] mx-auto h-auto object-contain"
                         onerror="this.onerror=null;this.src='<?php echo placeholder_image_url(); ?>'">
                </div>
                <div class="space-y-2">
                    <div class="text-xs font-black text-blue-600 uppercase tracking-[0.3em]">Ваш выбор</div>
                    <h2 class="text-4xl font-black text-gray-800 tracking-tighter">
                        <?php echo htmlspecialchars($car['name']); ?>
                    </h2>
                </div>
            </div>

            <!-- Trip Details -->
            <div class="space-y-6 mb-10 border-y-2 border-dashed border-gray-100">
                <?php echo $this->renderTripDetail('Получение', $params['pickupArea'], $params['pickupDate']); ?>
                <?php echo $this->renderTripDetail('Возврат', $params['dropoffArea'], $params['returnDate']); ?>
            </div>

            <!-- Price Summary -->
            <div class="mb-10 border-y-2 border-dashed border-gray-100">
                <?php echo \Components\component()->priceSummary(
                    $car,
                    $params['pickupDate'],
                    $params['returnDate'],
                    $params['pickupArea'],
                    $params['dropoffArea']
                ); ?>
            </div>

            <?php echo \Components\component()->button([
                'text' => 'Подтвердить заказ',
                'type' => 'submit',
                'icon' => get_icon('arrow-right', 'w-5 h-5'),
                'iconPosition' => 'right',
                'size' => 'lg',
                'class' => 'w-full',
                'attrs' => 'form="booking-form"',
            ]); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderTripDetail(string $label, string $location, string $date): string {
        ob_start();
        ?>
        <div class="flex items-start gap-5">
            <div class="bg-blue-50 p-3 rounded-2xl text-blue-600">
                <?php echo get_icon('map-pin', 'w-5 h-5'); ?>
            </div>
            <div>
                <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">
                    <?php echo htmlspecialchars($label); ?>
                </div>
                <div class="text-base font-black text-gray-800 leading-tight">
                    <?php echo htmlspecialchars($location); ?>
                </div>
                <div class="text-sm font-bold text-blue-600 mt-1">
                    <?php echo htmlspecialchars($date); ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderScripts(): string {
        return '<script>/* Booking validation scripts */</script>';
    }
}
