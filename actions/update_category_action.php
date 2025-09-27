<?php
session_start();
include '../controllers/category_controller.php';

header("Content-Type: application/json");

if(isset($_POST['id'], $_POST['name'])){
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $result = update_category_ctr($id, $name);
    echo json_encode($result);
}
?>

