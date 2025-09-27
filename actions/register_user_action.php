<?php
header('Content-Type: application/json');
session_start();

$response = array();

require_once '../controllers/user_controller.php';

// Collect POST data safely
$name         = isset($_POST['name']) ? trim($_POST['name']) : '';
$email        = isset($_POST['email']) ? trim($_POST['email']) : '';
$password     = isset($_POST['password']) ? $_POST['password'] : '';
$phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$role         = isset($_POST['role']) ? $_POST['role'] : '1'; // default: Customer

// Validate input
if (empty($name) || empty($email) || empty($password) || empty($phone_number)) {
    $response['status'] = 'error';
    $response['message'] = 'All fields are required!';
    echo json_encode($response);
    exit();
}

// Call controller function to register user
$user_id = register_user_ctr($name, $email, $password, $phone_number, $role);

if ($user_id) {
    $response['status']  = 'success';
    $response['message'] = 'Registered successfully. Please log in.';
    $response['user_id'] = $user_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to register. Try again later.';
}

echo json_encode($response);
