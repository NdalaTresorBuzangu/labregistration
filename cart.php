<?php
session_start();

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function getDisplayName(): string
{
    return isset($_SESSION['name']) && $_SESSION['name'] !== '' ? $_SESSION['name'] : 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en" data-page="cart">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart · Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
</head>
<body data-page="cart">
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
                <li class="nav-item"><a class="nav-link active" href="cart.php">
                    <i class="fa fa-shopping-cart"></i> Cart
                    <span class="badge bg-danger cart-count-badge ms-1" style="display:none;">0</span>
                </a></li>
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
            <?php if (isLoggedIn()): ?>
                <div class="ms-lg-3 text-white-50 small">Signed in as <?php echo htmlspecialchars(getDisplayName()); ?></div>
                <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm ms-lg-3">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container-xxl">
        <div class="app-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge-gradient">Shopping Cart</span>
                    <h2 class="mt-3 mb-1">Your Cart</h2>
                    <p class="text-muted mb-0">Review your items and proceed to checkout when ready.</p>
                </div>
                <button class="btn btn-outline-danger empty-cart-btn">
                    <i class="fa fa-trash"></i> Empty Cart
                </button>
            </div>

            <div id="cart-items-container">
                <!-- Cart items will be loaded here by cart.js -->
                <div class="text-center py-5">
                    <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                    <p class="mt-3 text-muted">Loading cart...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/cart.js"></script>
</body>
</html>

