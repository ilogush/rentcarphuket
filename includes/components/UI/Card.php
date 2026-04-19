<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент карточки
 */
class Card {
    public function render(array $props): string {
        $content = $props['content'] ?? '';
        $header = $props['header'] ?? '';
        $footer = $props['footer'] ?? '';
        $padding = $props['padding'] ?? 'p-6';
        $rounded = $props['rounded'] ?? 'rounded-3xl';
        $shadow = $props['shadow'] ?? 'shadow-sm';
        $extraClasses = $props['class'] ?? '';

        $classes = "border border-gray-200 overflow-hidden $rounded $shadow $extraClasses";

        ob_start();
        ?>
        <div class="<?php echo $classes; ?>">
            <?php if ($header): ?>
                <div class="border-b border-gray-100 px-6 py-4">
                    <?php echo $header; ?>
                </div>
            <?php endif; ?>
            
            <div class="<?php echo $padding; ?>">
                <?php echo $content; ?>
            </div>
            
            <?php if ($footer): ?>
                <div class="border-t border-gray-100 px-6 py-4">
                    <?php echo $footer; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
