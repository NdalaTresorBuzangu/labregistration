<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';

$response = ['success' => false, 'cart' => [], 'total' => 0.00];

// Get cart items
$cartItems = get_user_cart_ctr();
$cartTotal = get_cart_total_ctr();

$response = [
    'success' => true,
    'cart' => $cartItems,
    'total' => $cartTotal,
    'count' => count($cartItems)
];

echo json_encode($response);

?>

