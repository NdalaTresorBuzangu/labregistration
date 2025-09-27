<?php
include "../settings/connection.php";
include "../controllers/user_controller.php";
session_start();

// Always return JSON
header("Content-Type: application/json");

$email    = isset($_POST["email"]) ? trim($_POST["email"]) : '';
$password = isset($_POST["password"]) ? $_POST["password"] : '';

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
            $redirect  = "../category.php";
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

    // Send JSON response
    echo json_encode([
        "success"  => true,
        "message"  => "Welcome " . $customer["customer_name"] . " ($role_name)",
        "role"     => $_SESSION["role"],
        "redirect" => $redirect
    ]);

} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
}
