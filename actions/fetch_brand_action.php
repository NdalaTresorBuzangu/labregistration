<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include '../controllers/brand_controller.php';
include '../controllers/category_controller.php';

$userId = (int)$_SESSION['user_id'];

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
} elseif (isset($_POST['search'])) {
    $search = trim($_POST['search']);
}

$categories = fetch_category_ctr();
$brands = fetch_brands_grouped_ctr($userId);

if ($search !== '') {
    $brands = array_filter($brands, static function ($brand) use ($search) {
        return stripos($brand['brand_name'], $search) !== false
            || stripos($brand['cat_name'], $search) !== false;
    });
}

$grouped = [];

foreach ($categories as $category) {
    $grouped[$category['cat_id']] = [
        'category_id' => (int)$category['cat_id'],
        'category_name' => $category['cat_name'],
        'brands' => [],
    ];
}

foreach ($brands as $brand) {
    $categoryId = (int)$brand['category_id'];
    if (!isset($grouped[$categoryId])) {
        $grouped[$categoryId] = [
            'category_id' => $categoryId,
            'category_name' => $brand['cat_name'],
            'brands' => [],
        ];
    }

    $grouped[$categoryId]['brands'][] = [
        'brand_id' => (int)$brand['brand_id'],
        'brand_name' => $brand['brand_name'],
        'category_id' => $categoryId,
        'category_name' => $brand['cat_name'],
    ];
}

echo json_encode([
    'success' => true,
    'data' => array_values($grouped),
]);

?>

