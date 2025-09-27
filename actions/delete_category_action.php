<?php
session_start();
include '../controllers/category_controller.php';

header("Content-Type: application/json");

if(isset($_POST['id'])){
    $id = $_POST['id'];
    $result = delete_category_ctr($id);
    echo json_encode($result);
}
?>

