<?php

require_once '../classes/order_class.php';

/**
 * Order Controller
 * Wraps order_class methods for use by action scripts
 */
function create_order_ctr(int $customerId, string $orderStatus = 'Pending'): array
{
    $order = new Order();
    return $order->createOrder($customerId, $orderStatus);
}

function add_order_details_ctr(int $orderId, array $items): array
{
    $order = new Order();
    return $order->addOrderDetails($orderId, $items);
}

function record_payment_ctr(int $orderId, int $customerId, float $amount, string $currency = 'USD'): array
{
    $order = new Order();
    return $order->recordPayment($orderId, $customerId, $amount, $currency);
}

function get_customer_orders_ctr(int $customerId): array
{
    $order = new Order();
    return $order->getCustomerOrders($customerId);
}

function get_order_by_id_ctr(int $orderId, int $customerId): ?array
{
    $order = new Order();
    return $order->getOrderById($orderId, $customerId);
}

function update_order_status_ctr(int $orderId, string $status): array
{
    $order = new Order();
    return $order->updateOrderStatus($orderId, $status);
}

?>

