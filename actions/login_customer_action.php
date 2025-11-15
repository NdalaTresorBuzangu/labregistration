<?php
session_start();
require_once '../controllers/user_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit;
    }

    $result = login_customer_ctr($email, $password);

    if ($result) {
        // Set session variables
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['role'] = $result['role'];
        $_SESSION['name'] = $result['name'];
        $_SESSION['email'] = $result['email'];

        echo json_encode(["success" => true, "message" => "Login successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }
}
