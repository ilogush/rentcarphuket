<?php
declare(strict_types=1);

namespace Components\UI;

/**
 * Универсальный компонент таблицы
 * Объединяет admin tables, data tables, etc.
 */
class Table {
    public function render(array $props): string {
        $headers = $props['headers'] ?? [];
        $rows = $props['rows'] ?? [];
        $theme = $props['theme'] ?? 'default';
        $sortable = $props['sortable'] ?? false;
        $hoverable = $props['hoverable'] ?? true;
        $striped = $props['striped'] ?? false;
        $class = $props['class'] ?? '';

        $tableClass = $this->getThemeClass($theme);
        if ($hoverable) $tableClass .= ' hover-rows';
        if ($striped) $tableClass .= ' striped';
        $tableClass .= ' ' . $class;

        ob_start();
        ?>
        <div class="overflow-x-auto bg-gray-100 rounded-3xl border border-gray-200 p-4">
            <table class="w-full text-left border-collapse <?php echo $tableClass; ?>">
                <thead>
                    <tr class="border-b border-gray-50">
                        <?php foreach ($headers as $header): ?>
                            <?php echo $this->renderHeader($header, $sortable); ?>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?php echo count($headers); ?>" class="p-10 text-center text-gray-400 font-bold">
                                Ничего не найдено
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php echo $rows; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    private function renderHeader($header, bool $sortable): string {
        $label = is_array($header) ? ($header['label'] ?? '') : $header;
        $sortKey = is_array($header) ? ($header['sortKey'] ?? '') : '';
        $class = is_array($header) ? ($header['class'] ?? '') : '';

        $headerClass = "px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] $class";

        if ($sortable && $sortKey) {
            return '<th class="' . $headerClass . ' cursor-pointer hover:text-gray-600" onclick="sortTable(\'' . $sortKey . '\')">' . 
                   htmlspecialchars($label) . 
                   ' <span class="sort-icon">↕</span></th>';
        }

        return '<th class="' . $headerClass . '">' . htmlspecialchars($label) . '</th>';
    }

    private function getThemeClass(string $theme): string {
        $themes = [
            'default' => '',
            'admin' => 'admin-table',
            'compact' => 'text-sm',
            'bordered' => 'border border-gray-200',
        ];

        return $themes[$theme] ?? '';
    }
}
