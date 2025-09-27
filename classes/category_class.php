<?php
include_once 'config.php'; // your database connection

class Category {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection(); // assumes a Database class in config.php
    }

    public function getAll($user_id = null) {
        $sql = "SELECT * FROM categories ORDER BY cat_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($name) {
        // Check if name exists
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE cat_name = ?");
        $stmt->execute([$name]);
        if($stmt->rowCount() > 0){
            return ["success"=>false, "message"=>"Category already exists"];
        }

        $stmt = $this->conn->prepare("INSERT INTO categories(cat_name) VALUES(?)");
        if($stmt->execute([$name])){
            return ["success"=>true, "message"=>"Category added successfully"];
        } else {
            return ["success"=>false, "message"=>"Failed to add category"];
        }
    }

    public function update($id, $name) {
        $stmt = $this->conn->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        if($stmt->execute([$name, $id])){
            return ["success"=>true, "message"=>"Category updated successfully"];
        } else {
            return ["success"=>false, "message"=>"Failed to update category"];
        }
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE cat_id = ?");
        if($stmt->execute([$id])){
            return ["success"=>true, "message"=>"Category deleted successfully"];
        } else {
            return ["success"=>false, "message"=>"Failed to delete category"];
        }
    }
}
?>

