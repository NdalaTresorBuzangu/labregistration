<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

include '../controllers/product_controller.php';

$userId = (int)$_SESSION['user_id'];
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
} elseif (isset($_POST['search'])) {
    $search = trim($_POST['search']);
}

$products = list_products_ctr($userId);

if ($search !== '') {
    $products = array_filter($products, static function ($product) use ($search) {
        return stripos($product['product_title'], $search) !== false
            || stripos($product['brand_name'], $search) !== false
            || stripos($product['cat_name'], $search) !== false
            || stripos($product['product_keywords'], $search) !== false;
    });
}

$grouped = [];

foreach ($products as $product) {
    $catId = (int)$product['product_cat'];
    $brandId = (int)$product['product_brand'];

    if (!isset($grouped[$catId])) {
        $grouped[$catId] = [
            'category_id' => $catId,
            'category_name' => $product['cat_name'],
            'brands' => [],
        ];
    }

    if (!isset($grouped[$catId]['brands'][$brandId])) {
        $grouped[$catId]['brands'][$brandId] = [
            'brand_id' => $brandId,
            'brand_name' => $product['brand_name'],
            'products' => [],
        ];
    }

    $grouped[$catId]['brands'][$brandId]['products'][] = [
        'product_id' => (int)$product['product_id'],
        'title' => $product['product_title'],
        'price' => (float)$product['product_price'],
        'description' => $product['product_desc'],
        'keywords' => $product['product_keywords'],
        'image' => $product['product_image'],
        'category_id' => $catId,
        'brand_id' => $brandId,
    ];
}

// Convert nested associative arrays to indexed arrays for JSON cleanliness
$responseData = array_map(static function ($category) {
    $category['brands'] = array_values(array_map(static function ($brand) {
        $brand['products'] = array_values($brand['products']);
        return $brand;
    }, $category['brands']));
    return $category;
}, array_values($grouped));

echo json_encode([
    'success' => true,
    'data' => $responseData,
]);

?>

