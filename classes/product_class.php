<?php

require_once '../settings/db_class.php';

class Product extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    public function add(int $userId, array $payload): array
    {
        $validation = $this->validatePayload($payload, false);
        if ($validation['success'] === false) {
            return $validation;
        }

        $data = $validation['data'];

        $brand = $this->getBrand($data['brand_id']);
        if (!$brand) {
            return ['success' => false, 'message' => 'Selected brand does not exist'];
        }

        if ((int)$brand['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'You do not own the selected brand'];
        }

        if ((int)$brand['category_id'] !== (int)$data['category_id']) {
            return ['success' => false, 'message' => 'Brand does not belong to the selected category'];
        }

        $sql = 'INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, added_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';

        if ($stmt = $this->db->prepare($sql)) {
            $price = (float)$data['price'];
            $desc = $data['description'];
            $image = $data['image_path'] ?? null;
            $keywords = $data['keywords'];
            $stmt->bind_param(
                'iisdsssi',
                $data['category_id'],
                $data['brand_id'],
                $data['title'],
                $price,
                $desc,
                $image,
                $keywords,
                $userId
            );

            if ($stmt->execute()) {
                $productId = $this->db->insert_id;
                $stmt->close();
                return ['success' => true, 'message' => 'Product added successfully', 'product_id' => $productId];
            }

            $stmt->close();
        }

        return ['success' => false, 'message' => 'Failed to add product'];
    }

    public function update(int $userId, int $productId, array $payload, ?string $imagePath = null): array
    {
        $validation = $this->validatePayload($payload, false);
        if ($validation['success'] === false) {
            return $validation;
        }

        $data = $validation['data'];

        $product = $this->getById($userId, $productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        $brand = $this->getBrand($data['brand_id']);
        if (!$brand) {
            return ['success' => false, 'message' => 'Selected brand does not exist'];
        }

        if ((int)$brand['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'You do not own the selected brand'];
        }

        if ((int)$brand['category_id'] !== (int)$data['category_id']) {
            return ['success' => false, 'message' => 'Brand does not belong to the selected category'];
        }

        $fields = 'product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_keywords = ?';
        $types = 'iisdss';
        $params = [
            $data['category_id'],
            $data['brand_id'],
            $data['title'],
            (float)$data['price'],
            $data['description'],
            $data['keywords'],
        ];

        if ($imagePath !== null) {
            $fields .= ', product_image = ?';
            $types .= 's';
            $params[] = $imagePath;
        }

        $types .= 'ii';
        $params[] = $productId;
        $params[] = $userId;

        $sql = "UPDATE products SET {$fields} WHERE product_id = ? AND added_by = ?";

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param($types, ...$params);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Product updated successfully']
                : ['success' => false, 'message' => 'Failed to update product'];
        }

        return ['success' => false, 'message' => 'Database error while updating product'];
    }

    public function getById(int $userId, int $productId): ?array
    {
        $sql = 'SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                INNER JOIN categories c ON c.cat_id = p.product_cat
                INNER JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_id = ? AND p.added_by = ?
                LIMIT 1';

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('ii', $productId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            return $product ?: null;
        }

        return null;
    }

    public function listByUser(int $userId): array
    {
        $sql = 'SELECT p.product_id, p.product_title, p.product_price, p.product_desc, p.product_image, p.product_keywords,
                       p.product_cat, p.product_brand, c.cat_name, b.brand_name
                FROM products p
                INNER JOIN categories c ON c.cat_id = p.product_cat
                INNER JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.added_by = ?
                ORDER BY c.cat_name ASC, b.brand_name ASC, p.product_title ASC';

        $products = [];
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        }

        return $products;
    }

    public function delete(int $userId, int $productId): bool
    {
        if ($stmt = $this->db->prepare('DELETE FROM products WHERE product_id = ? AND added_by = ?')) {
            $stmt->bind_param('ii', $productId, $userId);
            $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();
            return $affected > 0;
        }

        return false;
    }

    public function updateImagePath(int $userId, int $productId, string $imagePath): array
    {
        if ($stmt = $this->db->prepare('UPDATE products SET product_image = ? WHERE product_id = ? AND added_by = ?')) {
            $stmt->bind_param('sii', $imagePath, $productId, $userId);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Product image updated']
                : ['success' => false, 'message' => 'Failed to update product image'];
        }

        return ['success' => false, 'message' => 'Database error while updating image'];
    }

    private function validatePayload(array $payload, bool $requireImage = false): array
    {
        $normalized = [
            'title' => isset($payload['title']) ? trim($payload['title']) : '',
            'price' => isset($payload['price']) ? (float)$payload['price'] : 0.0,
            'category_id' => isset($payload['category_id']) ? (int)$payload['category_id'] : 0,
            'brand_id' => isset($payload['brand_id']) ? (int)$payload['brand_id'] : 0,
            'description' => isset($payload['description']) ? trim($payload['description']) : '',
            'keywords' => isset($payload['keywords']) ? trim($payload['keywords']) : '',
            'image_path' => isset($payload['image_path']) ? trim($payload['image_path']) : null,
        ];

        if ($normalized['image_path'] === '') {
            $normalized['image_path'] = null;
        }

        if ($normalized['title'] === '' || $normalized['category_id'] <= 0 || $normalized['brand_id'] <= 0) {
            return ['success' => false, 'message' => 'Title, category, and brand are required'];
        }

        if ($normalized['price'] < 0) {
            return ['success' => false, 'message' => 'Price cannot be negative'];
        }

        if ($requireImage && empty($normalized['image_path'])) {
            return ['success' => false, 'message' => 'Product image is required'];
        }

        return ['success' => true, 'data' => $normalized];
    }

    private function getBrand(int $brandId): ?array
    {
        $sql = 'SELECT brand_id, brand_name, category_id, user_id FROM brands WHERE brand_id = ? LIMIT 1';
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $brandId);
            $stmt->execute();
            $result = $stmt->get_result();
            $brand = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            return $brand ?: null;
        }

        return null;
    }
}

?>

