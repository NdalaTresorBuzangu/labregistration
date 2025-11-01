<?php

require_once '../settings/db_class.php';

class Category extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    public function getAll(): array
    {
        $categories = [];
        $sql = 'SELECT cat_id, cat_name FROM categories ORDER BY cat_name ASC';

        if ($stmt = $this->db->prepare($sql)) {
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $categories = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            }
            $stmt->close();
        }

        return $categories;
    }

    public function add(string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if ($stmt = $this->db->prepare('SELECT 1 FROM categories WHERE LOWER(cat_name) = LOWER(?) LIMIT 1')) {
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                return ['success' => false, 'message' => 'Category already exists'];
            }
            $stmt->close();
        }

        if ($stmt = $this->db->prepare('INSERT INTO categories (cat_name) VALUES (?)')) {
            $stmt->bind_param('s', $name);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Category added successfully']
                : ['success' => false, 'message' => 'Failed to add category'];
        }

        return ['success' => false, 'message' => 'Database error while adding category'];
    }

    public function update(int $id, string $name): array
    {
        $name = trim($name);
        if ($name === '') {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if ($stmt = $this->db->prepare('SELECT 1 FROM categories WHERE LOWER(cat_name) = LOWER(?) AND cat_id <> ? LIMIT 1')) {
            $stmt->bind_param('si', $name, $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                return ['success' => false, 'message' => 'Another category already uses that name'];
            }
            $stmt->close();
        }

        if ($stmt = $this->db->prepare('UPDATE categories SET cat_name = ? WHERE cat_id = ?')) {
            $stmt->bind_param('si', $name, $id);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Category updated successfully']
                : ['success' => false, 'message' => 'Failed to update category'];
        }

        return ['success' => false, 'message' => 'Database error while updating category'];
    }

    public function delete(int $id): array
    {
        if ($stmt = $this->db->prepare('DELETE FROM categories WHERE cat_id = ?')) {
            $stmt->bind_param('i', $id);
            $success = $stmt->execute();
            $stmt->close();

            return $success
                ? ['success' => true, 'message' => 'Category deleted successfully']
                : ['success' => false, 'message' => 'Failed to delete category'];
        }

        return ['success' => false, 'message' => 'Database error while deleting category'];
    }
}

?>