<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login/login.php');
    exit;
}

function getDisplayName(): string
{
    return isset($_SESSION['name']) && $_SESSION['name'] !== '' ? $_SESSION['name'] : 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en" data-page="checkout">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout · Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
</head>
<body data-page="checkout">
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
                <li class="nav-item"><a class="nav-link" href="cart.php">
                    <i class="fa fa-shopping-cart"></i> Cart
                    <span class="badge bg-danger cart-count-badge ms-1" style="display:none;">0</span>
                </a></li>
            </ul>
            <div class="ms-lg-3 text-white-50 small">Signed in as <?php echo htmlspecialchars(getDisplayName()); ?></div>
            <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
        </div>
    </nav>

    <div class="container-xxl">
        <div class="row g-4">
            <!-- Order Summary -->
            <div class="col-lg-8">
                <div class="app-card mb-4">
                    <div class="mb-4">
                        <span class="badge-gradient">Order Summary</span>
                        <h2 class="mt-3 mb-1">Review Your Order</h2>
                        <p class="text-muted mb-0">Please review your items before proceeding to payment.</p>
                    </div>

                    <div id="checkout-summary">
                        <!-- Cart summary will be loaded here by checkout.js -->
                        <div class="text-center py-5">
                            <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                            <p class="mt-3 text-muted">Loading order summary...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="col-lg-4">
                <div class="app-card">
                    <h5 class="mb-4">Payment</h5>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Simulated Payment</strong>
                        <p class="mb-0 small">This is a demonstration checkout. No real payment will be processed.</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" disabled>
                            <option>Credit/Debit Card (Simulated)</option>
                        </select>
                        <small class="text-muted">Payment gateway integration not implemented</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn app-button-primary btn-lg simulate-payment-btn">
                            <i class="fa fa-credit-card"></i> Simulate Payment
                        </button>
                        <a href="cart.php" class="btn btn-outline-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Cart
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="small text-muted">
                        <p class="mb-1"><i class="fa fa-lock"></i> Secure checkout</p>
                        <p class="mb-0"><i class="fa fa-shield"></i> Your information is safe</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/checkout.js"></script>
</body>
</html>

