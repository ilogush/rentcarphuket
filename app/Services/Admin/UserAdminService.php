<?php
declare(strict_types=1);
namespace App\Services\Admin;

use App\Repositories\UserRepository;

class UserAdminService {
    private UserRepository $repo;

    public function __construct(?UserRepository $repo = null) {
        $this->repo = $repo ?? new UserRepository();
    }

    public function getAll(): array {
        return $this->repo->getAll();
    }

    public function save(array $post): array {
        $userData = [
            'id' => $post['user_id'] ?? '',
            'name' => $post['name'],
            'phone' => $post['phone'],
            'email' => $post['email'],
            'registered_at' => $post['registered_at'] ?? date('Y-m-d'),
        ];
        $this->repo->save($userData);
        return ['success' => true, 'message' => 'Данные обновлены'];
    }

    public function delete($id): array {
        $this->repo->delete($id);
        return ['success' => true, 'message' => 'Пользователь удален'];
    }
}
