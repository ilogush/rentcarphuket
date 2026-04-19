<?php
declare(strict_types=1);

namespace Components\Search;

/**
 * Компонент поиска автомобилей
 */
class SearchBox {
    public function render(array $locations = []): string {
        ob_start();
        ?>
        <div class="bg-gray-100 p-4 rounded-[32px] flex flex-col md:flex-row gap-2 items-stretch text-left mx-auto border border-gray-200 relative z-40 text-gray-900">
            <!-- Location -->
            <?php echo $this->renderLocationSelect($locations); ?>
            
            <!-- Dates -->
            <?php echo $this->renderDatePicker(); ?>

            <!-- Search Button -->
            <button type="button" onclick="handleSearchSubmit()" class="bg-orange-500 hover:bg-orange-600 text-white p-4 rounded-3xl transition-all shadow-orange-500/40 active:scale-95 flex items-center justify-center min-w-[84px] group">
                <div class="group-hover:scale-110 transition-transform">
                    <?php echo get_icon('search', 'w-6 h-6'); ?>
                </div>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderLocationSelect(array $locations): string {
        ob_start();
        ?>
        <div class="flex-1 relative group" id="location-select-container">
            <div class="bg-white p-4 rounded-3xl flex items-center gap-4 border border-gray-200 hover:border-blue-600/30 transition-all cursor-pointer h-full shadow-sm" onclick="toggleLocationDropdown(event)">
                <div class="text-blue-700 group-hover:scale-110 transition-transform">
                    <?php echo get_icon('map-pin', 'w-6 h-6'); ?>
                </div>
                <div class="flex-1">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Место подачи</div>
                    <div class="text-[15px] font-black text-gray-900 truncate leading-tight" id="selected-location">Выберите место</div>
                    <input type="hidden" name="pickup_location" id="pickup_location_input" value="">
                </div>
            </div>
            
            <!-- Dropdown -->
            <div id="location-dropdown" class="hidden absolute top-full left-0 right-0 mt-4 bg-white text-gray-900 rounded-[32px] border border-gray-200 z-[100] shadow-2xl">
                <div class="mc-location-dropdown-head">
                    <div class="mc-location-dropdown-title">Выберите район</div>
                    <div class="mc-location-dropdown-sub">Пхукет · доступные точки подачи</div>
                </div>
                <div class="mc-location-dropdown-list">
                <?php foreach($locations as $loc): 
                    $locName = is_array($loc) ? $loc['name'] : $loc;
                ?>
                    <div class="mc-dropdown-item px-8 py-3.5 hover:bg-blue-600 hover:text-white cursor-pointer font-bold text-gray-900 transition-all text-sm leading-tight" onclick="selectLocation('<?php echo htmlspecialchars($locName, ENT_QUOTES); ?>')">
                        <?php echo htmlspecialchars($locName); ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderDatePicker(): string {
        ob_start();
        ?>
        <div class="flex-1 bg-white p-4 rounded-3xl flex items-center gap-4 border border-gray-200 hover:border-blue-600/30 transition-all group cursor-pointer shadow-sm">
            <div class="text-blue-700 group-hover:scale-110 transition-transform">
                <?php echo get_icon('calendar', 'w-6 h-6'); ?>
            </div>
            <div class="flex-1">
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Даты аренды</div>
                <div class="flex items-center gap-3">
                    <input type="text" id="pickup_date" value="<?php echo date('d.m.Y'); ?>" class="bg-transparent outline-none w-full text-[15px] font-black text-gray-900 cursor-pointer placeholder-gray-400 leading-tight">
                    <span class="text-gray-400 font-black">/</span>
                    <input type="text" id="return_date" value="<?php echo date('d.m.Y', strtotime('+3 days')); ?>" class="bg-transparent outline-none w-full text-[15px] font-black text-gray-900 cursor-pointer placeholder-gray-400 leading-tight">
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}
