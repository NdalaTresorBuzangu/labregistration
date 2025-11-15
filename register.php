<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account · Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/app.css">
</head>
<body>
    <div class="app-shell d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7">
                    <div class="app-card">
                        <div class="mb-4 text-center">
                            <span class="badge-gradient">Create account</span>
                            <h2 class="mt-3 mb-1">Join Taste of Africa</h2>
                            <p class="text-muted mb-0">Register as a customer or owner to start managing your marketplace.</p>
                        </div>
                        <form method="POST" action="" id="register-form">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Full name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ama K. Mensah" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Phone number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="(+233) 555 555 555" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a secure password" required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <span class="form-label d-block mb-2">Register as</span>
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="customer" value="1" checked>
                                        <label class="form-check-label" for="customer">
                                            <i class="fa-solid fa-user me-2 text-muted"></i>Customer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="owner" value="2">
                                        <label class="form-check-label" for="owner">
                                            <i class="fa-solid fa-store me-2 text-muted"></i>Store owner
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn app-button-primary w-100 mt-4">Create account</button>
                        </form>
                        <div class="text-center mt-4">
                            <span class="text-muted">Already have an account?</span>
                            <a href="login.php" class="ms-2">Sign in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/register.js"></script>
</body>
</html>
