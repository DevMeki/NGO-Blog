<?php
session_start();
include '/Config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful. You can now log in.";
        header("location: .Admin_login.php");
        exit();
    } else {
        $error = "username already exist!";
    }
}