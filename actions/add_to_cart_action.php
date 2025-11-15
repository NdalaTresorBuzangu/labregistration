<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';

$response = ['success' => false, 'message' => ''];

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get POST data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validate input
if ($productId <= 0) {
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit;
}

if ($quantity <= 0) {
    $quantity = 1;
}

// Add to cart
$result = add_to_cart_ctr($productId, $quantity);

if ($result['success']) {
    // Get updated cart count
    $cartCount = get_cart_item_count_ctr();
    $response = [
        'success' => true,
        'message' => $result['message'],
        'cart_count' => $cartCount
    ];
} else {
    $response['message'] = $result['message'];
}

echo json_encode($response);

?>

