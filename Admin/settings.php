<?php
session_start();
require_once '../Backend/Config.php';

//check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

//fetch admin username
$username = $_SESSION['username'];

//check for logout confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Destroy the session and redirect to login page
    session_destroy();
    header('Location: Admin_login.php');
    exit();
}

?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admin Settings</title>

    <link rel="icon" href="../Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="./Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#3B82F6",
                        "background-light": "#F3F4F6",
                        "background-dark": "#0f1923",
                        "success": "#10B981"
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.5rem",
                        "lg": "0.75rem",
                        "xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#374151] dark:text-gray-300">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root">
        <div class="flex min-h-screen">
            <?php include 'Admin_nav.php' ?>
        </div>
    </div>

    <?php include 'Nav_script.php';?>
</body>