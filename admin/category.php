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
    <title>Admin | Categories</title>
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
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="category.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link" href="brand.php">Brands</a></li>
                <li class="nav-item"><a class="nav-link" href="product.php">Products</a></li>
            </ul>
            <a href="../logout.php" class="btn btn-outline-light btn-sm ms-lg-3">Logout</a>
        </div>
    </nav>

    <div class="container-xl">
        <div class="app-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <span class="badge-gradient">Collections</span>
                    <h2 class="mt-3 mb-1">Category Management</h2>
                    <p class="text-muted mb-0">Shape the structure of your catalogue and keep everything organised.</p>
                </div>
                <form id="add-category-form" class="d-flex gap-2 flex-wrap">
                    <input type="text" id="category_name" name="name" class="form-control" placeholder="Add a new category" required>
                    <button type="submit" class="btn app-button-primary">Add</button>
                </form>
            </div>

            <div class="row g-3 align-items-center mb-3">
                <div class="col-md-6">
                    <input type="text" id="search" class="form-control" placeholder="Search categories...">
                </div>
                <div class="col-md-6 text-md-end small text-muted">
                    Sort the table or edit inline for instant updates.
                </div>
            </div>

            <div class="table-modern">
                <table class="table mb-0" id="category-table">
                    <thead>
                        <tr>
                            <th scope="col">ID <button class="btn btn-sm btn-link sort" data-column="cat_id" data-order="asc"><i class="fas fa-sort"></i></button></th>
                            <th scope="col">Name <button class="btn btn-sm btn-link sort" data-column="cat_name" data-order="asc"><i class="fas fa-sort"></i></button></th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/category.js"></script>
</body>
</html>


