<?php

require_once __DIR__ . '/../settings/db_class.php';

/**
 * Order Management Class
 * Handles order creation, order details, and payment recording
 */
class Order extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): int
    {
        // Generate invoice number: timestamp + random 4 digits
        $timestamp = time();
        $random = rand(1000, 9999);
        return (int)($timestamp . $random);
    }

    /**
     * Create a new order
     * @param int $customerId Customer ID
     * @param string $orderStatus Order status (default: 'Pending')
     * @return array ['success' => bool, 'order_id' => int, 'invoice_no' => int, 'message' => string]
     */
    public function createOrder(int $customerId, string $orderStatus = 'Pending'): array
    {
        if ($customerId <= 0) {
            return ['success' => false, 'message' => 'Invalid customer ID'];
        }

        $invoiceNo = $this->generateInvoiceNumber();
        $orderDate = date('Y-m-d');

        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iiss', $customerId, $invoiceNo, $orderDate, $orderStatus);

        if ($stmt->execute()) {
            $orderId = $this->db->insert_id;
            $stmt->close();
            return [
                'success' => true,
                'order_id' => $orderId,
                'invoice_no' => $invoiceNo,
                'message' => 'Order created successfully'
            ];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to create order: ' . $error];
    }

    /**
     * Add order details (products in the order)
     * @param int $orderId Order ID
     * @param int $productId Product ID
     * @param int $quantity Quantity
     * @return array ['success' => bool, 'message' => string]
     */
    public function addOrderDetail(int $orderId, int $productId, int $quantity): array
    {
        if ($orderId <= 0 || $productId <= 0 || $quantity <= 0) {
            return ['success' => false, 'message' => 'Invalid order detail parameters'];
        }

        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $orderId, $productId, $quantity);

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Order detail added'];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to add order detail: ' . $error];
    }

    /**
     * Add multiple order details at once
     * @param int $orderId Order ID
     * @param array $items Array of ['product_id' => int, 'qty' => int]
     * @return array ['success' => bool, 'added' => int, 'failed' => int, 'message' => string]
     */
    public function addOrderDetails(int $orderId, array $items): array
    {
        $added = 0;
        $failed = 0;

        foreach ($items as $item) {
            $productId = (int)($item['product_id'] ?? 0);
            $quantity = (int)($item['qty'] ?? 0);

            if ($productId > 0 && $quantity > 0) {
                $result = $this->addOrderDetail($orderId, $productId, $quantity);
                if ($result['success']) {
                    $added++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        return [
            'success' => $failed === 0,
            'added' => $added,
            'failed' => $failed,
            'message' => "Added {$added} items, {$failed} failed"
        ];
    }

    /**
     * Record payment for an order
     * @param int $orderId Order ID
     * @param int $customerId Customer ID
     * @param float $amount Payment amount
     * @param string $currency Currency (default: 'USD')
     * @return array ['success' => bool, 'payment_id' => int, 'message' => string]
     */
    public function recordPayment(int $orderId, int $customerId, float $amount, string $currency = 'USD'): array
    {
        if ($orderId <= 0 || $customerId <= 0 || $amount <= 0) {
            return ['success' => false, 'message' => 'Invalid payment parameters'];
        }

        $paymentDate = date('Y-m-d');

        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('diiss', $amount, $customerId, $orderId, $currency, $paymentDate);

        if ($stmt->execute()) {
            $paymentId = $this->db->insert_id;
            $stmt->close();
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'message' => 'Payment recorded successfully'
            ];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to record payment: ' . $error];
    }

    /**
     * Get all orders for a customer
     * @param int $customerId Customer ID
     * @return array Array of orders with details
     */
    public function getCustomerOrders(int $customerId): array
    {
        if ($customerId <= 0) {
            return [];
        }

        $sql = "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                       p.pay_id, p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON p.order_id = o.order_id
                WHERE o.customer_id = ?
                ORDER BY o.order_date DESC, o.order_id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $customerId);
        $orders = [];

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                // Get order details (products)
                $orderId = (int)$row['order_id'];
                $detailsSql = "SELECT od.product_id, od.qty, p.product_title, p.product_price
                              FROM orderdetails od
                              INNER JOIN products p ON p.product_id = od.product_id
                              WHERE od.order_id = ?";
                $detailsStmt = $this->db->prepare($detailsSql);
                $detailsStmt->bind_param('i', $orderId);
                $detailsStmt->execute();
                $detailsResult = $detailsStmt->get_result();
                $row['items'] = $detailsResult->fetch_all(MYSQLI_ASSOC);
                $detailsStmt->close();

                // Calculate order total
                $total = 0.0;
                foreach ($row['items'] as $item) {
                    $total += (float)$item['product_price'] * (int)$item['qty'];
                }
                $row['total'] = $total;

                $orders[] = $row;
            }
        }

        $stmt->close();
        return $orders;
    }

    /**
     * Get single order by ID
     * @param int $orderId Order ID
     * @param int $customerId Customer ID (for security - only return if customer owns order)
     * @return array|null Order data or null
     */
    public function getOrderById(int $orderId, int $customerId): ?array
    {
        if ($orderId <= 0 || $customerId <= 0) {
            return null;
        }

        $sql = "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                       p.pay_id, p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON p.order_id = o.order_id
                WHERE o.order_id = ? AND o.customer_id = ?
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $orderId, $customerId);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            $stmt->close();

            if ($order) {
                // Get order details
                $orderId = (int)$order['order_id'];
                $detailsSql = "SELECT od.product_id, od.qty, p.product_title, p.product_price, p.product_image
                              FROM orderdetails od
                              INNER JOIN products p ON p.product_id = od.product_id
                              WHERE od.order_id = ?";
                $detailsStmt = $this->db->prepare($detailsSql);
                $detailsStmt->bind_param('i', $orderId);
                $detailsStmt->execute();
                $detailsResult = $detailsStmt->get_result();
                $order['items'] = $detailsResult->fetch_all(MYSQLI_ASSOC);
                $detailsStmt->close();

                // Calculate total
                $total = 0.0;
                foreach ($order['items'] as $item) {
                    $total += (float)$item['product_price'] * (int)$item['qty'];
                }
                $order['total'] = $total;

                return $order;
            }
        }

        $stmt->close();
        return null;
    }

    /**
     * Update order status
     * @param int $orderId Order ID
     * @param string $status New status
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateOrderStatus(int $orderId, string $status): array
    {
        if ($orderId <= 0) {
            return ['success' => false, 'message' => 'Invalid order ID'];
        }

        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $status, $orderId);

        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected > 0) {
                return ['success' => true, 'message' => 'Order status updated'];
            }
            return ['success' => false, 'message' => 'Order not found'];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update order: ' . $error];
    }
}

?>

