<?php
session_start();
include "../settings/connection.php";
include "../controllers/user_controller.php";

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
    <title>Login - Taste of Africa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .btn-custom { background-color:#D19C97; border-color:#D19C97; color:#fff; transition:0.3s; }
        .btn-custom:hover { background-color:#b77a7a; border-color:#b77a7a; }
        .highlight { color:#D19C97; }
        body { background-color:#f8f9fa; min-height:100vh; font-family:Arial,sans-serif; }
        .login-container { margin-top:100px; }
        .card { border:none; border-radius:15px; box-shadow:0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background-color:#D19C97; color:#fff; }
        .animate-pulse-custom { animation: pulse 2s infinite; }
        @keyframes pulse { 0%{transform:scale(1);}50%{transform:scale(1.05);}100%{transform:scale(1);} }
    </style>
</head>
<body>
<div class="container login-container">
    <div class="row justify-content-center animate__animated animate__fadeInDown">
        <div class="col-md-6">
            <div class="card animate__animated animate__zoomIn">
                <div class="card-header text-center highlight">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <form method="POST" id="login-form" class="mt-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <i class="fa fa-envelope"></i></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">Password <i class="fa fa-lock"></i></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100 animate-pulse-custom">Login</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Don't have an account? <a href="register.php" class="highlight">Register here</a>.
                </div>
            </div>
        </div>
    </div>
</div>

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


