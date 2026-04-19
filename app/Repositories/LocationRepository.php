<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Location Data
 */
class LocationRepository {
    use PersistsTrait;

    private $dataPath;
    private $locations = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_locations.php';
        $this->load();
    }

    private function load() {
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->locations = $locations ?? [];
        }
    }

    public function getAll() {
        return $this->locations;
    }

    public function getById($id) {
        foreach ($this->locations as $loc) {
            if ($loc['id'] == $id) return $loc;
        }
        return null;
    }

    public function save($data) {
        $exists = false;
        if (empty($data['id'])) {
           $data['id'] = (string)(!empty($this->locations) ? max(array_column($this->locations, 'id')) + 1 : 1);
        }
        foreach ($this->locations as &$loc) {
            if ($loc['id'] == $data['id']) {
                $loc = array_merge($loc, $data);
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->locations[] = $data;
        }
        return $this->persist();
    }

    public function delete($id) {
        $this->locations = array_filter($this->locations, function($c) use ($id) { return $c['id'] != $id; });
        return $this->persist();
    }

    private function persist() {
        usort($this->locations, function($a, $b) { return (int)$a['id'] - (int)$b['id']; });
        $content = "<?php\n\$locations = " . var_export($this->locations, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
