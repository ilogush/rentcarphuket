<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент статистики/метрики
 * Объединяет admin stats, car stats, dashboard metrics
 */
class Stat {
    public function render(array $props): string {
        $label = $props['label'] ?? '';
        $value = $props['value'] ?? '';
        $icon = $props['icon'] ?? null;
        $trend = $props['trend'] ?? null; // 'up', 'down', 'neutral'
        $trendValue = $props['trendValue'] ?? '';
        $color = $props['color'] ?? 'blue';
        $size = $props['size'] ?? 'md';
        $class = $props['class'] ?? '';

        $sizeClasses = [
            'sm' => ['container' => 'p-4', 'value' => 'text-2xl', 'label' => 'text-[9px]'],
            'md' => ['container' => 'p-6', 'value' => 'text-3xl', 'label' => 'text-[10px]'],
            'lg' => ['container' => 'p-8', 'value' => 'text-4xl', 'label' => 'text-xs'],
        ];

        $sizeConfig = $sizeClasses[$size] ?? $sizeClasses['md'];

        ob_start();
        ?>
        <div class="bg-gray-100 rounded-3xl border border-gray-200 <?php echo $sizeConfig['container']; ?> relative overflow-hidden group hover:border-<?php echo $color; ?>-200 transition-all <?php echo $class; ?>">
            <!-- Декоративный элемент -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-<?php echo $color; ?>-50 rounded-bl-[100%] transition-transform group-hover:scale-110"></div>
            
            <div class="relative">
                <?php if ($icon): ?>
                    <div class="text-<?php echo $color; ?>-600 mb-4">
                        <?php echo $icon; ?>
                    </div>
                <?php endif; ?>
                
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                    <?php echo htmlspecialchars($label); ?>
                </div>
                
                <div class="<?php echo $sizeConfig['value']; ?> font-black text-gray-800 tracking-tighter">
                    <?php echo htmlspecialchars($value); ?>
                </div>
                
                <?php if ($trend && $trendValue): ?>
                    <div class="mt-2 flex items-center gap-1 text-sm">
                        <?php if ($trend === 'up'): ?>
                            <span class="text-green-600"><?php echo get_icon('trending-up', 'w-4 h-4'); ?></span>
                            <span class="text-green-600 font-bold"><?php echo htmlspecialchars($trendValue); ?></span>
                        <?php elseif ($trend === 'down'): ?>
                            <span class="text-red-600"><?php echo get_icon('trending-down', 'w-4 h-4'); ?></span>
                            <span class="text-red-600 font-bold"><?php echo htmlspecialchars($trendValue); ?></span>
                        <?php else: ?>
                            <span class="text-gray-600 font-bold"><?php echo htmlspecialchars($trendValue); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
