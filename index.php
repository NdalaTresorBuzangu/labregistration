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
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/index.css">
</head>
<body>

    <div class="menu-tray d-flex justify-content-end p-2">
        <span class="me-2 fw-bold">Menu:</span>

        <?php
        $isLoggedIn = isset($_SESSION['user_id']);
        $isAdmin = isset($_SESSION['role']) && (int)$_SESSION['role'] === 2;
        ?>

        <?php if (!$isLoggedIn): ?>
            <!-- Not logged in -->
            <a href="login/register.php" class="btn btn-sm btn-outline-primary me-1">Register</a>
            <a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>

        <?php else: ?>
            <!-- Logged in -->
            <a href="logout.php" class="btn btn-sm btn-outline-danger me-1">Logout</a>

            <?php if ($isAdmin): ?>
                <a href="admin/category.php" class="btn btn-sm btn-outline-success me-1">Category</a>
                <a href="admin/brand.php" class="btn btn-sm btn-outline-success me-1">Brand</a>
                <a href="admin/product.php" class="btn btn-sm btn-outline-success">Add Product</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="container main-content">
        <div class="text-center mt-5">
            <h1>Welcome</h1>
            <?php if ($isLoggedIn): ?>
                <p class="text-muted">Hello, <?php echo htmlspecialchars(getCustomerName()); ?>!</p>
            <?php else: ?>
                <p class="text-muted">Use the menu in the top-right to Register or Login.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

