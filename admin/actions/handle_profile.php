<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

require_once __DIR__ . '/../auth.php';

use App\Services\Admin\ProfileAdminService;

$service = new ProfileAdminService();
$admin = $service->getProfileData($_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'CSRF error']);
            exit;
        }

        header('Location: /admin/profile?error=csrf');
        exit;
    }

    $result = $service->save($_POST);

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    if (!empty($result['success'])) {
        header('Location: /admin/profile?success=saved');
        exit;
    }

    header('Location: /admin/profile?error=save');
    exit;
}
