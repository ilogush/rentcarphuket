<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}
require_once __DIR__ . '/../auth.php';
use App\Repositories\SeasonRepository;

$seasonRepo = new SeasonRepository();
$seasons = $seasonRepo->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /admin/seasons?error=csrf");
        exit;
    }

    if (isset($_POST['save_season'])) {
        $seasonRepo->save([
            'id' => $_POST['season_id'] ?? '',
            'season' => $_POST['season'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'multiplier' => $_POST['multiplier'],
            'label' => $_POST['label']
        ]);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Сезон сохранен!']);
            exit;
        }
        
        header("Location: /admin/seasons?success=saved");
        exit;
    }

    if (isset($_POST['delete_season'])) {
        $seasonRepo->delete($_POST['season_id']);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Сезон удален!']);
            exit;
        }
        
        header("Location: /admin/seasons?success=deleted");
        exit;
    }
}
