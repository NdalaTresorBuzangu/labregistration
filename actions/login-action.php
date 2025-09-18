<?php
header('Content-Type: application/json');
session_start();

require_once "../controllers/user_controller.php";

// Get form inputs
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

// Get user by email
$user = get_user_by_email_ctr($email);

if ($user) {
    // Verify hashed password
    if (password_verify($password, $user['teacherPwd'])) {
        // Store session data
        $_SESSION['user_id'] = $user['teacherID'];
        $_SESSION['name'] = $user['teacherName'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => '../index.php'   // ✅ redirect to index.php
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Account not found']);
}

