<?php
header('Content-Type: application/json');
session_start();

require_once '../controllers/cart_controller.php';

$response = ['success' => false, 'message' => ''];

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get POST data
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Validate input
if ($productId <= 0) {
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit;
}

// Remove from cart
$result = remove_from_cart_ctr($productId);

if ($result['success']) {
    // Get updated cart count and total
    $cartCount = get_cart_item_count_ctr();
    $cartTotal = get_cart_total_ctr();
    $response = [
        'success' => true,
        'message' => $result['message'],
        'cart_count' => $cartCount,
        'cart_total' => $cartTotal
    ];
} else {
    $response['message'] = $result['message'];
}

echo json_encode($response);

?>

