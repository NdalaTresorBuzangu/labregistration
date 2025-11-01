<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include '../controllers/product_controller.php';
require_once '../helpers/upload_helper.php';

$userId = (int)$_SESSION['user_id'];
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product identifier']);
    exit;
}

$product = get_product_ctr($userId, $productId);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$payload = [
    'title' => isset($_POST['title']) ? trim($_POST['title']) : '',
    'price' => isset($_POST['price']) ? (float)$_POST['price'] : 0,
    'category_id' => isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0,
    'brand_id' => isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0,
    'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
    'keywords' => isset($_POST['keywords']) ? trim($_POST['keywords']) : '',
];

$stagedPath = null;
$incomingPath = isset($_POST['image_path']) ? trim($_POST['image_path']) : '';

if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $upload = uploads_store_image($_FILES['product_image'], $userId, ['staged']);
    if (!$upload['success']) {
        echo json_encode(['success' => false, 'message' => $upload['message']]);
        exit;
    }
    $stagedPath = $upload['path'];
} elseif ($incomingPath !== '' && strpos(uploads_normalize_relative($incomingPath), 'staged/') !== false) {
    $stagedPath = uploads_normalize_relative($incomingPath);
}

$newImagePath = null;
$movedImageRelative = null;

if ($stagedPath !== null) {
    $relative = uploads_normalize_relative($stagedPath);
    $move = uploads_move_within($relative, ['u' . $userId, 'p' . $productId], 'image_' . $productId . '_' . time());
    if (!$move['success']) {
        uploads_delete($stagedPath);
        echo json_encode(['success' => false, 'message' => $move['message']]);
        exit;
    }

    $movedImageRelative = $move['path'];
    $newImagePath = 'uploads/' . $move['path'];
}

$result = update_product_ctr($userId, $productId, $payload, $newImagePath);

if (!$result['success']) {
    if ($movedImageRelative !== null) {
        uploads_delete($movedImageRelative);
    }
    echo json_encode($result);
    exit;
}

if ($newImagePath !== null && !empty($product['product_image']) && $product['product_image'] !== $newImagePath) {
    uploads_delete($product['product_image']);
}

echo json_encode([
    'success' => true,
    'message' => $result['message'],
    'image' => $newImagePath ?? $product['product_image'],
]);

?>

