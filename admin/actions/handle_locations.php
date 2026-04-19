<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}
require_once __DIR__ . '/../auth.php';
use App\Repositories\LocationRepository;

$locationRepo = new LocationRepository();
$locations = $locationRepo->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /admin/locations?error=csrf");
        exit;
    }

    if (isset($_POST['save_location'])) {
        $locationRepo->save([
            'id' => $_POST['location_id'] ?? '',
            'name' => $_POST['name'],
            'delivery_price' => $_POST['delivery_price'] ?? 0
        ]);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Локация сохранена!']);
            exit;
        }

        header("Location: /admin/locations?success=saved");
        exit;
    }

    if (isset($_POST['delete_location'])) {
        $locationRepo->delete($_POST['location_id']);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Локация удалена!']);
            exit;
        }

        header("Location: /admin/locations?success=deleted");
        exit;
    }
}
