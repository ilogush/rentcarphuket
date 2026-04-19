<?php
declare(strict_types=1);

namespace Components\UI;

use function Components\component;

/**
 * Универсальный компонент формы
 * Объединяет все типы форм: booking, admin, login, etc.
 */
class Form {
    public function render(array $props): string {
        $action = $props['action'] ?? '';
        $method = $props['method'] ?? 'POST';
        $fields = $props['fields'] ?? [];
        $submitButton = $props['submitButton'] ?? null;
        $theme = $props['theme'] ?? 'default';
        $id = $props['id'] ?? '';
        $class = $props['class'] ?? '';
        $attrs = $props['attrs'] ?? '';

        $formClass = $this->getThemeClass($theme) . ' ' . $class;
        $idAttr = $id ? " id='$id'" : '';

        ob_start();
        ?>
        <form action="<?php echo htmlspecialchars($action); ?>" 
              method="<?php echo $method; ?>"
              class="<?php echo $formClass; ?>"
              <?php echo $idAttr; ?>
              <?php echo $attrs; ?>>
            
            <?php if (isset($props['csrf'])): ?>
                <input type="hidden" name="csrf_token" value="<?php echo $props['csrf']; ?>">
            <?php endif; ?>
            
            <?php foreach ($fields as $field): ?>
                <?php echo $this->renderField($field); ?>
            <?php endforeach; ?>
            
            <?php if ($submitButton): ?>
                <div class="mt-6">
                    <?php echo component()->button(array_merge(
                        ['type' => 'submit'],
                        $submitButton
                    )); ?>
                </div>
            <?php endif; ?>
        </form>
        <?php
        return ob_get_clean();
    }

    private function renderField(array $field): string {
        $type = $field['type'] ?? 'text';
        
        switch ($type) {
            case 'textarea':
                return $this->renderTextarea($field);
            case 'select':
                return component()->select($field);
            case 'hidden':
                return $this->renderHidden($field);
            default:
                return component()->input($field);
        }
    }

    private function renderTextarea(array $field): string {
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $value = $field['value'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $rows = $field['rows'] ?? 3;
        $required = $field['required'] ?? false;

        $requiredAttr = $required ? ' required' : '';

        ob_start();
        ?>
        <div class="space-y-2">
            <?php if ($label): ?>
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                    <?php echo htmlspecialchars($label); ?>
                </label>
            <?php endif; ?>
            <textarea 
                name="<?php echo htmlspecialchars($name); ?>" 
                rows="<?php echo $rows; ?>"
                placeholder="<?php echo htmlspecialchars($placeholder); ?>"
                class="w-full bg-gray-100 border border-gray-200 rounded-2xl px-6 py-4 font-bold text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all"
                <?php echo $requiredAttr; ?>><?php echo htmlspecialchars($value); ?></textarea>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderHidden(array $field): string {
        $name = $field['name'] ?? '';
        $value = $field['value'] ?? '';
        return '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '">';
    }

    private function getThemeClass(string $theme): string {
        $themes = [
            'default' => 'space-y-6',
            'admin' => 'space-y-4',
            'booking' => 'space-y-6',
            'inline' => 'flex gap-4 items-end',
        ];

        return $themes[$theme] ?? $themes['default'];
    }
}
