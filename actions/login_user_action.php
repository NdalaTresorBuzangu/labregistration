<?php
header('Content-Type: application/json');
session_start();

$response = array();

// ✅ Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// ✅ Call controller
$user = login_customer_ctr($email, $password);

if ($user) {
    // set session
    $_SESSION['user_id']   = $user['customer_id'];
    $_SESSION['name']      = $user['customer_name'];
    $_SESSION['email']     = $user['customer_email'];
    $_SESSION['role']      = $user['user_role'];

    $response['status']   = 'success';
    $response['message']  = 'Login successful';
    $response['redirect'] = 'index.php';
} else {
    $response['status']  = 'error';
    $response['message'] = 'Invalid email or password';
}

echo json_encode($response);
