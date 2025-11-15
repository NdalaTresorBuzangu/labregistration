<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get order details from URL parameters or session
$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$invoiceNo = isset($_GET['invoice_no']) ? htmlspecialchars($_GET['invoice_no']) : '';
$totalAmount = isset($_GET['total']) ? (float)$_GET['total'] : 0;
$currency = isset($_GET['currency']) ? htmlspecialchars($_GET['currency']) : 'USD';
$itemsCount = isset($_GET['items']) ? (int)$_GET['items'] : 0;

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
    <title>Payment Success · Taste of Africa</title>
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
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width: 100px; height: 100px; margin-bottom: 1.5rem;">
                            <i class="fa fa-check-circle fa-4x text-success"></i>
                        </div>
                        <span class="badge-gradient">Payment Successful</span>
                        <h2 class="mt-3 mb-1">Thank You for Your Purchase!</h2>
                        <p class="text-muted mb-0">Your order has been confirmed and payment has been processed successfully.</p>
                    </div>

                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Order Summary</h5>
                            
                            <?php if ($orderId > 0): ?>
                                <div class="row text-start mb-3">
                                    <div class="col-sm-4"><strong>Order ID:</strong></div>
                                    <div class="col-sm-8">#<?php echo $orderId; ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($invoiceNo): ?>
                                <div class="row text-start mb-3">
                                    <div class="col-sm-4"><strong>Invoice Number:</strong></div>
                                    <div class="col-sm-8"><?php echo $invoiceNo; ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($itemsCount > 0): ?>
                                <div class="row text-start mb-3">
                                    <div class="col-sm-4"><strong>Items:</strong></div>
                                    <div class="col-sm-8"><?php echo $itemsCount; ?> item(s)</div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($totalAmount > 0): ?>
                                <div class="row text-start mb-3">
                                    <div class="col-sm-4"><strong>Total Amount:</strong></div>
                                    <div class="col-sm-8"><h5 class="mb-0 text-success"><?php echo $currency; ?> <?php echo number_format($totalAmount, 2); ?></h5></div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row text-start">
                                <div class="col-sm-4"><strong>Payment Status:</strong></div>
                                <div class="col-sm-8"><span class="badge bg-success">Paid</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        <strong>Note:</strong> This was a simulated payment for demonstration purposes. In a real application, you would receive an email confirmation with your order details.
                    </div>

                    <div class="d-flex flex-wrap gap-3 justify-content-center mt-4">
                        <a href="all_product.php" class="btn app-button-primary">
                            <i class="fa fa-shopping-bag"></i> Continue Shopping
                        </a>
                        <?php if ($orderId > 0): ?>
                            <a href="orders.php?order_id=<?php echo $orderId; ?>" class="btn btn-outline-secondary">
                                <i class="fa fa-receipt"></i> View Order Details
                            </a>
                        <?php endif; ?>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fa fa-home"></i> Back to Home
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

