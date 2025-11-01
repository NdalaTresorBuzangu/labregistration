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
    <title>Product Management</title>
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
            <a class="btn btn-outline-light btn-sm me-2" href="brand.php">Brands</a>
            <a class="btn btn-primary btn-sm" href="product.php">Products</a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0" id="product-form-title">Add Product</h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="product-cancel-edit">Cancel Edit</button>
                    </div>
                    <form id="product-form" enctype="multipart/form-data">
                        <input type="hidden" id="product_id" name="product_id">
                        <input type="hidden" id="product_image_path" name="image_path">

                        <div class="mb-3">
                            <label for="product_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="product_title" name="title" placeholder="Product title" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="product_price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="product_price" name="price" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="product_category" class="form-label">Category</label>
                                <select id="product_category" name="category_id" class="form-select" required>
                                    <option value="" disabled selected>Select category</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 mb-3">
                            <label for="product_brand" class="form-label">Brand</label>
                            <select id="product_brand" name="brand_id" class="form-select" required>
                                <option value="" disabled selected>Select brand</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="product_keywords" class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="product_keywords" name="keywords" placeholder="e.g. wireless, bluetooth">
                        </div>

                        <div class="mb-3">
                            <label for="product_description" class="form-label">Description</label>
                            <textarea class="form-control" id="product_description" name="description" rows="3" placeholder="Describe the product"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="product_image">Product Image</label>
                            <input class="form-control" type="file" id="product_image" name="product_image" accept="image/*">
                            <div class="mt-2" id="product-image-preview" style="display:none;">
                                <img src="" alt="Product" class="img-fluid rounded border" id="product-preview-img">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="product-submit-btn">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Product Catalogue</h4>
                <div class="w-50">
                    <input type="search" class="form-control" id="product_search" placeholder="Search products, brands, categories">
                </div>
            </div>

            <div class="accordion shadow-sm" id="product-accordion">
                <div class="text-center text-muted py-5" id="product-empty-state" style="display:none;">
                    <p class="lead mb-0">No products yet. Add your first product using the form.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Product Detail Modal -->
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_product_title">Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_product_body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="modal_edit_product">Edit Product</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/product.js"></script>
</body>
</html>

