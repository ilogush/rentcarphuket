<?php
declare(strict_types=1);

namespace Components\Car;

/**
 * Компонент сетки автомобилей с сортировкой
 */
class CarGrid {
    private CarCard $carCard;

    public function __construct() {
        $this->carCard = new CarCard();
    }

    public function render(array $cars, array $options = []): string {
        $showSorter = $options['showSorter'] ?? true;
        $showLoadMore = $options['showLoadMore'] ?? true;
        $initialLimit = $options['initialLimit'] ?? 8;
        $title = $options['title'] ?? 'Наш автопарк';

        ob_start();
        ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="cars-section">
            <div class="flex flex-col md:flex-row md:items-start justify-between mb-8 gap-8">
                <div class="flex-1">
                    <div class="flex items-center justify-between flex-wrap gap-8">
                        <h2 class='text-2xl font-black text-gray-800 mb-6'><?php echo htmlspecialchars($title); ?></h2>
                        
                        <?php if ($showSorter): ?>
                            <div class="flex items-center bg-white p-1 rounded-3xl border border-gray-100 shadow-sm min-w-[240px]">
                                <select id="car-sorter" onchange="sortCars()" class="w-full bg-gray-50 border-none rounded-[20px] px-6 py-4 text-sm font-black text-gray-800 outline-none cursor-pointer hover:bg-blue-50 transition-all appearance-none pr-10 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2024%2024%22%20stroke%3D%22currentColor%22%3E%3Cpath%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%222.5%22%20d%3D%22M19%209l-7%207-7-7%22%2F%3E%3C/svg%3E')] bg-[length:1.1rem] bg-[right_1rem_center] bg-no-repeat">
                                    <option value="price" selected>По стоимости</option>
                                    <option value="year">По дате выпуска</option>
                                    <option value="engine">По мощности</option>
                                    <option value="discount">По скидке</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div id="cars-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach($cars as $i => $car): ?>
                    <div class="car-item <?php echo $i >= $initialLimit ? 'hidden' : ''; ?>" 
                         data-year="<?php echo $car['year']; ?>" 
                         data-price="<?php echo $car['price']; ?>" 
                         data-engine="<?php echo $car['engine']; ?>"
                         data-discount="<?php echo $car['discount'] ?? 0; ?>">
                        <?php echo $this->carCard->render($car); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($showLoadMore && count($cars) > $initialLimit): ?>
                <div id="show-all-container" class="py-8 text-center">
                    <button onclick="showAllCars()" class="group bg-white border border-gray-100 text-gray-800 px-8 py-4 rounded-2xl font-black hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-500 shadow-blue-500/5 active:scale-95 flex items-center gap-4 mx-auto uppercase tracking-widest text-xs">
                        Показать все машины
                        <div class="bg-gray-50 group-hover:bg-blue-500/50 p-2 rounded-full transition-colors">
                            <?php echo get_icon('chevron-down', 'w-4 h-4'); ?>
                        </div>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
