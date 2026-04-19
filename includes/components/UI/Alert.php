<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент уведомлений/алертов
 * Объединяет toast, alerts, notifications
 */
class Alert {
    private array $types = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-800',
            'icon' => 'check-circle',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-800',
            'icon' => 'x-circle',
        ],
        'warning' => [
            'bg' => 'bg-orange-50',
            'border' => 'border-orange-200',
            'text' => 'text-orange-800',
            'icon' => 'alert-triangle',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-800',
            'icon' => 'info',
        ],
    ];

    public function render(array $props): string {
        $type = $props['type'] ?? 'info';
        $message = $props['message'] ?? '';
        $title = $props['title'] ?? '';
        $dismissible = $props['dismissible'] ?? true;
        $class = $props['class'] ?? '';

        $config = $this->types[$type] ?? $this->types['info'];
        
        ob_start();
        ?>
        <div class="<?php echo $config['bg']; ?> <?php echo $config['border']; ?> border rounded-2xl p-4 <?php echo $class; ?>" role="alert">
            <div class="flex items-start gap-3">
                <div class="<?php echo $config['text']; ?> shrink-0">
                    <?php echo get_icon($config['icon'], 'w-5 h-5'); ?>
                </div>
                
                <div class="flex-1">
                    <?php if ($title): ?>
                        <h4 class="font-black <?php echo $config['text']; ?> mb-1">
                            <?php echo htmlspecialchars($title); ?>
                        </h4>
                    <?php endif; ?>
                    
                    <p class="<?php echo $config['text']; ?> text-sm font-medium">
                        <?php echo htmlspecialchars($message); ?>
                    </p>
                </div>
                
                <?php if ($dismissible): ?>
                    <button type="button" class="<?php echo $config['text']; ?> hover:opacity-70 transition-opacity" onclick="this.parentElement.parentElement.remove()">
                        <?php echo get_icon('x', 'w-4 h-4'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Рендерит toast уведомление (для JS)
     */
    public function toast(array $props): string {
        $type = $props['type'] ?? 'success';
        $message = $props['message'] ?? '';
        $duration = $props['duration'] ?? 5000;

        $messageJson = json_encode($message, JSON_UNESCAPED_UNICODE);
        $typeJson = json_encode($type, JSON_UNESCAPED_UNICODE);

        return "<script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof showToast === 'function') {
                    showToast($messageJson, $typeJson);
                }
            });
        </script>";
    }
}
