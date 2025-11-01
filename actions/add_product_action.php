<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include '../controllers/product_controller.php';
include '../controllers/brand_controller.php';
include '../controllers/category_controller.php';
require_once '../helpers/upload_helper.php';

$userId = (int)$_SESSION['user_id'];

$payload = [
    'title' => isset($_POST['title']) ? trim($_POST['title']) : '',
    'price' => isset($_POST['price']) ? (float)$_POST['price'] : 0,
    'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0,
    'brand_id' => isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0,
    'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
    'keywords' => isset($_POST['keywords']) ? trim($_POST['keywords']) : '',
];

$stagedPath = isset($_POST['image_path']) ? trim($_POST['image_path']) : null;

if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $upload = uploads_store_image($_FILES['product_image'], $userId, ['staged']);
    if (!$upload['success']) {
        echo json_encode(['success' => false, 'message' => $upload['message']]);
        exit;
    }
    $stagedPath = $upload['path'];
}

if ($stagedPath === null || $stagedPath === '') {
    echo json_encode(['success' => false, 'message' => 'Product image is required']);
    exit;
}

$result = add_product_ctr($userId, $payload);

if (!$result['success']) {
    uploads_delete($stagedPath);
    echo json_encode($result);
    exit;
}

$productId = (int)$result['product_id'];
$relativeStaged = uploads_normalize_relative($stagedPath);

$move = uploads_move_within($relativeStaged, ['images', 'u' . $userId, 'p' . $productId], 'image_' . $productId . '_' . time());
if (!$move['success']) {
    delete_product_ctr($userId, $productId);
    uploads_delete($stagedPath);
    echo json_encode(['success' => false, 'message' => $move['message']]);
    exit;
}

$finalPath = 'uploads/' . $move['path'];
$imageUpdate = update_product_image_ctr($userId, $productId, $finalPath);

if (!$imageUpdate['success']) {
    uploads_delete($move['path']);
    delete_product_ctr($userId, $productId);
    echo json_encode(['success' => false, 'message' => $imageUpdate['message']]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Product added successfully',
    'product_id' => $productId,
    'image' => $finalPath,
]);

?>

