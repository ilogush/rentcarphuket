<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}
require_once __DIR__ . '/../auth.php';
use App\Repositories\DiscountRepository;

$discountRepo = new DiscountRepository();
$discounts = $discountRepo->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header("Location: /admin/discounts?error=csrf");
        exit;
    }

    if (isset($_POST['save_discount'])) {
        $discountRepo->save([
            'id' => $_POST['discount_id'] ?? '',
            'code' => $_POST['code'],
            'amount' => (int)$_POST['amount'],
            'type' => $_POST['type'] // 'percent' or 'fixed'
        ]);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Промокод сохранен!']);
            exit;
        }
        
        header("Location: /admin/discounts?success=saved");
        exit;
    }

    if (isset($_POST['delete_discount'])) {
        $discountRepo->delete($_POST['discount_id']);
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => true, 'message' => 'Промокод удален!']);
            exit;
        }
        
        header("Location: /admin/discounts?success=deleted");
        exit;
    }
}
