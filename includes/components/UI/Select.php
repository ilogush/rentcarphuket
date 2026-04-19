<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент выпадающего списка
 */
class Select {
    public function render(array $props): string {
        $name = $props['name'] ?? '';
        $label = $props['label'] ?? '';
        $options = $props['options'] ?? [];
        $selected = $props['selected'] ?? '';
        $required = $props['required'] ?? false;
        $multiple = $props['multiple'] ?? false;
        $grouped = $props['grouped'] ?? false;
        $placeholder = $props['placeholder'] ?? 'Выберите...';
        $class = $props['class'] ?? '';
        $attrs = $props['attrs'] ?? '';

        $requiredAttr = $required ? ' required' : '';
        $multipleAttr = $multiple ? ' multiple' : '';

        ob_start();
        ?>
        <div class="space-y-2">
            <?php if ($label): ?>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                    <?php echo htmlspecialchars($label); ?>
                </label>
            <?php endif; ?>
            
            <select 
                name="<?php echo htmlspecialchars($name); ?><?php echo $multiple ? '[]' : ''; ?>" 
                class="w-full bg-gray-100 border border-gray-200 rounded-2xl px-6 py-4 font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all appearance-none cursor-pointer <?php echo $class; ?>"
                <?php echo $requiredAttr . $multipleAttr; ?>
                <?php echo $attrs; ?>>
                
                <?php if (!$multiple && $placeholder): ?>
                    <option value="" disabled <?php echo empty($selected) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($placeholder); ?>
                    </option>
                <?php endif; ?>
                
                <?php if ($grouped): ?>
                    <?php echo $this->renderGroupedOptions($options, $selected, $multiple); ?>
                <?php else: ?>
                    <?php echo $this->renderOptions($options, $selected, $multiple); ?>
                <?php endif; ?>
            </select>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderOptions(array $options, $selected, bool $multiple): string {
        $html = '';
        foreach ($options as $value => $label) {
            $isSelected = $multiple 
                ? in_array($value, (array)$selected) 
                : (string)$value === (string)$selected;
            $selectedAttr = $isSelected ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars((string)$value) . '"' . $selectedAttr . '>' . 
                     htmlspecialchars($label) . '</option>';
        }
        return $html;
    }

    private function renderGroupedOptions(array $groups, $selected, bool $multiple): string {
        $html = '';
        foreach ($groups as $groupLabel => $options) {
            $html .= '<optgroup label="' . htmlspecialchars($groupLabel) . '">';
            $html .= $this->renderOptions($options, $selected, $multiple);
            $html .= '</optgroup>';
        }
        return $html;
    }
}
