<?php
session_start();

// Optional: Helper function to safely get the customer's name
function getCustomerName() {
    return isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : 'User';
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

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Not logged in -->
            <a href="login/register.php" class="btn btn-sm btn-outline-primary me-1">Register</a>
            <a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>

        <?php else: ?>
            <!-- Logged in -->
            <a href="logout.php" class="btn btn-sm btn-outline-danger me-1">Logout</a>

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin/category.php" class="btn btn-sm btn-outline-success">Category</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="container main-content">
        <div class="text-center mt-5">
            <h1>Welcome</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p class="text-muted">Hello, <?php echo htmlspecialchars(getCustomerName()); ?>!</p>
            <?php else: ?>
                <p class="text-muted">Use the menu in the top-right to Register or Login.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

</body>
</html>
