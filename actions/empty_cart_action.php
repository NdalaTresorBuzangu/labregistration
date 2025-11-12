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

// Empty cart
$result = empty_cart_ctr();

if ($result['success']) {
    $response = [
        'success' => true,
        'message' => $result['message'],
        'items_removed' => $result['items_removed'] ?? 0,
        'cart_count' => 0,
        'cart_total' => 0.00
    ];
} else {
    $response['message'] = $result['message'];
}

echo json_encode($response);

?>

