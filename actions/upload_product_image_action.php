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

if (!isset($_FILES['product_image'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId > 0) {
    $product = get_product_ctr($userId, $productId);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    $upload = uploads_store_image(
        $_FILES['product_image'],
        $userId,
        ['p' . $productId],
        'image_' . $productId . '_' . time()
    );

    if (!$upload['success']) {
        echo json_encode(['success' => false, 'message' => $upload['message']]);
        exit;
    }

    $finalPath = 'uploads/' . $upload['path'];
    $update = update_product_image_ctr($userId, $productId, $finalPath);
    if (!$update['success']) {
        uploads_delete($upload['path']);
        echo json_encode(['success' => false, 'message' => $update['message']]);
        exit;
    }

    if (!empty($product['product_image']) && $product['product_image'] !== $finalPath) {
        uploads_delete($product['product_image']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'path' => $finalPath,
        'is_temp' => false,
        'product_id' => $productId,
    ]);
    exit;
}

$upload = uploads_store_image($_FILES['product_image'], $userId, ['staged']);
if (!$upload['success']) {
    echo json_encode(['success' => false, 'message' => $upload['message']]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Image staged successfully',
    'path' => 'uploads/' . $upload['path'],
    'is_temp' => true,
]);

?>

