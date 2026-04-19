<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for Discount/Promo Code Data
 */
class DiscountRepository {
    use PersistsTrait;

    private $dataPath;
    private $discounts = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_discounts.php';
        $this->load();
    }

    private function load() {
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->discounts = $discounts ?? [];
        }
    }

    public function getAll() {
        return $this->discounts;
    }

    public function save($data) {
        $exists = false;
        if (empty($data['id'])) {
           $data['id'] = (string)(!empty($this->discounts) ? max(array_column($this->discounts, 'id')) + 1 : 1);
        }
        foreach ($this->discounts as &$d) {
            if ($d['id'] == $data['id']) {
                $d = array_merge($d, $data);
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->discounts[] = $data;
        }
        return $this->persist();
    }

    public function delete($id) {
        $this->discounts = array_filter($this->discounts, function($c) use ($id) { return $c['id'] != $id; });
        return $this->persist();
    }

    private function persist() {
        $content = "<?php\n\$discounts = " . var_export($this->discounts, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
