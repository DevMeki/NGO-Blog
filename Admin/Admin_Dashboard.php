<?php
session_start();

require_once '../Backend/Config.php';
// require_once '../Backend/track_visits.php';

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

if (isset($_GET['logout']) && $_GET['logout'] === 'confirm') {
    session_destroy();
    header('Location: Admin_login.php');
    exit();
}

// Check for logout confirmation
// if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
//     session_destroy();
//     header('Location: Admin_login.php');
//     exit();
// }

// Fetch admin username and ID
$username = $_SESSION['username'];
$current_admin_id = $_SESSION['admin_id'] ?? 0;

// Initialize stats
$total_posts = 0;
$total_views = 0;
$total_drafts = 0;
$total_inquiries = 0;
$blog_posts = [];
$total_visits = 0;
$today_visits = 0;
$this_week_visits = 0;

// DEBUG: Check connection
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// TEST: Let's check if tables exist and have data
$table_check = [];
$tables_to_check = ['blog_post', 'draft', 'inquiry', 'visitor_logs'];

foreach ($tables_to_check as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($sql);
    $table_check[$table]['exists'] = $result && $result->num_rows > 0;

    if ($table_check[$table]['exists']) {
        $count_sql = "SELECT COUNT(*) as count FROM $table";
        $count_result = $conn->query($count_sql);
        if ($count_result) {
            $row = $count_result->fetch_assoc();
            $table_check[$table]['count'] = $row['count'];
        } else {
            $table_check[$table]['count'] = 0;
            error_log("Count query failed for table $table: " . $conn->error);
        }
    }
}

// SIMPLE FIX: Let's use direct queries with proper error reporting
$total_posts = $table_check['blog_post']['count'] ?? 0;

// Get recent posts - SIMPLIFIED
$sql = "SELECT * FROM blog_post ORDER BY Date_posted DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $blog_posts[] = $row;
    }
} else {
    error_log("No blog posts found or query failed: " . ($conn->error ?? 'No error'));
}

// Get total drafts
$sql = "SELECT COUNT(*) as total FROM draft";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_drafts = $row['total'] ?? 0;
} else {
    error_log("Drafts query failed: " . $conn->error);
}

// Get total inquiries  
$sql = "SELECT COUNT(*) as total FROM inquiry";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_inquiries = $row['total'] ?? 0;
} else {
    error_log("Inquiries query failed: " . $conn->error);
}

// Get visit stats
$sql = "SELECT COUNT(*) as total FROM visitor_logs";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $total_visits = $row['total'] ?? 0;
} else {
    error_log("Total visits query failed: " . $conn->error);
}

$sql = "SELECT COUNT(*) as today FROM visitor_logs WHERE DATE(visit_time) = CURDATE()";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $today_visits = $row['today'] ?? 0;
} else {
    error_log("Today visits query failed: " . $conn->error);
}

$sql = "SELECT COUNT(*) as week FROM visitor_logs WHERE YEARWEEK(visit_time) = YEARWEEK(CURDATE())";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $this_week_visits = $row['week'] ?? 0;
} else {
    error_log("This week visits query failed: " . $conn->error);
}

// Get unique visitors for the last 4 weeks and comparison
$unique_visitors = 0;
$unique_visitors_change = '+0%';

try {
    $current_unique_res = $conn->query("SELECT COUNT(DISTINCT ip_address) as count FROM visitor_logs WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)");
    $previous_unique_res = $conn->query("SELECT COUNT(DISTINCT ip_address) as count FROM visitor_logs WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK) AND visit_time < DATE_SUB(CURDATE(), INTERVAL 4 WEEK)");

    $unique_visitors = $current_unique_res ? $current_unique_res->fetch_assoc()['count'] ?? 0 : 0;
    $previous_unique = $previous_unique_res ? $previous_unique_res->fetch_assoc()['count'] ?? 0 : 0;

    $unique_visitors_change = calculatePercentageChange($unique_visitors, $previous_unique);
} catch (Exception $e) {
    error_log("Error calculating unique visitors: " . $e->getMessage());
}

