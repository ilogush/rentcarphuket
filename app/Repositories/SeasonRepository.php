<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Season Multipliers
 */
class SeasonRepository {
    use PersistsTrait;

    private $dataPath;
    private $seasons = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_seasons.php';
        $this->load();
    }

    private function load() {
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->seasons = $seasons ?? [];
        }
    }

    public function getAll() {
        return $this->seasons;
    }

    public function save($data) {
        $exists = false;
        if (empty($data['id'])) {
           $data['id'] = (string)(!empty($this->seasons) ? max(array_column($this->seasons, 'id')) + 1 : 1);
        }
        foreach ($this->seasons as &$d) {
            if ($d['id'] == $data['id']) {
                $d = array_merge($d, $data);
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->seasons[] = $data;
        }
        return $this->persist();
    }

    public function delete($id) {
        $this->seasons = array_filter($this->seasons, function($c) use ($id) { return $c['id'] != $id; });
        return $this->persist();
    }

    private function persist() {
        $content = "<?php\n\$seasons = " . var_export($this->seasons, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
