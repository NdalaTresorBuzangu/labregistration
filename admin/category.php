<?php
session_start();
#include '../core.php';
#include '../config.php';
#isLogin(); // ensure user is logged in

// Redirect if not admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 2) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="p-4">
<div class="container">
    <h2>Category Management</h2>

    <!-- Add Category Form -->
    <form id="add-category-form" class="mb-4">
        <div class="input-group">
            <input type="text" id="category_name" name="name" class="form-control" placeholder="Category Name" required>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </div>
    </form>

    <!-- Search -->
    <input type="text" id="search" class="form-control mb-3" placeholder="Search Categories...">

    <!-- Categories Table -->
    <table class="table table-bordered" id="category-table">
        <thead>
            <tr>
                <th>ID <button class="btn btn-sm btn-link sort" data-column="cat_id" data-order="asc">⇅</button></th>
                <th>Name <button class="btn btn-sm btn-link sort" data-column="cat_name" data-order="asc">⇅</button></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script src="category.js"></script>
</body>
</html>


