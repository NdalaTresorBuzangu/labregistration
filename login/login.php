<?php
session_start();

require_once __DIR__ . '/../settings/connection.php';
require_once __DIR__ . '/../controllers/user_controller.php';

// Handle AJAX login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    header("Content-Type: application/json");

    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and password are required."]);
        exit;
    }

    $customer = login_customer_ctr($email, $password);

    if ($customer) {
        $_SESSION["user_id"] = $customer["customer_id"];
        $_SESSION["name"]    = $customer["customer_name"];
        $_SESSION["role"]    = (int)$customer["user_role"];

        // Transfer guest cart to logged-in user
        require_once __DIR__ . '/../controllers/cart_controller.php';
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        transfer_guest_cart_ctr($customer["customer_id"], $ipAddress);

        // Determine role name and redirect
        switch ($_SESSION["role"]) {
            case 2:
                $role_name = "Owner";
                $redirect  = "../admin/category.php"; // ✅ correct path for Owner
                break;
            case 1:
                $role_name = "Customer";
                $redirect  = "../index.php";
                break;
            default:
                echo json_encode([
                    "success" => false,
                    "message" => "Invalid role in database. Allowed roles: 1 (Customer) or 2 (Owner). Got: " . $_SESSION["role"]
                ]);
                exit;
        }

        echo json_encode([
            "success"  => true,
            "message"  => "Welcome " . $customer["customer_name"] . " ($role_name)",
            "role"     => $_SESSION["role"],
            "redirect" => $redirect
        ]);
        exit;

    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password."
        ]);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Taste of Africa</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/app.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="app-shell d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="app-card">
                    <div class="text-center mb-4">
                        <span class="badge-gradient">Welcome back</span>
                        <h2 class="mt-3 mb-1">Sign in to your workspace</h2>
                        <p class="text-muted mb-0">Access your marketplace analytics and management tools.</p>
                    </div>
                    <form method="POST" id="login-form">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label mb-0">Password</label>
                                <a href="#" class="small text-muted">Forgot password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn app-button-primary w-100">Sign in</button>
                    </form>
                    <div class="text-center mt-4">
                        <span class="text-muted">New here?</span>
                        <a href="register.php" class="ms-2">Create an account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function(){
    $("#login-form").on("submit", function(e){
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "", // same page
            data: $(this).serialize(),
            dataType: "json",
            success: function(response){
                if(response.success){
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        html: response.message,
                        timer: 1500,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    }).then(()=>{
                        if(response.redirect){
                            window.location.href = response.redirect;
                        } else if(response.role){
                            if(response.role==2){ window.location.href="../admin/category.php"; } // ✅ correct path
                            else { window.location.href="../index.php"; }
                        }
                    });
                } else {
                    Swal.fire({ icon:"error", title:"Login Failed", text: response.message });
                }
            },
            error: function(xhr,status,error){
                Swal.fire({ icon:"error", title:"Oops...", text:"Something went wrong. Try again." });
            }
        });
    });
});
</script>
</body>
</html>


