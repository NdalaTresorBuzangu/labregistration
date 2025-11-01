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
<body>

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
                <div class="ms-auto d-flex align-items-center gap-2">
                    <?php if (!$isLoggedIn): ?>
                        <a href="login/register.php" class="btn app-button-ghost btn-sm">Register</a>
                        <a href="login/login.php" class="btn app-button-primary btn-sm">Login</a>
                    <?php else: ?>
                        <?php if ($isAdmin): ?>
                            <a href="admin/category.php" class="btn app-button-ghost btn-sm">Categories</a>
                            <a href="admin/brand.php" class="btn app-button-ghost btn-sm">Brands</a>
                            <a href="admin/product.php" class="btn app-button-primary btn-sm">Products</a>
                        <?php endif; ?>
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
</body>
</html>

