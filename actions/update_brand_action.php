<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include '../controllers/brand_controller.php';

$brandId = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';

if ($brandId <= 0 || $name === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$result = update_brand_ctr((int)$_SESSION['user_id'], $brandId, $name);
echo json_encode($result);

?>

