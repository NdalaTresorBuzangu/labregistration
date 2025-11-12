<?php
header('Content-Type: application/json');
session_start();

require_once '../controllers/cart_controller.php';

$count = get_cart_item_count_ctr();

$response = [
    'success' => true,
    'count' => $count
];

echo json_encode($response);

?>

