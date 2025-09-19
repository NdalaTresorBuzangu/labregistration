<?php
include "../settings/connection.php"; // ✅ connection file
session_start();

$email = $password = "";

// Collect and sanitize input
$email = mysqli_real_escape_string($con, $_POST["email"]);
$password = mysqli_real_escape_string($con, $_POST["password"]);

// Query the customer table
$login_query = "SELECT * FROM `customer` WHERE `customer_email` = ?";
$query = $con->prepare($login_query);

if ($query) {
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify hashed password
        if (!password_verify($password, $row["customer_pass"])) {
            echo json_encode([
                'success' => false,
                'message' => 'Incorrect email or password'
            ]);
        } else {
            // Store session values
            $_SESSION["user_id"]  = $row["customer_id"];
            $_SESSION["name"]     = $row["customer_name"];
            $_SESSION["role"]     = $row["user_role"];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful! Welcome, ' . $row["customer_name"]
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'This account is not registered'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Query preparation failed'
    ]);
}
?>
