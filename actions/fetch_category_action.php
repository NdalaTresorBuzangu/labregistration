<?php
session_start();
include '../controllers/category_controller.php';

header('Content-Type: application/json');

$categories = fetch_category_ctr();

$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$sortColumn = isset($_POST['sortColumn']) ? $_POST['sortColumn'] : '';
$sortOrder = isset($_POST['sortOrder']) ? strtolower($_POST['sortOrder']) : '';

if ($search !== '') {
    $categories = array_filter($categories, static function ($cat) use ($search) {
        return stripos($cat['cat_name'], $search) !== false;
    });
}

if ($sortColumn !== '' && in_array($sortColumn, ['cat_id', 'cat_name'], true)) {
    $sortOrder = $sortOrder === 'desc' ? 'desc' : 'asc';
    usort($categories, static function ($a, $b) use ($sortColumn, $sortOrder) {
        if ($sortOrder === 'asc') {
            return strnatcasecmp($a[$sortColumn], $b[$sortColumn]);
        }
        return strnatcasecmp($b[$sortColumn], $a[$sortColumn]);
    });
}

echo json_encode([
    'success' => true,
    'data' => array_values($categories),
]);
?>


