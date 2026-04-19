<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Booking Data
 */
class BookingRepository {
    use PersistsTrait;

    private string $dataPath;
    private array $bookings = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_bookings.php';
        $this->load();
    }

    private function load(): void {
        $this->bookings = $this->readBookingsFromFile();
    }

    public function getAll(): array {
        return $this->bookings;
    }

    public function getByUser($userId): array {
        return array_filter($this->bookings, function($b) use ($userId) {
            return $b['user_id'] == $userId;
        });
    }

    public function getById($id): ?array {
        foreach ($this->bookings as $b) {
            if ($b['id'] == $id) return $b;
        }
        return null;
    }

    public function save($data): ?array {
        return $this->withExclusiveLock(function () use ($data) {
            $this->bookings = $this->readBookingsFromFile();
            $data = $this->normalizeForSave((array)$data);
            $exists = false;

            foreach ($this->bookings as &$b) {
                if ($b['id'] == $data['id']) {
                    $b = array_merge($b, $data);
                    $exists = true;
                    break;
                }
            }

            unset($b);

            if (!$exists) {
                $this->bookings[] = $data;
            }

            return $this->persist() ? $data : null;
        });
    }

    public function createIfAvailable(array $data): array {
        return $this->withExclusiveLock(function () use ($data) {
            $this->bookings = $this->readBookingsFromFile();

            if (!$this->isAvailableIn($this->bookings, $data['car_id'] ?? '', $data['start_date'] ?? '', $data['end_date'] ?? '')) {
                return ['success' => false, 'error' => 'unavailable'];
            }

            $data = $this->normalizeForSave($data);
            $this->bookings[] = $data;

            if (!$this->persist()) {
                return ['success' => false, 'error' => 'persist'];
            }

            return ['success' => true, 'booking' => $data];
        }) ?? ['success' => false, 'error' => 'lock'];
    }

    public function delete($id): bool {
        return (bool)$this->withExclusiveLock(function () use ($id) {
            $this->bookings = $this->readBookingsFromFile();
            $this->bookings = array_values(array_filter($this->bookings, function($c) use ($id) { return $c['id'] != $id; }));
            return $this->persist();
        });
    }

    public function isAvailable($carId, $startDate, $endDate): bool {
        return $this->isAvailableIn($this->bookings, $carId, $startDate, $endDate);
    }

    private function isAvailableIn(array $bookings, $carId, $startDate, $endDate): bool {
        $start = \DateTime::createFromFormat('d.m.Y', $startDate);
        $end = \DateTime::createFromFormat('d.m.Y', $endDate);
        if (!$start || !$end) return false;

        foreach ($bookings as $b) {
            if ($b['car_id'] != $carId) continue;
            if (($b['status'] ?? 'pending') === 'cancelled') continue;

            $bStart = \DateTime::createFromFormat('d.m.Y', $b['start_date']);
            $bEnd = \DateTime::createFromFormat('d.m.Y', $b['end_date']);
            if (!$bStart || !$bEnd) continue;

            // Overlap check: (StartDate1 <= EndDate2) and (EndDate1 >= StartDate2)
            if ($start <= $bEnd && $end >= $bStart) {
                return false;
            }
        }
        return true;
    }

    private function readBookingsFromFile(): array {
        $bookings = [];
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
        }

        return is_array($bookings) ? $bookings : [];
    }

    private function normalizeForSave(array $data): array {
        if (empty($data['id'])) {
            $data['id'] = $this->nextId();
        }

        return $data;
    }

    private function nextId(): string {
        if (empty($this->bookings)) {
            return '1';
        }

        $ids = array_map('intval', array_column($this->bookings, 'id'));
        return (string)(max($ids) + 1);
    }

    private function withExclusiveLock(callable $callback) {
        $lockPath = $this->dataPath . '.lock';
        $handle = fopen($lockPath, 'c');

        if ($handle === false) {
            return null;
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                return null;
            }

            return $callback();
        } finally {
            if (is_resource($handle)) {
                @flock($handle, LOCK_UN);
                @fclose($handle);
            }
        }
    }

    private function persist(): bool {
        $content = "<?php\ndeclare(strict_types=1);\n\$bookings = " . var_export($this->bookings, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
