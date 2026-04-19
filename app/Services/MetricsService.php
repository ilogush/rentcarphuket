<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Сервис для сбора метрик производительности
 */
class MetricsService {
    private array $timers = [];
    private array $counters = [];
    private array $metrics = [];
    private float $requestStart;

    public function __construct() {
        $this->requestStart = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
    }

    /**
     * Начать таймер
     */
    public function startTimer(string $name): void {
        $this->timers[$name] = microtime(true);
    }

    /**
     * Остановить таймер и записать метрику
     */
    public function stopTimer(string $name): float {
        if (!isset($this->timers[$name])) {
            return 0.0;
        }

        $elapsed = microtime(true) - $this->timers[$name];
        $this->record($name . '.time', $elapsed);
        unset($this->timers[$name]);

        return $elapsed;
    }

    /**
     * Измерить время выполнения функции
     */
    public function measure(string $name, callable $callback) {
        $this->startTimer($name);
        $result = $callback();
        $this->stopTimer($name);
        return $result;
    }

    /**
     * Увеличить счетчик
     */
    public function increment(string $name, int $value = 1): void {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = 0;
        }
        $this->counters[$name] += $value;
    }

    /**
     * Записать метрику
     */
    public function record(string $name, $value): void {
        if (!isset($this->metrics[$name])) {
            $this->metrics[$name] = [];
        }
        $this->metrics[$name][] = $value;
    }

    /**
     * Получить все метрики
     */
    public function getMetrics(): array {
        return [
            'timers' => $this->timers,
            'counters' => $this->counters,
            'metrics' => $this->metrics,
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
            ],
            'request_time' => microtime(true) - $this->requestStart,
        ];
    }

    /**
     * Получить среднее значение метрики
     */
    public function average(string $name): float {
        if (!isset($this->metrics[$name]) || empty($this->metrics[$name])) {
            return 0.0;
        }

        return array_sum($this->metrics[$name]) / count($this->metrics[$name]);
    }

    /**
     * Сохранить метрики в лог
     */
    public function flush(): void {
        $logFile = __DIR__ . '/../../storage/logs/metrics.log';
        $metrics = $this->getMetrics();
        
        $entry = [
            'timestamp' => date('c'),
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'metrics' => $metrics,
        ];

        file_put_contents(
            $logFile,
            json_encode($entry) . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * Вывести метрики в HTML комментарий (для разработки)
     */
    public function renderDebug(): string {
        if (!defined('APP_DEBUG') || !APP_DEBUG) {
            return '';
        }

        $metrics = $this->getMetrics();
        
        ob_start();
        ?>
        <!-- Performance Metrics
        Request Time: <?php echo number_format($metrics['request_time'] * 1000, 2); ?>ms
        Memory Usage: <?php echo number_format($metrics['memory']['current'] / 1024 / 1024, 2); ?>MB
        Peak Memory: <?php echo number_format($metrics['memory']['peak'] / 1024 / 1024, 2); ?>MB
        
        Timers:
        <?php foreach ($metrics['counters'] as $name => $count): ?>
        - <?php echo $name; ?>: <?php echo $count; ?>
        <?php endforeach; ?>
        -->
        <?php
        return ob_get_clean();
    }
}
