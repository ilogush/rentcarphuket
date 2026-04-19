<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}
require_once __DIR__ . '/../auth.php';
use App\Repositories\DurationRepository;

$durationRepo = new DurationRepository();
$durations = $durationRepo->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /admin/durations?error=csrf");
        exit;
    }

    if (isset($_POST['save_duration'])) {
        $durationRepo->save([
            'id' => $_POST['duration_id'] ?? '',
            'range' => $_POST['range'],
            'min_days' => $_POST['min_days'],
            'max_days' => $_POST['max_days'],
            'rate' => $_POST['rate'],
            'label' => $_POST['label']
        ]);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Период сохранен!']);
            exit;
        }
        
        header("Location: /admin/durations?success=saved");
        exit;
    }

    if (isset($_POST['delete_duration'])) {
        $durationRepo->delete($_POST['duration_id']);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Период удален!']);
            exit;
        }
        
        header("Location: /admin/durations?success=deleted");
        exit;
    }
}
