<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент бейджа
 */
class Badge {
    private array $variants = [
        'gray' => 'bg-gray-50/80 text-gray-800 border-gray-100',
        'blue' => 'bg-blue-50 text-blue-600 border-blue-100',
        'green' => 'bg-green-50 text-green-600 border-green-100',
        'red' => 'bg-red-50 text-red-600 border-red-100',
        'orange' => 'bg-orange-50 text-orange-600 border-orange-100',
    ];

    public function render(array $props): string {
        $text = $props['text'] ?? '';
        $variant = $props['variant'] ?? 'gray';
        $icon = $props['icon'] ?? '';
        $extraClasses = $props['class'] ?? '';

        $variantClass = $this->variants[$variant] ?? $this->variants['gray'];
        $classes = "inline-flex items-center justify-center px-3 py-1 rounded-lg text-[10px] font-black border $variantClass $extraClasses";

        $iconHtml = $icon ? "<span class='mr-1'>$icon</span>" : '';

        return "<span class='$classes'>$iconHtml$text</span>";
    }
}
