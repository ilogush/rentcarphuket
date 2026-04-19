<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

require_once __DIR__ . '/../auth.php';

use App\Services\Admin\UserAdminService;

$service = new UserAdminService();
$users = $service->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'error' => 'CSRF error']);
        exit;
    }

    if (isset($_POST['save_user'])) {
        echo json_encode($service->save($_POST));
        exit;
    }

    if (isset($_POST['delete_user'])) {
        echo json_encode($service->delete($_POST['user_id'] ?? ''));
        exit;
    }
}
