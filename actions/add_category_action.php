<?php
session_start();
include '../controllers/category_controller.php';

header("Content-Type: application/json");

if(isset($_POST['name'])){
    $name = trim($_POST['name']);
    $result = add_category_ctr($name);
    echo json_encode($result);
}
?>


