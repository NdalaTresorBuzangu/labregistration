<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header('Location: ../login/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">Dashboard</a>
        <div class="d-flex">
            <a class="btn btn-outline-light btn-sm me-2" href="category.php">Categories</a>
            <a class="btn btn-primary btn-sm" href="product.php">Products</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Create Brand</h5>
                    <form id="brand-form">
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brand_name" name="name" placeholder="Enter brand name" required>
                        </div>
                        <div class="mb-3">
                            <label for="brand_category" class="form-label">Category</label>
                            <select id="brand_category" name="category_id" class="form-select" required>
                                <option value="" disabled selected>Select category</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Add Brand</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Brands by Category</h4>
                <div class="w-50">
                    <input type="search" class="form-control" id="brand_search" placeholder="Search brands or categories">
                </div>
            </div>

            <div id="brand-accordion" class="accordion shadow-sm" data-search="">
                <div class="text-center py-5 text-muted" id="brand-empty-state" style="display: none;">
                    <p class="lead mb-0">No brands found. Create your first brand using the form.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="edit-brand-form">
                <div class="modal-header">
                    <h5 class="modal-title">Update Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_brand_id">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" id="edit_brand_category" disabled>
                    </div>
                    <div class="mb-0">
                        <label for="edit_brand_name" class="form-label">Brand Name</label>
                        <input type="text" id="edit_brand_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/brand.js"></script>
</body>
</html>

