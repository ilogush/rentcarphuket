<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

require_once __DIR__ . '/../auth.php';

use App\Services\Admin\CarAdminService;

$service = new CarAdminService();
$cars = $service->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /admin?error=csrf");
        exit;
    }

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (isset($_POST['save_car'])) {
        $result = $service->save($_POST, $_FILES);
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
        if (!empty($result['success'])) {
            header("Location: /admin?success=saved");
        } else {
            header("Location: /admin?error=upload");
        }
        exit;
    }

    if (isset($_POST['toggle_status'])) {
        header('Content-Type: application/json');
        echo json_encode($service->toggleStatus($_POST['car_id'] ?? '', $_POST['status'] ?? 'inactive'));
        exit;
    }

    if (isset($_POST['delete_car'])) {
        $result = $service->delete($_POST['car_id'] ?? '');
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
        header("Location: /admin?success=deleted");
        exit;
    }
}
