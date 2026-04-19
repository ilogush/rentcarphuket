<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент поля ввода
 */
class Input {
    public function render(array $props): string {
        $name = $props['name'] ?? '';
        $type = $props['type'] ?? 'text';
        $value = $props['value'] ?? '';
        $placeholder = $props['placeholder'] ?? '';
        $label = $props['label'] ?? '';
        $required = $props['required'] ?? false;
        $icon = $props['icon'] ?? '';
        $prefix = $props['prefix'] ?? '';
        $suffix = $props['suffix'] ?? '';
        $extraClasses = $props['class'] ?? '';
        $attrs = $props['attrs'] ?? '';
        $error = $props['error'] ?? '';

        $requiredAttr = $required ? ' required' : '';
        $valueAttr = $value ? ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"' : '';
        $placeholderAttr = $placeholder ? ' placeholder="' . htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') . '"' : '';

        $baseInputClasses = 'w-full bg-gray-100 border border-gray-200 rounded-2xl px-6 py-4 font-bold text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all';
        
        if ($error) {
            $baseInputClasses .= ' border-red-500 bg-red-50';
        }

        // Adjust padding for icon, prefix, suffix
        $paddingLeft = $icon ? ' pl-14' : ($prefix ? ' pl-12' : '');
        $paddingRight = $suffix ? ' pr-20' : '';

        $inputClasses = "$baseInputClasses $extraClasses $paddingLeft $paddingRight";

        ob_start();
        ?>
        <div class="space-y-2">
            <?php if ($label): ?>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                    <?php echo htmlspecialchars($label); ?>
                </label>
            <?php endif; ?>
            
            <div class="relative">
                <?php if ($icon): ?>
                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400">
                        <?php echo $icon; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($prefix && !$icon): ?>
                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-600 font-bold text-sm">
                        <?php echo htmlspecialchars($prefix); ?>
                    </div>
                <?php endif; ?>
                
                <input 
                    type="<?php echo $type; ?>" 
                    name="<?php echo $name; ?>"
                    class="<?php echo $inputClasses; ?>"
                    <?php echo $valueAttr . $placeholderAttr . $requiredAttr; ?>
                    <?php echo $attrs; ?>
                >
                
                <?php if ($suffix): ?>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-600 font-bold text-sm">
                        <?php echo htmlspecialchars($suffix); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="absolute -bottom-5 left-4 text-[9px] font-black text-red-500 uppercase tracking-widest">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
