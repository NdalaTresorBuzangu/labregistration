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
    <title>Admin | Brands</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/app.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="app-shell">
    <nav class="navbar navbar-expand-lg app-navbar px-4 py-3 mb-4">
        <a class="navbar-brand" href="../index.php">Taste of Africa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="category.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="brand.php">Brands</a></li>
                <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
            </ul>
            <a href="../logout.php" class="btn btn-outline-light btn-sm ms-lg-3">Logout</a>
        </div>
    </nav>

    <div class="container-xl">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="app-card h-100">
                    <span class="badge-gradient">Create brand</span>
                    <h4 class="mt-3">Add a new brand</h4>
                    <p class="text-muted">Pair brands with categories to build a layered product catalogue.</p>
                    <form id="brand-form" class="mt-3">
                        <div class="mb-3">
                            <label for="brand_name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brand_name" name="name" placeholder="e.g. Sunrise Organics" required>
                        </div>
                        <div class="mb-4">
                            <label for="brand_category" class="form-label">Category</label>
                            <select id="brand_category" name="category_id" class="form-select" required>
                                <option value="" disabled selected>Select category</option>
                            </select>
                        </div>
                        <button type="submit" class="btn app-button-primary w-100">Add brand</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="app-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h4 class="mb-1">Brands by category</h4>
                            <p class="text-muted mb-0">Search and manage brands grouped by their parent categories.</p>
                        </div>
                        <div class="w-100 w-md-auto" style="max-width:280px;">
                            <input type="search" class="form-control" id="brand_search" placeholder="Search brands or categories">
                        </div>
                    </div>

                    <div id="brand-accordion" class="accordion" data-search="">
                        <div class="app-empty-state" id="brand-empty-state" style="display:none;">
                            <h5 class="fw-semibold mb-2">No brands yet</h5>
                            <p class="mb-0">Create your first brand using the form on the left to populate this view.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade app-modal" id="editBrandModal" tabindex="-1" aria-hidden="true">
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn app-button-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/brand.js"></script>
</body>
</html>

