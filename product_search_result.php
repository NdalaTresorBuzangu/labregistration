<?php
session_start();

$initialQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$initialCategory = isset($_GET['category_id']) ? trim($_GET['category_id']) : '';
$initialBrand = isset($_GET['brand_id']) ? trim($_GET['brand_id']) : '';

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
    <title>Search Results · Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
</head>
<body data-page="search-results" data-query="<?php echo htmlspecialchars($initialQuery, ENT_QUOTES); ?>" data-api-base="actions/product_actions.php">
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
                <input type="text" name="q" class="form-control" placeholder="Search products" value="<?php echo htmlspecialchars($initialQuery); ?>" data-role="product-search-input">
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
        <div class="app-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <span class="badge-gradient">Search results</span>
                    <h2 class="mt-3 mb-1">Results for “<?php echo htmlspecialchars($initialQuery ?: 'all'); ?>”</h2>
                    <p class="text-muted mb-0">Refine your search by selecting a category or brand, or adjust price filters below.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <select class="form-select" data-role="filter-category"></select>
                    <select class="form-select" data-role="filter-brand"></select>
                </div>
            </div>
            <div>
                <form class="row g-3" data-role="inline-search-form">
                    <div class="col-lg-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search by name or keyword" data-role="product-search-input" value="<?php echo htmlspecialchars($initialQuery); ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <input type="number" min="0" step="0.01" class="form-control" placeholder="Min price" name="price_min" data-role="filter-price-min">
                    </div>
                    <div class="col-lg-3">
                        <input type="number" min="0" step="0.01" class="form-control" placeholder="Max price" name="price_max" data-role="filter-price-max">
                    </div>
                </form>
            </div>
        </div>

        <div id="product-state-message" class="mb-4"></div>
        <div id="product-grid" class="mb-4"></div>
        <div id="product-pagination" class="d-flex flex-wrap gap-2 justify-content-center"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/storefront.js"></script>
</body>
</html>

