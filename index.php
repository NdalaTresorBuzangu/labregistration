<?php
session_start();

function getCustomerName() {
    if (isset($_SESSION['name']) && $_SESSION['name'] !== '') {
        return $_SESSION['name'];
    }
    if (isset($_SESSION['customer_name']) && $_SESSION['customer_name'] !== '') {
        return $_SESSION['customer_name'];
    }
    return 'User';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taste of Africa | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
    <link rel="stylesheet" href="CSS/index.css">
</head>
<body data-page="landing" data-api-base="actions/product_actions.php">

    <?php
    $isLoggedIn = isset($_SESSION['user_id']);
    $isAdmin = isset($_SESSION['role']) && (int)$_SESSION['role'] === 2;
    ?>

    <div class="app-shell">
        <nav class="navbar navbar-expand-lg app-navbar px-4 py-3 mb-4">
            <a class="navbar-brand" href="index.php">Taste of Africa</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="primaryNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="all_product.php">All Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">
                        <i class="fa fa-shopping-cart"></i> Cart
                        <span class="badge bg-danger cart-count-badge ms-1" style="display:none;">0</span>
                    </a></li>
                    <?php if (!$isLoggedIn): ?>
                        <li class="nav-item"><a class="nav-link" href="login/register.php">Register</a></li>
                    <?php endif; ?>
                    <?php if ($isLoggedIn && $isAdmin): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/category.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
                <form class="d-flex flex-wrap gap-2 align-items-center" action="product_search_result.php" method="get">
                    <input type="text" name="q" class="form-control" placeholder="Search products" data-role="product-search-input">
                    <select class="form-select" name="category_id" data-role="filter-category"></select>
                    <select class="form-select" name="brand_id" data-role="filter-brand"></select>
                    <button class="btn app-button-primary" type="submit">Search</button>
                </form>
                <div class="d-flex gap-2 ms-lg-3">
                    <?php if (!$isLoggedIn): ?>
                        <a href="login/login.php" class="btn btn-outline-light btn-sm">Login</a>
                    <?php else: ?>
                        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="container-xl">
            <section class="dashboard-hero mb-5">
                <span class="badge-gradient">Welcome back</span>
                <h1 class="mt-3">
                    <?php echo $isLoggedIn ? 'Hello, ' . htmlspecialchars(getCustomerName()) . '!' : 'Taste of Africa, reimagined'; ?>
                </h1>
                <p class="mb-0">
                    <?php if ($isLoggedIn): ?>
                        Manage categories, brands, and products through the links above. New insights await inside your dashboard.
                    <?php else: ?>
                        Sign in to manage your marketplace or explore our catalogue. We crafted a modern workspace to keep your business moving.
                    <?php endif; ?>
                </p>
            </section>

            <section class="data-board mb-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="data-card">
                            <h6>Smart Workflows</h6>
                            <strong>Streamlined admin tools</strong>
                            <p class="text-muted mb-0">Create, update, and track every category, brand, and product in one cohesive experience.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="data-card">
                            <h6>Responsive Experience</h6>
                            <strong>Pixel-perfect UI</strong>
                            <p class="text-muted mb-0">Optimised layouts ensure your teams stay productive across desktops and tablets alike.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="data-card">
                            <h6>Next-gen Insights</h6>
                            <strong>Data-ready design</strong>
                            <p class="text-muted mb-0">Our component system paves the way for analytics, growth dashboards, and customer metrics.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="app-card">
                    <h3 class="app-section-title">Get Started</h3>
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <p class="text-muted mb-3">Use the navigation to access management tools, or pick an action below to jump right in.</p>
                            <div class="d-flex flex-wrap gap-3">
                                <?php if ($isLoggedIn && $isAdmin): ?>
                                    <a href="admin/product.php" class="btn app-button-primary">Create Product</a>
                                    <a href="admin/brand.php" class="btn app-button-ghost">Manage Brands</a>
                                    <a href="admin/category.php" class="btn app-button-ghost">Manage Categories</a>
                                <?php elseif (!$isLoggedIn): ?>
                                    <a href="login/login.php" class="btn app-button-primary">Sign In</a>
                                    <a href="login/register.php" class="btn app-button-ghost">Create Account</a>
                                <?php else: ?>
                                    <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="app-empty-state">
                                <h5 class="fw-semibold mb-2">New to the workspace?</h5>
                                <p class="mb-0">Start by adding categories, then brands, then products. Each step unlocks richer catalog experiences for your customers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/storefront.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>

