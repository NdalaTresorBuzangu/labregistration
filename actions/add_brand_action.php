<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include '../controllers/brand_controller.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;

if ($name === '' || $categoryId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Brand name and category are required']);
    exit;
}

$result = add_brand_ctr((int)$_SESSION['user_id'], $categoryId, $name);
echo json_encode($result);

?>

