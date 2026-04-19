<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент модального окна
 */
class Modal {
    public function render(array $props): string {
        $id = $props['id'] ?? 'modal-' . uniqid();
        $title = $props['title'] ?? '';
        $content = $props['content'] ?? '';
        $footer = $props['footer'] ?? '';
        $size = $props['size'] ?? 'md';
        $closeButton = $props['closeButton'] ?? true;

        $sizeClasses = [
            'sm' => 'max-w-sm',
            'md' => 'max-w-xl',
            'lg' => 'max-w-4xl',
            'xl' => 'max-w-6xl',
        ];

        $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];

        ob_start();
        ?>
        <div id="<?php echo $id; ?>" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all overflow-y-auto">
            <div class="bg-gray-100 rounded-[40px] w-full <?php echo $sizeClass; ?> overflow-hidden animate-fade-in-down mb-auto mt-20 border border-gray-200">
                <?php if ($title || $closeButton): ?>
                    <div class="p-8 pb-4 flex justify-between items-center">
                        <?php if ($title): ?>
                            <h3 class="text-2xl font-black text-gray-800 tracking-tight leading-tight">
                                <?php echo $title; ?>
                            </h3>
                        <?php endif; ?>
                        
                        <?php if ($closeButton): ?>
                            <button onclick="closeModal('<?php echo $id; ?>')" class="text-gray-400 hover:text-gray-800 transition-colors p-1">
                                <?php echo get_icon('x', 'w-6 h-6'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <?php echo $content; ?>
                </div>
                
                <?php if ($footer): ?>
                    <div class="p-4 pt-4 border-t border-gray-200">
                        <?php echo $footer; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
        </script>
        <?php
        return ob_get_clean();
    }
}
