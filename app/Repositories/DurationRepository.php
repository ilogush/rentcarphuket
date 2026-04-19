<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Rental Duration Multipliers
 */
class DurationRepository {
    use PersistsTrait;

    private $dataPath;
    private $durations = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_durations.php';
        $this->load();
    }

    private function load() {
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->durations = $durations ?? [];
        }
    }

    public function getAll() {
        return $this->durations;
    }

    public function save($data) {
        $exists = false;
        if (empty($data['id'])) {
           $data['id'] = (string)(!empty($this->durations) ? max(array_column($this->durations, 'id')) + 1 : 1);
        }
        foreach ($this->durations as &$d) {
            if ($d['id'] == $data['id']) {
                $d = array_merge($d, $data);
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->durations[] = $data;
        }
        return $this->persist();
    }

    public function delete($id) {
        $this->durations = array_filter($this->durations, function($c) use ($id) { return $c['id'] != $id; });
        return $this->persist();
    }

    private function persist() {
        $content = "<?php\n\$durations = " . var_export($this->durations, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
