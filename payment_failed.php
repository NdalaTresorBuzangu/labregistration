<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get error message from URL or use default
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : 'The payment could not be processed.';

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
    <title>Payment Failed · Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
</head>
<body>
<div class="app-shell d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="app-card text-center">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10" style="width: 100px; height: 100px; margin-bottom: 1.5rem;">
                            <i class="fa fa-times-circle fa-4x text-danger"></i>
                        </div>
                        <span class="badge bg-danger">Payment Failed</span>
                        <h2 class="mt-3 mb-1">Payment Unsuccessful</h2>
                        <p class="text-muted mb-0">We were unable to process your payment at this time.</p>
                    </div>

                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>Error:</strong> <?php echo $errorMessage; ?>
                    </div>

                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">What happened?</h5>
                            <p class="text-muted mb-0">
                                The simulated payment process encountered an error. Your cart items have been preserved, 
                                so you can try again or contact support if the problem persists.
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> This was a simulated payment for demonstration purposes. 
                        In a real application, you would be redirected back to the checkout page to try again.
                    </div>

                    <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                        <a href="checkout.php" class="btn app-button-primary">
                            <i class="fa fa-credit-card"></i> Try Payment Again
                        </a>
                        <a href="cart.php" class="btn btn-outline-secondary">
                            <i class="fa fa-shopping-cart"></i> Back to Cart
                        </a>
                        <a href="all_product.php" class="btn btn-outline-secondary">
                            <i class="fa fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

