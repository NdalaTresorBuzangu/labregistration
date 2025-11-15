<?php
require_once __DIR__ . '/../classes/category_class.php';

$category = new Category();

function add_category_ctr($name){
    global $category;
    return $category->add($name);
}

function fetch_category_ctr(){
    global $category;
    return $category->getAll();
}

function update_category_ctr($id, $name){
    global $category;
    return $category->update($id, $name);
}

function delete_category_ctr($id){
    global $category;
    return $category->delete($id);
}
?>

