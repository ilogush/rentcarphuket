<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент кнопки
 * Объединяет все типы кнопок: client, admin, submit, link
 */
class Button {
    private array $themes = [
        'client' => [
            'base' => 'px-10 py-5 text-xs shadow-2xl',
            'primary' => 'bg-blue-600 text-white hover:bg-blue-700 shadow-blue-600/30',
            'secondary' => 'bg-white text-gray-800 border border-gray-100 hover:bg-gray-50',
        ],
        'admin' => [
            'base' => 'px-6 py-3 text-[10px]',
            'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
            'secondary' => 'bg-white text-blue-600 hover:bg-blue-50 border border-blue-100',
        ],
    ];

    private array $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
        'secondary' => 'bg-white text-gray-800 border border-gray-100 hover:bg-gray-50',
        'danger' => 'bg-red-500 text-white hover:bg-red-600',
        'success' => 'bg-green-500 text-white hover:bg-green-600',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-50',
        'orange' => 'bg-orange-500 text-white hover:bg-orange-600',
    ];

    private array $sizes = [
        'xs' => 'px-3 py-1.5 text-[10px]',
        'sm' => 'px-4 py-2 text-xs',
        'md' => 'px-6 py-3 text-sm',
        'lg' => 'px-10 py-5 text-base',
        'xl' => 'px-12 py-6 text-lg',
    ];

    public function render(array $props): string {
        $text = $props['text'] ?? '';
        $variant = $props['variant'] ?? 'primary';
        $size = $props['size'] ?? 'md';
        $theme = $props['theme'] ?? 'default';
        $type = $props['type'] ?? 'button';
        $icon = $props['icon'] ?? '';
        $iconPosition = $props['iconPosition'] ?? 'left';
        $loading = $props['loading'] ?? false;
        $fullWidth = $props['fullWidth'] ?? false;
        $extraClasses = $props['class'] ?? '';
        $attrs = $props['attrs'] ?? '';
        $href = $props['href'] ?? null;
        $disabled = $props['disabled'] ?? false || $loading;

        // Получаем классы темы или используем стандартные
        if ($theme !== 'default' && isset($this->themes[$theme])) {
            $themeConfig = $this->themes[$theme];
            $baseClasses = $themeConfig['base'];
            $variantClass = $themeConfig[$variant] ?? $this->variants[$variant];
        } else {
            $baseClasses = '';
            $variantClass = $this->variants[$variant] ?? $this->variants['primary'];
        }
        
        $sizeClass = $this->sizes[$size] ?? $this->sizes['md'];
        
        $classes = 'inline-flex items-center justify-center gap-3 rounded-2xl font-black transition-all active:scale-95 uppercase tracking-widest';
        $classes .= " $baseClasses $variantClass $sizeClass";
        
        if ($fullWidth) {
            $classes .= ' w-full';
        }
        
        if ($disabled) {
            $classes .= ' opacity-50 cursor-not-allowed pointer-events-none';
        }
        
        $classes .= " $extraClasses";

        // Контент кнопки
        $content = $this->renderContent($text, $icon, $iconPosition, $loading);

        // Рендерим ссылку или кнопку
        if ($href && !$disabled) {
            return "<a href='$href' class='$classes' $attrs>$content</a>";
        }

        $disabledAttr = $disabled ? ' disabled' : '';
        return "<button type='$type' class='$classes' $attrs$disabledAttr>$content</button>";
    }

    private function renderContent(string $text, string $icon, string $iconPosition, bool $loading): string {
        if ($loading) {
            return '<span class="relative z-10">' . htmlspecialchars($text) . '</span>' .
                   '<div class="animate-spin">' . get_icon('clock', 'w-4 h-4') . '</div>';
        }

        $iconHtml = $icon ? "<span class='shrink-0'>$icon</span>" : '';
        $textHtml = "<span>" . htmlspecialchars($text) . "</span>";
        
        return $iconPosition === 'left' 
            ? "$iconHtml$textHtml" 
            : "$textHtml$iconHtml";
    }
}
