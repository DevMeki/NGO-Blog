<?php
require_once '../Backend/Config.php';
session_start();
//check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

//fetch admin username
$username = $_SESSION['username'];

header('Content-Type: application/json');

// The key is now checked for 'generate_token' (lowercase)
if (isset($_POST['generate_token'])) {
    $token = uniqid() . bin2hex(random_bytes(1));

    echo json_encode([
        'success' => true,
        'token' => $token,
    ]);

    if (isset($conn)) {
        $created_by = $username;
        $stmt = $conn->prepare("INSERT INTO tokens (token, created_at, created_by) VALUES (?, NOW(), ?)");
        $stmt->bind_param("ss", $token, $created_by);
        $stmt->execute();
        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database connection not available.',
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid request: POST parameter "generate_token" not set.',
    ]);
}

?>