<?php

require_once __DIR__ . '/../settings/db_class.php';

class Brand extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    public function add(int $userId, int $categoryId, string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['success' => false, 'message' => 'Brand name is required'];
        }

        if (!$this->categoryExists($categoryId)) {
            return ['success' => false, 'message' => 'Selected category does not exist'];
        }

        if ($this->brandExists($name, $categoryId, null)) {
            return ['success' => false, 'message' => 'Brand already exists for this category'];
        }

        if ($stmt = $this->db->prepare('INSERT INTO brands (brand_name, category_id, user_id) VALUES (?, ?, ?)')) {
            $stmt->bind_param('sii', $name, $categoryId, $userId);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Brand created successfully']
                : ['success' => false, 'message' => 'Failed to create brand'];
        }

        return ['success' => false, 'message' => 'Database error while creating brand'];
    }

    public function update(int $userId, int $brandId, string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['success' => false, 'message' => 'Brand name is required'];
        }

        $brand = $this->getById($brandId, $userId);
        if (!$brand) {
            return ['success' => false, 'message' => 'Brand not found'];
        }

        if ($this->brandExists($name, (int)$brand['category_id'], $brandId)) {
            return ['success' => false, 'message' => 'Another brand in this category already has that name'];
        }

        if ($stmt = $this->db->prepare('UPDATE brands SET brand_name = ? WHERE brand_id = ? AND user_id = ?')) {
            $stmt->bind_param('sii', $name, $brandId, $userId);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Brand updated successfully']
                : ['success' => false, 'message' => 'Failed to update brand'];
        }

        return ['success' => false, 'message' => 'Database error while updating brand'];
    }

    public function delete(int $userId, int $brandId): array
    {
        if ($stmt = $this->db->prepare('DELETE FROM brands WHERE brand_id = ? AND user_id = ?')) {
            $stmt->bind_param('ii', $brandId, $userId);
            $success = $stmt->execute();
            $affected = $stmt->affected_rows;
            $stmt->close();

            if ($success && $affected > 0) {
                return ['success' => true, 'message' => 'Brand deleted successfully'];
            }

            return ['success' => false, 'message' => 'Brand could not be deleted'];
        }

        return ['success' => false, 'message' => 'Database error while deleting brand'];
    }

    public function getGroupedByCategory(int $userId): array
    {
        $data = [];
        $sql = 'SELECT b.brand_id, b.brand_name, b.category_id, c.cat_name
                FROM brands b
                INNER JOIN categories c ON c.cat_id = b.category_id
                WHERE b.user_id = ?
                ORDER BY c.cat_name ASC, b.brand_name ASC';

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        }

        return $data;
    }

    public function getById(int $brandId, int $userId): ?array
    {
        $sql = 'SELECT brand_id, brand_name, category_id, user_id FROM brands WHERE brand_id = ? AND user_id = ? LIMIT 1';
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param('ii', $brandId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $brand = $result ? $result->fetch_assoc() : null;
            $stmt->close();
            return $brand ?: null;
        }

        return null;
    }

    private function categoryExists(int $categoryId): bool
    {
        if ($stmt = $this->db->prepare('SELECT 1 FROM categories WHERE cat_id = ? LIMIT 1')) {
            $stmt->bind_param('i', $categoryId);
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            return $exists;
        }

        return false;
    }

    private function brandExists(string $name, int $categoryId, ?int $excludeId): bool
    {
        $query = 'SELECT 1 FROM brands WHERE LOWER(brand_name) = LOWER(?) AND category_id = ?';
        $params = [$name, $categoryId];
        $types = 'si';

        if ($excludeId !== null) {
            $query .= ' AND brand_id <> ?';
            $types .= 'i';
            $params[] = $excludeId;
        }

        $query .= ' LIMIT 1';

        if ($stmt = $this->db->prepare($query)) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            return $exists;
        }

        return false;
    }
}

?>

