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

    public function addGalleryImage(int $userId, int $productId, string $imagePath): array
    {
        $imagePath = trim($imagePath);

        if ($productId <= 0 || $imagePath === '') {
            return ['success' => false, 'message' => 'Invalid product or image'];
        }

        if (!$this->ownsProduct($userId, $productId)) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if ($stmt = $this->db->prepare('INSERT INTO product_images (product_id, image_path, added_by) VALUES (?, ?, ?)')) {
            $stmt->bind_param('isi', $productId, $imagePath, $userId);
            $success = $stmt->execute();
            $imageId = $this->db->insert_id;
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Gallery image added', 'image_id' => $imageId, 'path' => $imagePath]
                : ['success' => false, 'message' => 'Failed to store gallery image'];
        }

        return ['success' => false, 'message' => 'Database error while storing gallery image'];
    }

    public function getGalleryImagesForProducts(int $userId, array $productIds): array
    {
        $productIds = array_values(array_unique(array_map('intval', $productIds)));

        if (empty($productIds)) {
            return [];
        }

        $idList = implode(',', $productIds);
        $sql = "SELECT image_id, product_id, image_path, created_at
                FROM product_images
                WHERE added_by = ? AND product_id IN ($idList)
                ORDER BY created_at DESC";

        $grouped = [];

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();

            foreach ($rows as $row) {
                $productId = (int)$row['product_id'];
                $grouped[$productId][] = [
                    'image_id' => (int)$row['image_id'],
                    'path' => $row['image_path'],
                    'created_at' => $row['created_at'],
                ];
            }
        }

        return $grouped;
    }

    public function viewAllProducts(?int $limit = null, int $offset = 0, array $filters = []): array
    {
        return $this->fetchProducts($filters, $limit, $offset);
    }

    public function searchProducts(string $query, ?int $limit = null, int $offset = 0, array $filters = []): array
    {
        $filters['query'] = $query;
        return $this->fetchProducts($filters, $limit, $offset);
    }

    public function filterProductsByCategory(int $categoryId, ?int $limit = null, int $offset = 0, array $filters = []): array
    {
        $filters['category_id'] = $categoryId;
        return $this->fetchProducts($filters, $limit, $offset);
    }

    public function filterProductsByBrand(int $brandId, ?int $limit = null, int $offset = 0, array $filters = []): array
    {
        $filters['brand_id'] = $brandId;
        return $this->fetchProducts($filters, $limit, $offset);
    }

    public function viewSingleProduct(int $productId): ?array
    {
        $result = $this->fetchProducts(['product_id' => $productId], null, 0);
        return $result[0] ?? null;
    }

    public function countProducts(array $filters = []): int
    {
        [$whereClause, $types, $params] = $this->buildFilterQuery($filters);
        $sql = 'SELECT COUNT(*) AS total FROM products p WHERE 1=1' . $whereClause;

        if ($stmt = $this->db->prepare($sql)) {
            $this->bindDynamicParams($stmt, $types, $params);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return (int)($result['total'] ?? 0);
        }

        return 0;
    }

    public function listAllBrands(): array
    {
        $brands = [];
        $sql = 'SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC';

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->execute();
            $result = $stmt->get_result();
            $brands = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        }

        return $brands;
    }

    private function fetchProducts(array $filters = [], ?int $limit = null, int $offset = 0): array
    {
        [$whereClause, $types, $params] = $this->buildFilterQuery($filters);

        $sql = 'SELECT p.product_id, p.product_cat, p.product_brand, p.product_title, p.product_price, '
             . 'p.product_desc, p.product_image, p.product_keywords, p.added_by, p.created_at, p.updated_at, '
             . 'c.cat_name, b.brand_name '
             . 'FROM products p '
             . 'INNER JOIN categories c ON c.cat_id = p.product_cat '
             . 'INNER JOIN brands b ON b.brand_id = p.product_brand '
             . 'WHERE 1=1' . $whereClause
             . ' ORDER BY p.created_at DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $types .= 'ii';
            $params[] = (int)$limit;
            $params[] = max(0, (int)$offset);
        }

        $products = [];

        if ($stmt = $this->db->prepare($sql)) {
            $this->bindDynamicParams($stmt, $types, $params);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        }

        if (empty($products)) {
            return [];
        }

        $productIds = array_map(static function ($product) {
            return (int)$product['product_id'];
        }, $products);

        $galleryMap = $this->getGalleryImagesPublic($productIds);

        foreach ($products as &$product) {
            $id = (int)$product['product_id'];
            $product['gallery'] = $galleryMap[$id] ?? [];
            if ((empty($product['product_image']) || $product['product_image'] === null) && !empty($product['gallery'])) {
                $product['product_image'] = $product['gallery'][0]['path'];
            }
        }
        unset($product);

        return $products;
    }

    private function buildFilterQuery(array $filters): array
    {
        $clauses = [];
        $types = '';
        $params = [];

        if (!empty($filters['product_id'])) {
            $clauses[] = ' AND p.product_id = ?';
            $types .= 'i';
            $params[] = (int)$filters['product_id'];
        }

        if (!empty($filters['category_id'])) {
            $clauses[] = ' AND p.product_cat = ?';
            $types .= 'i';
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $clauses[] = ' AND p.product_brand = ?';
            $types .= 'i';
            $params[] = (int)$filters['brand_id'];
        }

        if (!empty($filters['added_by'])) {
            $clauses[] = ' AND p.added_by = ?';
            $types .= 'i';
            $params[] = (int)$filters['added_by'];
        }

        if (!empty($filters['price_min'])) {
            $clauses[] = ' AND p.product_price >= ?';
            $types .= 'd';
            $params[] = (float)$filters['price_min'];
        }

        if (!empty($filters['price_max'])) {
            $clauses[] = ' AND p.product_price <= ?';
            $types .= 'd';
            $params[] = (float)$filters['price_max'];
        }

        if (!empty($filters['query'])) {
            $like = '%' . $filters['query'] . '%';
            $clauses[] = ' AND (p.product_title LIKE ? OR p.product_keywords LIKE ?)';
            $types .= 'ss';
            $params[] = $like;
            $params[] = $like;
        }

        if (!empty($filters['keywords'])) {
            $clauses[] = ' AND FIND_IN_SET(?, p.product_keywords)';
            $types .= 's';
            $params[] = $filters['keywords'];
        }

        return [implode('', $clauses), $types, $params];
    }

    private function getGalleryImagesPublic(array $productIds): array
    {
        if (empty($productIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        $sql = "SELECT image_id, product_id, image_path, created_at
                FROM product_images
                WHERE product_id IN ($placeholders)
                ORDER BY created_at DESC";

        $types = str_repeat('i', count($productIds));
        $gallery = [];

        if ($stmt = $this->db->prepare($sql)) {
            $this->bindDynamicParams($stmt, $types, $productIds);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();

            foreach ($rows as $row) {
                $pid = (int)$row['product_id'];
                $gallery[$pid][] = [
                    'image_id' => (int)$row['image_id'],
                    'path' => $row['image_path'],
                    'created_at' => $row['created_at'],
                ];
            }
        }

        return $gallery;
    }

    private function bindDynamicParams(\mysqli_stmt $stmt, string $types, array $params): void
    {
        if ($types === '' || empty($params)) {
            return;
        }

        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }

        array_unshift($refs, $types);
        $stmt->bind_param(...$refs);
    }

    private function ownsProduct(int $userId, int $productId): bool
    {
        if ($stmt = $this->db->prepare('SELECT 1 FROM products WHERE product_id = ? AND added_by = ? LIMIT 1')) {
            $stmt->bind_param('ii', $productId, $userId);
            $stmt->execute();
            $stmt->store_result();
            $owns = $stmt->num_rows > 0;
            $stmt->close();
            return $owns;
        }

        return false;
    }

    public function deleteGalleryImage(int $userId, int $imageId): bool
    {
        if ($stmt = $this->db->prepare('DELETE FROM product_images WHERE image_id = ? AND added_by = ?')) {
            $stmt->bind_param('ii', $imageId, $userId);
            $stmt->execute();
            $affected = $stmt->affected_rows > 0;
            $stmt->close();
            return $affected;
        }

        return false;
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

