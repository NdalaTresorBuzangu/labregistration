<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../controllers/product_controller.php';

$response = [
    'success' => false,
    'message' => '',
    'order_id' => null,
    'invoice_no' => null
];

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please login to checkout';
    echo json_encode($response);
    exit;
}

$customerId = (int)$_SESSION['user_id'];

// Get cart items
$cartItems = get_user_cart_ctr();

if (empty($cartItems)) {
    $response['message'] = 'Your cart is empty';
    echo json_encode($response);
    exit;
}

// Start transaction-like process
try {
    // Step 1: Create order
    $orderResult = create_order_ctr($customerId, 'Pending');
    
    if (!$orderResult['success']) {
        $response['message'] = 'Failed to create order: ' . $orderResult['message'];
        echo json_encode($response);
        exit;
    }

    $orderId = $orderResult['order_id'];
    $invoiceNo = $orderResult['invoice_no'];

    // Step 2: Prepare order details
    $orderDetails = [];
    $totalAmount = 0.0;

    foreach ($cartItems as $item) {
        $productId = (int)$item['product_id'];
        $quantity = (int)$item['qty'];
        $price = (float)$item['product_price'];
        
        // Verify product still exists and get current price
        $product = view_single_product_public_ctr($productId);
        if (!$product) {
            $response['message'] = "Product ID {$productId} no longer available";
            echo json_encode($response);
            exit;
        }
        
        $currentPrice = (float)$product['product_price'];
        $totalAmount += $currentPrice * $quantity;
        
        $orderDetails[] = [
            'product_id' => $productId,
            'qty' => $quantity
        ];
    }

    // Step 3: Add order details
    $detailsResult = add_order_details_ctr($orderId, $orderDetails);
    
    if (!$detailsResult['success']) {
        $response['message'] = 'Failed to add order details: ' . $detailsResult['message'];
        echo json_encode($response);
        exit;
    }

    // Step 4: Record payment (simulated)
    $paymentResult = record_payment_ctr($orderId, $customerId, $totalAmount, 'USD');
    
    if (!$paymentResult['success']) {
        $response['message'] = 'Failed to record payment: ' . $paymentResult['message'];
        echo json_encode($response);
        exit;
    }

    // Step 5: Update order status to 'Paid'
    update_order_status_ctr($orderId, 'Paid');

    // Step 6: Empty cart
    $emptyResult = empty_cart_ctr();
    
    // Even if emptying cart fails, order is already created
    // But we'll note it in the response
    if (!$emptyResult['success']) {
        error_log("Warning: Failed to empty cart after checkout. Order ID: {$orderId}");
    }

    // Success response
    $response = [
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $orderId,
        'invoice_no' => $invoiceNo,
        'total_amount' => $totalAmount,
        'currency' => 'USD',
        'items_count' => count($orderDetails)
    ];

} catch (Exception $e) {
    $response['message'] = 'Checkout error: ' . $e->getMessage();
    error_log("Checkout error: " . $e->getMessage());
}

echo json_encode($response);

?>

