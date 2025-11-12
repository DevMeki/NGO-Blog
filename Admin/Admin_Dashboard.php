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
    <title>Admin Dashboard</title>

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
            <div
                class="flex flex-col gap-4 p-4 bg-white dark:bg-[#1a2633] w-64 border-r border-gray-200 dark:border-gray-700 fixed h-full">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
                        style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCg1Z53prw3r7jvHrQ3BWX9HVYQGDUgb7H78xWoCUM5rEvg1XZKPtvuf3Nq6KnY61EdHLyf-t-DGB5dhOXc_JwnoKAOtBkCgoVcuDQv-_IVN_ZNLrv7xbvf2kYv_XC854fjuW-FhKCmKRXHr_PtkDatlihusYBLIfPtnjyY26cUn-lSruOeCWXggGBz0ORQ4lbIYFAwzp6nGAg1I2wG21Ir6N5WaH3jOI_m04KZfWDwWReWdV-I-qiqUZX4WQhUOWwEJLsRq2ll7fkH");'>
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-[#111418] dark:text-white text-base font-medium leading-normal">
                            <?php
                            echo htmlspecialchars($username);
                            ?>
                        </h1>
                        <!-- <p class="text-[#5f758c] dark:text-gray-400 text-sm font-normal leading-normal">
                            admin@example.com</p> -->
                    </div>
                </div>
                <div class="flex flex-col gap-2 mt-4">
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg bg-primary/10 text-primary dark:bg-primary/20"
                        href="#">
                        <span class="material-symbols-outlined">dashboard</span>
                        <p class="text-sm font-medium leading-normal">Dashboard</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                        </svg>

                        <p class="text-sm font-medium leading-normal">Posts</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                        <!-- <i class="bi bi-chat-left-text"></i> -->

                        <p class="text-sm font-medium leading-normal">Comments</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                        href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>


                        <p class="text-sm font-medium leading-normal">Settings</p>
                    </a>
                    <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                        href="?confirm=yes">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                            <path fill-rule="evenodd"
                                d="M7.5 3.75A1.5 1.5 0 0 0 6 5.25v13.5a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5V15a.75.75 0 0 1 1.5 0v3.75a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V5.25a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3V9A.75.75 0 0 1 15 9V5.25a1.5 1.5 0 0 0-1.5-1.5h-6Zm5.03 4.72a.75.75 0 0 1 0 1.06l-1.72 1.72h10.94a.75.75 0 0 1 0 1.5H10.81l1.72 1.72a.75.75 0 1 1-1.06 1.06l-3-3a.75.75 0 0 1 0-1.06l3-3a.75.75 0 0 1 1.06 0Z"
                                clip-rule="evenodd" />
                        </svg>

                        <p class="text-sm font-medium leading-normal">Log out</p>
                    </a>
                </div>
                <div class="mt-auto">
                    <a href="Admin_post_Editor.php">
                        <button
                            class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90">
                            <span class="truncate">New Post</span>
                        </button>
                    </a>
                </div>
            </div>
            <main class="flex-1 ml-64 p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
                        <div class="flex flex-col gap-1">
                            <h1
                                class="text-3xl font-black leading-tight tracking-[-0.033em] text-[#111418] dark:text-white">
                                Welcome back, <?php
                                echo htmlspecialchars($username);
                                ?> !
                            </h1>
                            <p class="text-[#5f758c] dark:text-gray-400 text-base font-normal leading-normal">Here's a
                                quick overview of your website's activity.</p>
                        </div>
                        <div class="relative">
                            <button class="flex items-center gap-2">
                                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCg1Z53prw3r7jvHrQ3BWX9HVYQGDUgb7H78xWoCUM5rEvg1XZKPtvuf3Nq6KnY61EdHLyf-t-DGB5dhOXc_JwnoKAOtBkCgoVcuDQv-_IVN_ZNLrv7xbvf2kYv_XC854fjuW-FhKCmKRXHr_PtkDatlihusYBLIfPtnjyY26cUn-lSruOeCWXggGBz0ORQ4lbIYFAwzp6nGAg1I2wG21Ir6N5WaH3jOI_m04KZfWDwWReWdV-I-qiqUZX4WQhUOWwEJLsRq2ll7fkH");'>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="flex flex-col w-full">
                            <div class="flex w-full flex-1 items-stretch rounded-lg h-12">
                                <div
                                    class="text-[#5f758c] dark:text-gray-400 flex bg-white dark:bg-[#1a2633] items-center justify-center pl-4 rounded-l-lg border border-gray-200 dark:border-gray-700 border-r-0">
                                    <span class="material-symbols-outlined">search</span>
                                </div>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-r-lg text-[#111418] dark:text-white focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-200 dark:border-gray-700 bg-white dark:bg-[#1a2633] h-full placeholder:text-[#5f758c] dark:placeholder:text-gray-400 px-4 text-base font-normal leading-normal"
                                    placeholder="Search for posts or comments" value="" />
                            </div>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                        <div
                            class="flex flex-col gap-2 rounded-lg p-6 bg-white dark:bg-[#1a2633] border border-gray-200 dark:border-gray-700">
                            <p class="text-[#111418] dark:text-white text-base font-medium leading-normal">Total Posts
                            </p>
                            <p class="text-[#111418] dark:text-white tracking-light text-4xl font-bold leading-tight">
                                1,234</p>
                            <p class="text-success text-base font-medium leading-normal">+5% from last month</p>
                        </div>
                        <div
                            class="flex flex-col gap-2 rounded-lg p-6 bg-white dark:bg-[#1a2633] border border-gray-200 dark:border-gray-700">
                            <p class="text-[#111418] dark:text-white text-base font-medium leading-normal">Total
                                Comments</p>
                            <p class="text-[#111418] dark:text-white tracking-light text-4xl font-bold leading-tight">
                                5,678</p>
                            <p class="text-success text-base font-medium leading-normal">+10% from last month</p>
                        </div>
                        <div
                            class="flex flex-col gap-2 rounded-lg p-6 bg-white dark:bg-[#1a2633] border border-gray-200 dark:border-gray-700">
                            <p class="text-[#111418] dark:text-white text-base font-medium leading-normal">Site Visits
                            </p>
                            <p class="text-[#111418] dark:text-white tracking-light text-4xl font-bold leading-tight">
                                12,345</p>
                            <p class="text-success text-base font-medium leading-normal">+15% from last month</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                        <div
                            class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-[#1a2633]">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">article</span>
                                <h3 class="text-[#111418] dark:text-white text-lg font-medium leading-normal">Manage
                                    Posts</h3>
                            </div>
                            <p class="text-[#5f758c] dark:text-gray-400 text-sm">Create, edit, or delete blog posts and
                                manage categories.</p>
                            <div class="flex flex-col sm:flex-row gap-3 mt-2">
                                <button
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90">
                                    <span class="material-symbols-outlined">add</span>
                                    <span>New Post</span>
                                </button>
                                <button
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-[#111418] dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600">
                                    <span class="material-symbols-outlined">edit_document</span>
                                    <span>All Posts</span>
                                </button>
                            </div>
                        </div>
                        <!-- <div
                            class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-[#1a2633]">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">settings</span>
                                <h3 class="text-[#111418] dark:text-white text-lg font-medium leading-normal">Website
                                    Settings</h3>
                            </div>
                            <p class="text-[#5f758c] dark:text-gray-400 text-sm">Configure general site settings,
                                customize the theme, and manage integrations.</p>
                            <div class="flex flex-col sm:flex-row gap-3 mt-2">
                                <button
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-[#111418] dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600">
                                    <span class="material-symbols-outlined">tune</span>
                                    <span>General</span>
                                </button>
                                <button
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 text-[#111418] dark:text-white text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600">
                                    <span class="material-symbols-outlined">palette</span>
                                    <span>Theme</span>
                                </button>
                            </div>
                        </div> -->
                        <div
                            class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-[#1a2633]">
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-primary">link</span>
                                <h3 class="text-[#111418] dark:text-white text-lg font-medium leading-normal">Quick
                                    Links</h3>
                            </div>
                            <div class="flex flex-col gap-3">
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                    href="#">
                                    <span class="material-symbols-outlined text-primary">article</span>
                                    <p class="text-sm font-medium leading-normal">Create New Post</p>
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                    href="#">
                                    <span class="material-symbols-outlined text-primary">chat</span>
                                    <p class="text-sm font-medium leading-normal">Moderate Comments</p>
                                </a>
                                <a class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                    href="#">
                                    <span class="material-symbols-outlined text-primary">analytics</span>
                                    <p class="text-sm font-medium leading-normal">View Analytics</p>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        <div
                            class="xl:col-span-2 flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-[#1a2633]">
                            <div class="flex flex-col">
                                <p class="text-[#111418] dark:text-white text-lg font-medium leading-normal">Website
                                    Visits - Last 30 Days</p>
                                <p
                                    class="text-[#111418] dark:text-white tracking-light text-4xl font-bold leading-tight truncate">
                                    12,345</p>
                                <div class="flex gap-1">
                                    <p class="text-[#5f758c] dark:text-gray-400 text-base font-normal leading-normal">
                                        Last 30 Days</p>
                                    <p class="text-success text-base font-medium leading-normal">+15%</p>
                                </div>
                            </div>
                            <div class="flex min-h-[250px] flex-1 flex-col gap-8 py-4">
                                <svg fill="none" height="100%" preserveAspectRatio="none" viewBox="0 0 500 200"
                                    width="100%" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M0 150 C 41.66 150, 41.66 50, 83.33 50 C 125 50, 125 100, 166.67 100 C 208.33 100, 208.33 20, 250 20 C 291.67 20, 291.67 120, 333.33 120 C 375 120, 375 80, 416.67 80 C 458.33 80, 458.33 180, 500 180 V 200 H 0 Z"
                                        fill="url(#chartGradient)"></path>
                                    <path
                                        d="M0 150 C 41.66 150, 41.66 50, 83.33 50 C 125 50, 125 100, 166.67 100 C 208.33 100, 208.33 20, 250 20 C 291.67 20, 291.67 120, 333.33 120 C 375 120, 375 80, 416.67 80 C 458.33 80, 458.33 180, 500 180"
                                        stroke="#3B82F6" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="3"></path>
                                    <defs>
                                        <linearGradient gradientUnits="userSpaceOnUse" id="chartGradient" x1="250"
                                            x2="250" y1="20" y2="200">
                                            <stop stop-color="#3B82F6" stop-opacity="0.2"></stop>
                                            <stop offset="1" stop-color="#3B82F6" stop-opacity="0"></stop>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <div class="flex justify-between text-sm text-[#5f758c] dark:text-gray-400 font-medium">
                                    <span>Week 1</span>
                                    <span>Week 2</span>
                                    <span>Week 3</span>
                                    <span>Week 4</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-6">
                            <div
                                class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-[#1a2633]">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-[#111418] dark:text-white font-medium">Recent Posts</h3>
                                    <a class="text-sm text-primary hover:underline" href="#">View All</a>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <p class="font-medium text-[#111418] dark:text-white truncate">The Future of Web
                                            Design</p>
                                        <p class="text-sm text-[#5f758c] dark:text-gray-400">by Jane Doe - 2 days ago
                                        </p>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="font-medium text-[#111418] dark:text-white truncate">10 Tips for
                                            Better Tailwind CSS</p>
                                        <p class="text-sm text-[#5f758c] dark:text-gray-400">by John Smith - 5 days ago
                                        </p>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="font-medium text-[#111418] dark:text-white truncate">A Deep Dive into
                                            Modern JavaScript</p>
                                        <p class="text-sm text-[#5f758c] dark:text-gray-400">by Emily White - 1 week ago
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-[#1a2633]">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-[#111418] dark:text-white font-medium">Recent Comments</h3>
                                    <a class="text-sm text-primary hover:underline" href="#">View All</a>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-col">
                                        <p class="text-sm text-[#5f758c] dark:text-gray-400"><span
                                                class="font-medium text-[#111418] dark:text-white">Alex Johnson</span>
                                            on <span class="text-primary">The Future of Web Design</span></p>
                                        <p class="text-sm text-[#374151] dark:text-gray-300 truncate mt-1">"This is a
                                            fantastic read! So insightful."</p>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-sm text-[#5f758c] dark:text-gray-400"><span
                                                class="font-medium text-[#111418] dark:text-white">Maria Garcia</span>
                                            on <span class="text-primary">10 Tips for Better...</span></p>
                                        <p class="text-sm text-[#374151] dark:text-gray-300 truncate mt-1">"Great tips,
                                            I've already implemented some."</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>

</html>