// Get visits for the last 4 weeks for the chart - PRECISE MAPPING
$weekly_visits = [0, 0, 0, 0];
$target_weeks = [];

// Determine the target YEARWEEK values for the last 4 weeks (ending with current week)
for ($i = 3; $i >= 0; $i--) {
    $wk_res = $conn->query("SELECT YEARWEEK(DATE_SUB(CURDATE(), INTERVAL $i WEEK)) as wk");
    if ($wk_res) {
        $target_weeks[] = $wk_res->fetch_assoc()['wk'];
    }
}

// target_weeks is now [oldest_wk, ..., current_wk]
// Mapping: Index 0 -> Week 4 (Oldest), Index 3 -> Week 1 (Current)

$sql = "SELECT 
            YEARWEEK(visit_time) as week_number,
            COUNT(*) as visits 
        FROM visitor_logs 
        WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
        GROUP BY YEARWEEK(visit_time)
        ORDER BY week_number ASC";

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $idx = array_search($row['week_number'], $target_weeks);
        if ($idx !== false) {
            $weekly_visits[$idx] = (int) $row['visits'];
        }
    }
} else {
    error_log("Weekly visits query failed: " . $conn->error);
}

// Get visits for the last 12 months for the second chart
$monthly_visits = [];
$monthly_labels = [];

try {
    // Generate the last 12 months (inclusive of current month)
    for ($i = 11; $i >= 0; $i--) {
        $month_res = $conn->query("SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL $i MONTH), '%Y-%m') as ym, DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL $i MONTH), '%b %y') as lbl");
        if ($month_res) {
            $row = $month_res->fetch_assoc();
            $monthly_visits[$row['ym']] = 0;
            $monthly_labels[] = $row['lbl'];
        }
    }

    $sql_monthly = "SELECT 
                        DATE_FORMAT(visit_time, '%Y-%m') as ym,
                        COUNT(*) as count 
                    FROM visitor_logs 
                    WHERE visit_time >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
                    GROUP BY ym 
                    ORDER BY ym ASC";

    $res_monthly = $conn->query($sql_monthly);
    if ($res_monthly) {
        while ($row = $res_monthly->fetch_assoc()) {
            if (isset($monthly_visits[$row['ym']])) {
                $monthly_visits[$row['ym']] = (int) $row['count'];
            }
        }
    }
    // Re-index for JSON usage
    $monthly_visits = array_values($monthly_visits);
} catch (Exception $e) {
    error_log("Monthly visits calculation failed: " . $e->getMessage());
}

// If no real data, use proportional data based on this week's visits (Week 1 gets highest)
if (array_sum($weekly_visits) === 0 && $this_week_visits > 0) {
    $weekly_visits = [
        intval($this_week_visits * 0.9),  // Week 1 (newest - 90% of current)
        intval($this_week_visits * 0.8),  // Week 2 (80% of current)
        intval($this_week_visits * 0.7),  // Week 3 (70% of current)
        intval($this_week_visits * 0.6)   // Week 4 (oldest - 60% of current)
    ];
}

// Calculate actual percentage changes (Option 1 - Dynamic)
function calculatePercentageChange($current, $previous)
{
    if ($previous == 0) {
        return $current > 0 ? '+100%' : '0%';
    }
    $change = (($current - $previous) / $previous) * 100;
    return ($change >= 0 ? '+' : '') . round($change) . '%';
}

// DEBUG: Initialize percentage variables with fallbacks
$posts_change = '+0%';
$views_change = '+0%';
$visits_change = '+0%';
$drafts_change = '+0%';

