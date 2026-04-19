<?php
declare(strict_types=1);
namespace App\Repositories;

/**
 * Repository for User Data
 */
class UserRepository {
    use PersistsTrait;

    private $dataPath;
    private $users = [];

    public function __construct() {
        $this->dataPath = __DIR__ . '/../../includes/data_users.php';
        $this->load();
    }

    private function load() {
        if (file_exists($this->dataPath)) {
            include $this->dataPath;
            $this->users = $users ?? [];
        }
    }

    public function getAll() {
        return $this->users;
    }

    public function findById($id) {
        foreach ($this->users as $u) {
            if ($u['id'] == $id) return $u;
        }
        return null;
    }

    public function findByEmail($email) {
        $email = strtolower(trim($email));
        foreach ($this->users as $u) {
            if (strtolower(trim($u['email'] ?? '')) === $email) return $u;
        }
        return null;
    }

    public function findByPhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        foreach ($this->users as $u) {
            $userPhone = preg_replace('/[^0-9]/', '', $u['phone'] ?? '');
            if ($userPhone === $phone) return $u;
        }
        return null;
    }

    public function save($data) {
        $exists = false;
        if (empty($data['id'])) {
           $data['id'] = (string)(!empty($this->users) ? max(array_column($this->users, 'id')) + 1 : 1);
        }
        foreach ($this->users as &$u) {
            if ($u['id'] == $data['id']) {
                $u = array_merge($u, $data);
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $this->users[] = $data;
        }
        return $this->persist();
    }

    public function delete($id) {
        $this->users = array_filter($this->users, function($u) use ($id) { return $u['id'] != $id; });
        return $this->persist();
    }

    private function persist() {
        $content = "<?php\n\$users = " . var_export($this->users, true) . ";\n";
        return $this->atomicWrite($this->dataPath, $content);
    }
}
