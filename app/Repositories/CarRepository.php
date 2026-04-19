<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Car Data (with caching)
 */
class CarRepository {
    use PersistsTrait;

    private string $dataPath;
    private array $cars = [];
    private static ?array $cachedCars = null;

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_cars.php';
        $this->load();
    }

    private function load(): void {
        // Use static cache to avoid multiple file reads per request
        if (self::$cachedCars !== null) {
            $this->cars = self::$cachedCars;
            return;
        }
        
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->cars = $cars ?? [];
            self::$cachedCars = $this->cars;
        }
    }

    public function getAll(): array {
        return $this->cars;
    }

    public function getById($id): ?array {
        // Use array_column for faster lookup
        $ids = array_column($this->cars, 'id');
        $index = array_search($id, $ids);
        return $index !== false ? $this->cars[$index] : null;
    }

    public function save(array $data): bool {
        $exists = false;
        foreach ($this->cars as &$car) {
            if ($car['id'] == $data['id']) {
                $car = array_merge($car, $data);
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            if (empty($data['id'])) {
                $maxId = !empty($this->cars) ? max(array_column($this->cars, 'id')) : 0;
                $data['id'] = (string)($maxId + 1);
            }
            $this->cars[] = $data;
        }
        
        $result = $this->persist();
        if ($result) {
            self::$cachedCars = $this->cars; // Update cache
        }
        return $result;
    }

    public function delete($id): bool {
        $this->cars = array_values(array_filter($this->cars, fn($c) => $c['id'] != $id));
        $result = $this->persist();
        if ($result) {
            self::$cachedCars = $this->cars; // Update cache
        }
        return $result;
    }

    private function persist(): bool {
        usort($this->cars, fn($a, $b) => (int)$a['id'] - (int)$b['id']);
        $content = "<?php\n\$cars = " . var_export($this->cars, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
