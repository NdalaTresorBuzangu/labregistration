<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || (int)$_SESSION['role'] !== 2) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Products</title>
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
                <li class="nav-item"><a class="nav-link" href="brand.php">Brands</a></li>
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="product.php">Products</a></li>
            </ul>
            <a href="../logout.php" class="btn btn-outline-light btn-sm ms-lg-3">Logout</a>
        </div>
    </nav>

    <div class="container-xxl">
        <div class="row g-4">
            <div class="col-xl-4">
                <div class="app-card h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <span class="badge-gradient">Product form</span>
                            <h4 class="mt-2 mb-1" id="product-form-title">Add Product</h4>
                            <p class="text-muted mb-0">Capture core details, assign a brand, and upload your hero imagery.</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="product-cancel-edit">Cancel</button>
                    </div>

                    <form id="product-form" enctype="multipart/form-data" class="mt-3">
                        <input type="hidden" id="product_id" name="product_id">
                        <input type="hidden" id="product_image_path" name="image_path">

                        <div class="mb-3">
                            <label for="product_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="product_title" name="title" placeholder="Product title" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label for="product_price" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="product_price" name="price" min="0" required>
                            </div>
                            <div class="col-6">
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
                            <input type="text" class="form-control" id="product_keywords" name="keywords" placeholder="e.g. artisan, organic">
                        </div>

                        <div class="mb-3">
                            <label for="product_description" class="form-label">Description</label>
                            <textarea class="form-control" id="product_description" name="description" rows="3" placeholder="Describe the product"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="product_image">Main image</label>
                            <input class="form-control" type="file" id="product_image" name="product_image" accept="image/*">
                            <div class="mt-2" id="product-image-preview" style="display:none;">
                                <img src="" alt="Product" class="img-fluid rounded border" id="product-preview-img">
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="product-bulk-upload-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" for="product_bulk_images">Gallery images</label>
                                <span class="small text-muted" id="product-bulk-upload-status"></span>
                            </div>
                            <input class="form-control" type="file" id="product_bulk_images" name="product_images[]" accept="image/*" multiple>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn app-button-primary btn-sm" id="product-bulk-upload-btn">Upload selected</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="product-bulk-clear-btn">Clear</button>
                            </div>
                            <div class="small text-muted mt-2">Add supplemental images to build a gallery for this product.</div>
                        </div>

                        <div class="mb-3 d-none" id="product-gallery-preview"></div>

                        <div class="d-grid">
                            <button type="submit" class="btn app-button-primary" id="product-submit-btn">Save product</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="app-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h4 class="mb-1">Product catalogue</h4>
                            <p class="text-muted mb-0">Browse products grouped by category and brand with gallery insights.</p>
                        </div>
                        <div class="w-100 w-md-auto" style="max-width:320px;">
                            <input type="search" class="form-control" id="product_search" placeholder="Search products, brands, or categories">
                        </div>
                    </div>

                    <div class="accordion" id="product-accordion">
                        <div class="app-empty-state" id="product-empty-state" style="display:none;">
                            <h5 class="fw-semibold mb-2">No products yet</h5>
                            <p class="mb-0">Once you add products they will appear here, grouped by category and brand.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade app-modal" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_product_title">Product details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal_product_body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn app-button-primary" id="modal_edit_product">Edit product</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/product.js"></script>
</body>
</html>

