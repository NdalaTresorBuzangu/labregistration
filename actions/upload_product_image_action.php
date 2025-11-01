<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../controllers/product_controller.php';
require_once '../helpers/upload_helper.php';

$hasMainImage = isset($_FILES['product_image']);
$hasGalleryBatch = isset($_FILES['product_images']);

if (!$hasMainImage && !$hasGalleryBatch) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$userId = (int)$_SESSION['user_id'];
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product = null;

if ($productId > 0) {
    $product = get_product_ctr($userId, $productId);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
}

if ($hasGalleryBatch) {
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Save the product before uploading gallery images']);
        exit;
    }

    $files = normalize_files_array($_FILES['product_images']);
    if (empty($files)) {
        echo json_encode(['success' => false, 'message' => 'No gallery files received']);
        exit;
    }

    $uploaded = [];

    foreach ($files as $file) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $store = uploads_store_image($file, $userId, ['p' . $productId, 'gallery']);
        if (!$store['success']) {
            cleanup_uploaded($uploaded, $userId);
            echo json_encode(['success' => false, 'message' => $store['message']]);
            exit;
        }

        $finalPath = 'uploads/' . $store['path'];
        $record = add_product_gallery_image_ctr($userId, $productId, $finalPath);

        if (!$record['success']) {
            uploads_delete($store['path']);
            cleanup_uploaded($uploaded, $userId);
            echo json_encode(['success' => false, 'message' => $record['message']]);
            exit;
        }

        $uploaded[] = [
            'path' => $finalPath,
            'relative' => $store['path'],
            'name' => $file['name'],
            'image_id' => $record['image_id'] ?? null,
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => count($uploaded) . ' image(s) uploaded successfully',
        'uploaded' => array_map(static function ($item) {
            return [
                'path' => $item['path'],
                'name' => $item['name'],
                'image_id' => $item['image_id'],
            ];
        }, $uploaded),
        'product_id' => $productId,
    ]);
    exit;
}

if ($hasMainImage) {
    $file = $_FILES['product_image'];

    if ($productId > 0) {
        $upload = uploads_store_image($file, $userId, ['p' . $productId], 'image_' . $productId . '_' . time());

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

    $upload = uploads_store_image($file, $userId, ['staged']);
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
    exit;
}

echo json_encode(['success' => false, 'message' => 'No supported files uploaded']);

function normalize_files_array(?array $fileInput): array
{
    if ($fileInput === null || !isset($fileInput['name']) || !is_array($fileInput['name'])) {
        return [];
    }

    $normalized = [];
    $count = count($fileInput['name']);

    for ($i = 0; $i < $count; $i++) {
        $normalized[] = [
            'name' => $fileInput['name'][$i],
            'type' => $fileInput['type'][$i],
            'tmp_name' => $fileInput['tmp_name'][$i],
            'error' => $fileInput['error'][$i],
            'size' => $fileInput['size'][$i],
        ];
    }

    return $normalized;
}

function cleanup_uploaded(array $uploaded, int $userId): void
{
    foreach ($uploaded as $item) {
        if (isset($item['relative'])) {
            uploads_delete($item['relative']);
        }
        if (!empty($item['image_id'])) {
            delete_product_gallery_image_ctr($userId, (int)$item['image_id']);
        }
    }
}

?>

