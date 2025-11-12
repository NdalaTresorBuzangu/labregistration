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
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

// Validate input
if ($productId <= 0) {
    $response['message'] = 'Invalid product ID';
    echo json_encode($response);
    exit;
}

if ($quantity <= 0) {
    $response['message'] = 'Quantity must be greater than 0';
    echo json_encode($response);
    exit;
}

// Update quantity
$result = update_cart_item_ctr($productId, $quantity);

if ($result['success']) {
    // Get updated cart data
    $cartCount = get_cart_item_count_ctr();
    $cartTotal = get_cart_total_ctr();
    
    // Get updated item subtotal
    $cart = get_user_cart_ctr();
    $itemSubtotal = 0;
    foreach ($cart as $item) {
        if ((int)$item['product_id'] === $productId) {
            $itemSubtotal = (float)$item['product_price'] * (int)$item['qty'];
            break;
        }
    }
    
    $response = [
        'success' => true,
        'message' => $result['message'],
        'cart_count' => $cartCount,
        'cart_total' => $cartTotal,
        'item_subtotal' => $itemSubtotal
    ];
} else {
    $response['message'] = $result['message'];
}

echo json_encode($response);

?>

