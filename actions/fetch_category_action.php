<?php
session_start();
include '../controllers/category_controller.php';

header("Content-Type: application/json");

$categories = fetch_category_ctr();

// Optional: filter by search or sort
if(isset($_POST['search']) && $_POST['search'] != ''){
    $categories = array_filter($categories, function($cat){
        return stripos($cat['cat_name'], $_POST['search']) !== false;
    });
}

// Optional: sorting
if(isset($_POST['sortColumn']) && isset($_POST['sortOrder'])){
    usort($categories, function($a, $b){
        $col = $_POST['sortColumn'];
        if($_POST['sortOrder'] == 'asc'){
            return strcmp($a[$col], $b[$col]);
        } else {
            return strcmp($b[$col], $a[$col]);
        }
    });
}

echo json_encode(array_values($categories));
?>


