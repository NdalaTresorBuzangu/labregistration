<?php

require_once '../settings/db_class.php';

/**
 * Cart Management Class
 * Handles cart operations for both logged-in users and guests
 * 
 * Decision: Hybrid approach
 * - Logged-in users: Cart tied to customer_id
 * - Guest users: Cart tied to IP address
 * This allows guests to add items before registering, and cart persists after login
 */
class Cart extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    /**
     * Get user identifier (customer_id if logged in, IP if guest)
     */
    private function getUserIdentifier(): array
    {
        $customerId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        return [
            'customer_id' => $customerId,
            'ip_address' => $ipAddress
        ];
    }

    /**
     * Check if product already exists in cart
     * Returns cart row if exists, false otherwise
     */
    public function productExistsInCart(int $productId, ?int $customerId = null, string $ipAddress = ''): ?array
    {
        if ($customerId !== null) {
            // Logged-in user: check by customer_id
            $sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ii', $productId, $customerId);
        } else {
            // Guest user: check by IP address
            $sql = "SELECT * FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('is', $productId, $ipAddress);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row ?: null;
        }

        $stmt->close();
        return null;
    }

    /**
     * Add product to cart
     * If product exists, increment quantity instead of duplicating
     */
    public function addToCart(int $productId, int $quantity = 1): array
    {
        if ($productId <= 0 || $quantity <= 0) {
            return ['success' => false, 'message' => 'Invalid product or quantity'];
        }

        $user = $this->getUserIdentifier();
        $customerId = $user['customer_id'];
        $ipAddress = $user['ip_address'];

        // Check if product already exists in cart
        $existing = $this->productExistsInCart($productId, $customerId, $ipAddress);

        if ($existing) {
            // Update quantity (increment)
            $newQuantity = (int)$existing['qty'] + $quantity;
            return $this->updateQuantity($productId, $newQuantity, $customerId, $ipAddress);
        }

        // Insert new cart item
        if ($customerId !== null) {
            // Logged-in user
            $sql = "INSERT INTO cart (p_id, c_id, ip_add, qty) VALUES (?, ?, '', ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iii', $productId, $customerId, $quantity);
        } else {
            // Guest user
            $sql = "INSERT INTO cart (p_id, c_id, ip_add, qty) VALUES (?, NULL, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('isi', $productId, $ipAddress, $quantity);
        }

        if ($stmt->execute()) {
            $stmt->close();
            return ['success' => true, 'message' => 'Product added to cart'];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to add to cart: ' . $error];
    }

    /**
     * Update quantity of a product in cart
     */
    public function updateQuantity(int $productId, int $newQuantity, ?int $customerId = null, string $ipAddress = ''): array
    {
        if ($productId <= 0 || $newQuantity <= 0) {
            return ['success' => false, 'message' => 'Invalid product or quantity'];
        }

        if ($customerId === null && $ipAddress === '') {
            $user = $this->getUserIdentifier();
            $customerId = $user['customer_id'];
            $ipAddress = $user['ip_address'];
        }

        if ($customerId !== null) {
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iii', $newQuantity, $productId, $customerId);
        } else {
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('iis', $newQuantity, $productId, $ipAddress);
        }

        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected > 0) {
                return ['success' => true, 'message' => 'Cart updated'];
            }
            return ['success' => false, 'message' => 'Cart item not found'];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to update cart: ' . $error];
    }

    /**
     * Remove product from cart
     */
    public function removeFromCart(int $productId): array
    {
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }

        $user = $this->getUserIdentifier();
        $customerId = $user['customer_id'];
        $ipAddress = $user['ip_address'];

        if ($customerId !== null) {
            $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ii', $productId, $customerId);
        } else {
            $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('is', $productId, $ipAddress);
        }

        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            
            if ($affected > 0) {
                return ['success' => true, 'message' => 'Product removed from cart'];
            }
            return ['success' => false, 'message' => 'Cart item not found'];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to remove from cart: ' . $error];
    }

    /**
     * Get all cart items for current user
     * Returns cart items with product details
     */
    public function getUserCart(): array
    {
        $user = $this->getUserIdentifier();
        $customerId = $user['customer_id'];
        $ipAddress = $user['ip_address'];

        if ($customerId !== null) {
            $sql = "SELECT c.p_id, c.qty, p.product_id, p.product_title, p.product_price, p.product_image, 
                           p.product_desc, cat.cat_name, b.brand_name
                    FROM cart c
                    INNER JOIN products p ON p.product_id = c.p_id
                    LEFT JOIN categories cat ON cat.cat_id = p.product_cat
                    LEFT JOIN brands b ON b.brand_id = p.product_brand
                    WHERE c.c_id = ?
                    ORDER BY c.p_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $customerId);
        } else {
            $sql = "SELECT c.p_id, c.qty, p.product_id, p.product_title, p.product_price, p.product_image, 
                           p.product_desc, cat.cat_name, b.brand_name
                    FROM cart c
                    INNER JOIN products p ON p.product_id = c.p_id
                    LEFT JOIN categories cat ON cat.cat_id = p.product_cat
                    LEFT JOIN brands b ON b.brand_id = p.product_brand
                    WHERE c.ip_add = ? AND c.c_id IS NULL
                    ORDER BY c.p_id ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('s', $ipAddress);
        }

        $items = [];
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $row['subtotal'] = (float)$row['product_price'] * (int)$row['qty'];
                $items[] = $row;
            }
        }

        $stmt->close();
        return $items;
    }

    /**
     * Get cart total
     */
    public function getCartTotal(): float
    {
        $items = $this->getUserCart();
        $total = 0.0;

        foreach ($items as $item) {
            $total += (float)$item['product_price'] * (int)$item['qty'];
        }

        return $total;
    }

    /**
     * Get cart item count
     */
    public function getCartItemCount(): int
    {
        $items = $this->getUserCart();
        return count($items);
    }

    /**
     * Empty the entire cart
     */
    public function emptyCart(): array
    {
        $user = $this->getUserIdentifier();
        $customerId = $user['customer_id'];
        $ipAddress = $user['ip_address'];

        if ($customerId !== null) {
            $sql = "DELETE FROM cart WHERE c_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('i', $customerId);
        } else {
            $sql = "DELETE FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('s', $ipAddress);
        }

        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ['success' => true, 'message' => 'Cart emptied', 'items_removed' => $affected];
        }

        $error = $stmt->error;
        $stmt->close();
        return ['success' => false, 'message' => 'Failed to empty cart: ' . $error];
    }

    /**
     * Transfer guest cart to logged-in user
     * Called when a guest user logs in
     */
    public function transferGuestCartToUser(int $customerId, string $ipAddress): bool
    {
        // Get guest cart items
        $sql = "SELECT p_id, qty FROM cart WHERE ip_add = ? AND c_id IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $ipAddress);
        $stmt->execute();
        $result = $stmt->get_result();
        $guestItems = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (empty($guestItems)) {
            return true; // No guest cart to transfer
        }

        // Transfer each item
        foreach ($guestItems as $item) {
            $productId = (int)$item['p_id'];
            $quantity = (int)$item['qty'];

            // Check if user already has this product in cart
            $existing = $this->productExistsInCart($productId, $customerId, '');
            
            if ($existing) {
                // Update quantity (add guest quantity to existing)
                $newQty = (int)$existing['qty'] + $quantity;
                $this->updateQuantity($productId, $newQty, $customerId, '');
            } else {
                // Insert new cart item for user
                $insertSql = "INSERT INTO cart (p_id, c_id, ip_add, qty) VALUES (?, ?, '', ?)";
                $insertStmt = $this->db->prepare($insertSql);
                $insertStmt->bind_param('iii', $productId, $customerId, $quantity);
                $insertStmt->execute();
                $insertStmt->close();
            }
        }

        // Delete guest cart items
        $deleteSql = "DELETE FROM cart WHERE ip_add = ? AND c_id IS NULL";
        $deleteStmt = $this->db->prepare($deleteSql);
        $deleteStmt->bind_param('s', $ipAddress);
        $deleteStmt->execute();
        $deleteStmt->close();

        return true;
    }
}

?>