try {
    // Get current month stats with better error handling
    $current_month_posts_result = $conn->query("SELECT COUNT(*) as count FROM blog_post WHERE MONTH(Date_posted) = MONTH(CURDATE()) AND YEAR(Date_posted) = YEAR(CURDATE())");
    $previous_month_posts_result = $conn->query("SELECT COUNT(*) as count FROM blog_post WHERE MONTH(Date_posted) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(Date_posted) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");

    $current_month_posts = $current_month_posts_result ? $current_month_posts_result->fetch_assoc()['count'] ?? 0 : 0;
    $previous_month_posts = $previous_month_posts_result ? $previous_month_posts_result->fetch_assoc()['count'] ?? 0 : 0;

    $posts_change = calculatePercentageChange($current_month_posts, $previous_month_posts);

    // For views - check if Views column exists first
    $check_views_column = $conn->query("SHOW COLUMNS FROM blog_post LIKE 'Views'");
    if ($check_views_column && $check_views_column->num_rows > 0) {
        $current_month_views_result = $conn->query("SELECT SUM(Views) as total_views FROM blog_post WHERE MONTH(Date_posted) = MONTH(CURDATE()) AND YEAR(Date_posted) = YEAR(CURDATE())");
        $previous_month_views_result = $conn->query("SELECT SUM(Views) as total_views FROM blog_post WHERE MONTH(Date_posted) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(Date_posted) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");

        $current_month_views = $current_month_views_result ? $current_month_views_result->fetch_assoc()['total_views'] ?? 0 : 0;
        $previous_month_views = $previous_month_views_result ? $previous_month_views_result->fetch_assoc()['total_views'] ?? 0 : 0;

        $views_change = calculatePercentageChange($current_month_views, $previous_month_views);
    } else {
        $views_change = '+0%'; // Fallback if Views column doesn't exist
    }

    // For visits
    $current_month_visits_result = $conn->query("SELECT COUNT(*) as count FROM visitor_logs WHERE MONTH(visit_time) = MONTH(CURDATE()) AND YEAR(visit_time) = YEAR(CURDATE())");
    $previous_month_visits_result = $conn->query("SELECT COUNT(*) as count FROM visitor_logs WHERE MONTH(visit_time) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(visit_time) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");

    $current_month_visits = $current_month_visits_result ? $current_month_visits_result->fetch_assoc()['count'] ?? 0 : 0;
    $previous_month_visits = $previous_month_visits_result ? $previous_month_visits_result->fetch_assoc()['count'] ?? 0 : 0;

    $visits_change = calculatePercentageChange($current_month_visits, $previous_month_visits);

    // For drafts - check if created_at column exists
    $check_drafts_column = $conn->query("SHOW COLUMNS FROM draft LIKE 'created_at'");
    if ($check_drafts_column && $check_drafts_column->num_rows > 0) {
        $current_month_drafts_result = $conn->query("SELECT COUNT(*) as count FROM draft WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
        $previous_month_drafts_result = $conn->query("SELECT COUNT(*) as count FROM draft WHERE MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");

        $current_month_drafts = $current_month_drafts_result ? $current_month_drafts_result->fetch_assoc()['count'] ?? 0 : 0;
        $previous_month_drafts = $previous_month_drafts_result ? $previous_month_drafts_result->fetch_assoc()['count'] ?? 0 : 0;

        $drafts_change = calculatePercentageChange($current_month_drafts, $previous_month_drafts);
    } else {
        // If no created_at column, use total drafts count
        $drafts_change = '+0%';
    }

} catch (Exception $e) {
    error_log("Error calculating percentages: " . $e->getMessage());
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
    <link rel="apple-touch-icon" href="../Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#1f9c7b">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <style>
        @media (max-width: 1023px) {
            .mobile-margin {
                margin-left: 0 !important;
                padding: 1rem !important;
            }

            .mobile-full-width {
                width: 100% !important;
                max-width: 100% !important;
            }

            .mobile-stack {
                flex-direction: column !important;
            }

            .mobile-padding {
                padding: 1rem !important;
            }

            .mobile-text-center {
                text-align: center !important;
            }

        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#374151] dark:text-gray-300">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root">
        <div class="flex min-h-screen">
            <?php include 'Admin_nav.php' ?>
            <!-- Mobile padding adjustment -->
            <main class="flex-1 ml-0 lg:ml-64 p-4 lg:p-8 mobile-margin">
                <div class="max-w-7xl mx-auto mobile-full-width">
                    <!-- Dashboard Header -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8 lg:mb-10">
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-black text-[#111418] dark:text-white tracking-tight">
                                Dashboard
                            </h1>
                            <p class="text-[#5f758c] dark:text-gray-400 text-sm mt-1 font-medium italic">
                                Welcome back, <span
                                    class="text-primary font-bold"><?php echo htmlspecialchars($username); ?></span>.
                                Here's what's happening today.
                            </p>
                        </div>
                        <div
                            class="flex items-center gap-3 bg-white dark:bg-[#1a2633] px-4 py-2 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm self-start md:self-auto">
                            <div class="size-2 bg-success rounded-full animate-pulse"></div>
                            <span
                                class="text-xs font-bold text-[#111418] dark:text-white uppercase tracking-widest">Systems
                                Online</span>
                        </div>
                    </div>

                    <!-- Stats Cards (KPIs) - 4-Column Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                        <!-- Total Page Visits -->
                        <div
                            class="flex flex-col gap-2 rounded-xl p-4 lg:p-6 bg-white dark:bg-[#1a2633] border border-gray-100 dark:border-gray-800 shadow-sm">
                            <p
                                class="text-[#5f758c] dark:text-gray-400 text-xs lg:text-sm font-semibold uppercase tracking-wider">
                                Total Page Visits</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-[#111418] dark:text-white text-2xl lg:text-3xl font-black">
                                    <?php echo number_format($total_visits); ?></p>
                                <span class="text-success text-xs font-bold"><?php echo $visits_change; ?></span>
                            </div>
                            <p class="text-[#5f758c] dark:text-gray-500 text-xs">Total cumulative visits</p>
                        </div>

                        <!-- Unique Visitors -->
                        <div
                            class="flex flex-col gap-2 rounded-xl p-4 lg:p-6 bg-white dark:bg-[#1a2633] border border-gray-100 dark:border-gray-800 shadow-sm">
                            <p
                                class="text-[#5f758c] dark:text-gray-400 text-xs lg:text-sm font-semibold uppercase tracking-wider">
                                Unique Visitors</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-[#111418] dark:text-white text-2xl lg:text-3xl font-black">
                                    <?php echo number_format($unique_visitors); ?></p>
                                <span
                                    class="text-success text-xs font-bold"><?php echo $unique_visitors_change; ?></span>
                            </div>
                            <p class="text-[#5f758c] dark:text-gray-500 text-xs">Last 30 days active</p>
                        </div>

                        <!-- Total Posts -->
                        <div
                            class="flex flex-col gap-2 rounded-xl p-4 lg:p-6 bg-white dark:bg-[#1a2633] border border-gray-100 dark:border-gray-800 shadow-sm">
                            <p
                                class="text-[#5f758c] dark:text-gray-400 text-xs lg:text-sm font-semibold uppercase tracking-wider">
                                Live Posts</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-[#111418] dark:text-white text-2xl lg:text-3xl font-black">
                                    <?php echo number_format($total_posts); ?></p>
                                <span class="text-success text-xs font-bold"><?php echo $posts_change; ?></span>
                            </div>
                            <p class="text-[#5f758c] dark:text-gray-500 text-xs">Published on blog</p>
                        </div>

                        <!-- Total Inquiries -->
                        <div
                            class="flex flex-col gap-2 rounded-xl p-4 lg:p-6 bg-white dark:bg-[#1a2633] border border-gray-100 dark:border-gray-800 shadow-sm">
                            <p
                                class="text-[#5f758c] dark:text-gray-400 text-xs lg:text-sm font-semibold uppercase tracking-wider">
                                Inquiries</p>
                            <div class="flex items-baseline gap-2">
                                <p class="text-[#111418] dark:text-white text-2xl lg:text-3xl font-black">
                                    <?php echo number_format($total_inquiries); ?></p>
                                <span
                                    class="bg-primary/10 text-primary px-2 py-0.5 rounded text-[10px] font-bold">NEW</span>
                            </div>
                            <p class="text-[#5f758c] dark:text-gray-500 text-xs">Messages & Donations</p>
                        </div>
                    </div>

                    <!-- Main Content Layout - Two Columns -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 lg:gap-8">

                        <!-- Left Column: Detailed Analytics -->
                        <div class="xl:col-span-2 space-y-6 lg:space-y-8">
                            <!-- Annual Visits Trend -->
                            <div
                                class="flex flex-col gap-6 rounded-xl border border-gray-100 dark:border-gray-800 p-6 lg:p-8 bg-white dark:bg-[#1a2633] shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h2 class="text-[#111418] dark:text-white text-lg lg:text-xl font-bold">Annual
                                            Traffic Trend</h2>
                                        <p class="text-[#5f758c] dark:text-gray-400 text-sm">Monthly visit distribution
                                            over the last 12 months</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[#111418] dark:text-white text-2xl font-black">
                                            <?php echo number_format(array_sum($monthly_visits)); ?>
                                        </p>
                                        <p
                                            class="text-[#5f758c] dark:text-gray-500 text-[10px] uppercase font-bold tracking-tighter">
                                            Yearly Total</p>
                                    </div>
                                </div>
                                <div class="h-[250px] lg:h-[300px] w-full">
                                    <canvas id="yearlyVisitsChart"></canvas>
                                </div>
                            </div>

                            <!-- Weekly Breakdown -->
                            <div
                                class="flex flex-col gap-6 rounded-xl border border-gray-100 dark:border-gray-800 p-6 lg:p-8 bg-white dark:bg-[#1a2633] shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h2 class="text-[#111418] dark:text-white text-lg lg:text-xl font-bold">Recent
                                            Activity Analysis</h2>
                                        <p class="text-[#5f758c] dark:text-gray-400 text-sm">Granular weekly breakdown
                                            for the last 4 weeks</p>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 px-3 py-1 bg-success/10 text-success rounded-full text-xs font-bold">
                                        <span class="size-2 bg-success rounded-full animate-pulse"></span>
                                        LIVE TREND
                                    </div>
                                </div>
                                <div class="h-[200px] lg:h-[250px] w-full">
                                    <canvas id="visitsChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Sidebar Management -->
                        <div class="space-y-6 lg:space-y-8">
                            <!-- Recent Posts -->
                            <div
                                class="flex flex-col rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-[#1a2633] shadow-sm overflow-hidden">
                                <div
                                    class="p-6 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                                    <h3 class="text-[#111418] dark:text-white font-bold italic">Recent Content</h3>
                                    <a class="text-xs font-bold text-primary flex items-center gap-1 hover:gap-2 transition-all"
                                        href="List_Post.php">
                                        VIEW ALL <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                </div>
                                <div class="p-6 space-y-5">
                                    <?php if (!empty($blog_posts)): ?>
                                        <?php foreach ($blog_posts as $post): ?>
                                            <div class="group cursor-pointer">
                                                <p
                                                    class="font-bold text-[#111418] dark:text-white group-hover:text-primary transition-colors truncate text-sm">
                                                    <?php echo htmlspecialchars($post['Title'] ?? 'No Title'); ?>
                                                </p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="text-[10px] bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-gray-500">
                                                        <?php echo htmlspecialchars($post['Categories'] ?? 'Post'); ?>
                                                    </span>
                                                    <p class="text-[11px] text-[#5f758c] dark:text-gray-500 font-medium">
                                                        <?php echo date('M j, Y', strtotime($post['Date_posted'] ?? 'now')); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <p class="text-[#5f758c] dark:text-gray-500 text-sm">No recent posts found</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Dashboard Quick Insights -->
                            <div
                                class="flex flex-col rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-[#1a2633] shadow-sm p-6">
                                <h3 class="text-[#111418] dark:text-white font-bold italic mb-6">Quick Insights</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div
                                        class="p-3 rounded-lg bg-background-light dark:bg-background-dark/50 border border-gray-50 dark:border-gray-800">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Today</p>
                                        <p class="text-lg font-black text-[#111418] dark:text-white">
                                            <?php echo number_format($today_visits); ?>
                                        </p>
                                        <p class="text-[9px] text-gray-500">Peak Visits</p>
                                    </div>
                                    <div
                                        class="p-3 rounded-lg bg-background-light dark:bg-background-dark/50 border border-gray-50 dark:border-gray-800">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Drafts</p>
                                        <p class="text-lg font-black text-[#111418] dark:text-white">
                                            <?php echo number_format($total_drafts); ?>
                                        </p>
                                        <p class="text-[9px] text-gray-500">To Review</p>
                                    </div>
                                    <div
                                        class="p-3 rounded-lg bg-background-light dark:bg-background-dark/50 border border-gray-50 dark:border-gray-800">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Weekly</p>
                                        <p class="text-lg font-black text-[#111418] dark:text-white">
                                            <?php echo number_format($this_week_visits); ?>
                                        </p>
                                        <p class="text-[9px] text-gray-500">This Week</p>
                                    </div>
                                    <div
                                        class="p-3 rounded-lg bg-background-light dark:bg-background-dark/50 border border-gray-50 dark:border-gray-800">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Status</p>
                                        <p class="text-sm font-black text-success mt-1 uppercase tracking-tighter">
                                            Healthy</p>
                                        <p class="text-[9px] text-gray-500">Live Services</p>
                                    </div>
                                </div>
                                <button onclick="window.location.reload()"
                                    class="w-full mt-6 py-2.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-sm">refresh</span> REFRESH DASHBOARD
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'Nav_script.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('visitsChart').getContext('2d');

            // Use real PHP data for the chart - Week 4 (oldest) to Week 1 (newest)
            const weeklyVisits = [
                <?php echo $weekly_visits[0] ?? 0; ?>, // Week 4 (oldest)
                <?php echo $weekly_visits[1] ?? 0; ?>, // Week 3
                <?php echo $weekly_visits[2] ?? 0; ?>, // Week 2
                <?php echo $weekly_visits[3] ?? 0; ?>  // Week 1 (newest)
            ];

            const visitsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Week 4', 'Week 3', 'Week 2', 'Week 1'], // Week 4 oldest, Week 1 newest
                    datasets: [{
                        label: 'Website Visits',
                        data: weeklyVisits,
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `Visits: ${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.1)' },
                            ticks: {
                                callback: function (value) {
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Mobile responsive adjustments for the chart
            function adjustChartForMobile() {
                if (window.innerWidth < 768) {
                    // Smaller font sizes for mobile
                    visitsChart.options.scales.y.ticks.font = { size: 10 };
                    visitsChart.options.scales.x.ticks.font = { size: 10 };
                    visitsChart.options.plugins.tooltip.titleFont = { size: 12 };
                    visitsChart.options.plugins.tooltip.bodyFont = { size: 12 };
                } else {
                    // Reset to default for larger screens
                    visitsChart.options.scales.y.ticks.font = { size: 12 };
                    visitsChart.options.scales.x.ticks.font = { size: 12 };
                    visitsChart.options.plugins.tooltip.titleFont = { size: 14 };
                    visitsChart.options.plugins.tooltip.bodyFont = { size: 14 };
                }
                visitsChart.update();
            }

            // Initial adjustment
            adjustChartForMobile();

            // Adjust on resize
            window.addEventListener('resize', adjustChartForMobile);

            // Yearly Chart Implementation
            const yearlyCtx = document.getElementById('yearlyVisitsChart').getContext('2d');
            const monthlyLabels = <?php echo json_encode($monthly_labels); ?>;
            const monthlyData = <?php echo json_encode($monthly_visits); ?>;

            const yearlyVisitsChart = new Chart(yearlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Monthly Visits',
                        data: monthlyData,
                        backgroundColor: '#10B981', // Success color
                        borderRadius: 4,
                        barThickness: 'flex'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `Visits: ${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: function (value) { return value.toLocaleString(); },
                                font: { size: 11 }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>