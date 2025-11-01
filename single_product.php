<?php
session_start();

require_once __DIR__ . '/controllers/product_controller.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productId > 0 ? view_single_product_public_ctr($productId) : null;

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && (int)$_SESSION['role'] === 2;
}

function getDisplayName(): string
{
    return isset($_SESSION['name']) && $_SESSION['name'] !== '' ? $_SESSION['name'] : 'Guest';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $product ? htmlspecialchars($product['product_title']) . ' · ' : ''; ?>Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
</head>
<body data-page="single-product" data-api-base="actions/product_actions.php">
<div class="app-shell">
    <nav class="navbar navbar-expand-lg app-navbar px-4 py-3 mb-4">
        <a class="navbar-brand" href="index.php">Taste of Africa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#storeNav" aria-controls="storeNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="storeNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="all_product.php">All Products</a></li>
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="login/register.php">Register</a></li>
                <?php endif; ?>
                <?php if (isLoggedIn() && isAdmin()): ?>
                    <li class="nav-item"><a class="nav-link" href="admin/category.php">Admin</a></li>
                <?php endif; ?>
            </ul>
            <form class="d-flex flex-wrap gap-2 align-items-center" action="product_search_result.php" method="get">
                <input type="text" name="q" class="form-control" placeholder="Search products" data-role="product-search-input">
                <select class="form-select" name="category_id" data-role="filter-category"></select>
                <select class="form-select" name="brand_id" data-role="filter-brand"></select>
                <button class="btn app-button-primary" type="submit">Search</button>
            </form>
            <?php if (isLoggedIn()): ?>
                <div class="ms-lg-3 text-white-50 small">Signed in as <?php echo htmlspecialchars(getDisplayName()); ?></div>
            <?php else: ?>
                <a href="login/login.php" class="btn btn-outline-light btn-sm ms-lg-3">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container-xxl">
        <?php if (!$product): ?>
            <div class="app-card">
                <div class="app-empty-state">
                    <h5 class="fw-semibold mb-2">Product not found</h5>
                    <p class="mb-3">The product you are looking for might have been removed or is temporarily unavailable.</p>
                    <a href="all_product.php" class="btn app-button-primary">Browse all products</a>
                </div>
            </div>
        <?php else: ?>
            <div class="app-card">
                <div class="row g-5">
                    <div class="col-lg-6">
                        <?php
                        $primaryImage = isset($product['product_image']) && $product['product_image'] !== ''
                            ? $product['product_image']
                            : 'https://via.placeholder.com/600x480?text=No+Image';
                        ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($primaryImage); ?>" alt="<?php echo htmlspecialchars($product['product_title']); ?>" class="img-fluid rounded" style="width:100%;height:auto;object-fit:cover;">
                        </div>
                        <?php if (!empty($product['gallery'])): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($product['gallery'] as $galleryItem):
                                    $thumbPath = isset($galleryItem['path']) && $galleryItem['path'] !== '' ? $galleryItem['path'] : 'https://via.placeholder.com/110x110?text=No+Image';
                                ?>
                                    <div class="gallery-thumb" style="width:110px;">
                                        <img src="<?php echo htmlspecialchars($thumbPath); ?>" alt="Gallery image">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-6">
                        <span class="badge badge-gradient mb-3"><?php echo htmlspecialchars($product['cat_name']); ?></span>
                        <h1 class="fw-bold mb-3"><?php echo htmlspecialchars($product['product_title']); ?></h1>
                        <p class="text-muted mb-2">Brand: <strong><?php echo htmlspecialchars($product['brand_name']); ?></strong></p>
                        <h3 class="fw-semibold mb-4"><?php echo number_format((float)$product['product_price'], 2); ?> USD</h3>
                        <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['product_desc'] ?? 'No description provided.')); ?></p>
                        <div class="d-flex gap-2 mb-4">
                            <button class="btn app-button-primary" type="button" disabled>Add to cart</button>
                            <a href="all_product.php" class="btn btn-outline-secondary">Back to products</a>
                        </div>
                        <div class="small text-muted">
                            <div class="mb-1">Product ID: <?php echo (int)$product['product_id']; ?></div>
                            <?php if (!empty($product['product_keywords'])): ?>
                                <div>Keywords:
                                    <?php
                                    $keywords = array_map('trim', explode(',', $product['product_keywords']));
                                    foreach ($keywords as $keyword):
                                        if ($keyword === '') {
                                            continue;
                                        }
                                        ?>
                                        <span class="badge bg-light text-dark me-1">#<?php echo htmlspecialchars($keyword); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/storefront.js"></script>
</body>
</html>

