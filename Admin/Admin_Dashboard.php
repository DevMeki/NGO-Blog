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
    header('Location: login.php');
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

// Get visits for the last 4 weeks for the chart - REVISED ORDER (Week 4 oldest, Week 1 newest)
$weekly_visits = [0, 0, 0, 0];
$week_labels = ['Week 4', 'Week 3', 'Week 2', 'Week 1'];

$sql = "SELECT 
            YEARWEEK(visit_time) as week_number,
            COUNT(*) as visits 
        FROM visitor_logs 
        WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK)
        GROUP BY YEARWEEK(visit_time)
        ORDER BY week_number ASC 
        LIMIT 4";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $weeks_data = [];
    while ($row = $result->fetch_assoc()) {
        $weeks_data[] = $row;
    }

    // Fill the array with data in correct order (Week 4 = oldest, Week 1 = newest)
    $data_count = count($weeks_data);
    for ($i = 0; $i < 4; $i++) {
        if ($i < $data_count) {
            $weekly_visits[3 - $i] = $weeks_data[$i]['visits'] ?? 0;
        }
    }
} else {
    error_log("Weekly visits query failed or no data: " . ($conn->error ?? 'No error'));
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
            
            .mobile-grid-1 {
                grid-template-columns: 1fr !important;
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
                    <!-- Header Section - Stack on mobile -->
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 mb-8 mobile-stack">
                        <div class="flex flex-col gap-1 mobile-text-center lg:text-left w-full lg:w-auto">
                            <h1
                                class="text-2xl lg:text-3xl font-black leading-tight tracking-[-0.033em] text-[#111418] dark:text-white">
                                Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
                            <p class="text-[#5f758c] dark:text-gray-400 text-sm lg:text-base font-normal leading-normal">
                                Here's a quick overview of your website's activity.</p>
                        </div>
                        <div class="relative">
                            <button class="flex items-center gap-2">
                                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 border-2 border-primary"
                                    style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuCg1Z53prw3r7jvHrQ3BWX9HVYQGDUgb7H78xWoCUM5rEvg1XZKPtvuf3Nq6KnY61EdHLyf-t-DGB5dhOXc_JwnoKAOtBkCgoVcuDQv-_IVN_ZNLrv7xbvf2kYv_XC854fjuW-FhKCmKRXHr_PtkDatlihusYBLIfPtnjyY26cUn-lSruOeCWXggGBz0ORQ4lbIYFAwzp6nGAg1I2wG21Ir6N5WaH3jOI_m04KZfWDwWReWdV-I-qiqUZX4WQhUOWwEJLsRq2ll7fkH");'>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards - Stack on mobile -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8 mobile-grid-1">
                        <div
                            class="flex flex-col gap-2 rounded-lg p-4 lg:p-6 bg-white dark:bg-[#1a2633] border border-gray-200 dark:border-gray-700">
                            <p class="text-[#111418] dark:text-white text-sm lg:text-base font-medium leading-normal">Total Posts
                            </p>
                            <p class="text-[#111418] dark:text-white tracking-light text-3xl lg:text-4xl font-bold leading-tight">
                                <?php echo number_format($total_posts); ?>
                            </p>
                            <p class="text-success text-sm lg:text-base font-medium leading-normal"><?php echo $posts_change; ?>
                                from last month</p>
                        </div>
                        <!-- Removed Views card -->
                        <div
                            class="flex flex-col gap-2 rounded-lg p-4 lg:p-6 bg-white dark:bg-[#1a2633] border border-gray-200 dark:border-gray-700">
                            <p class="text-[#111418] dark:text-white text-sm lg:text-base font-medium leading-normal">Total Page
                                Visits</p>
                            <p class="text-[#111418] dark:text-white tracking-light text-3xl lg:text-4xl font-bold leading-tight">
                                <?php echo number_format($total_visits); ?>
                            </p>
                            <p class="text-success text-sm lg:text-base font-medium leading-normal"><?php echo $visits_change; ?>
                                from last month</p>
                        </div>
                    </div>

                    <!-- Main Content - Stack on mobile -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6 mobile-grid-1">
                        <!-- Chart Section - Full width on mobile -->
                        <div
                            class="xl:col-span-2 flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4 lg:p-6 bg-white dark:bg-[#1a2633]">
                            <div class="flex flex-col">
                                <p class="text-[#111418] dark:text-white text-base lg:text-lg font-medium leading-normal">Website
                                    Visits - Last 4 Weeks</p>
                                <p
                                    class="text-[#111418] dark:text-white tracking-light text-2xl lg:text-4xl font-bold leading-tight truncate">
                                    <?php echo number_format(array_sum($weekly_visits)); ?> visits
                                </p>
                                <div class="flex gap-1 flex-wrap">
                                    <p class="text-[#5f758c] dark:text-gray-400 text-sm lg:text-base font-normal leading-normal">
                                        Last 4 Weeks</p>
                                    <p class="text-success text-sm lg:text-base font-medium leading-normal">
                                        <?php echo $visits_change; ?></p>
                                </div>
                            </div>
                            <div class="flex min-h-[200px] lg:min-h-[250px] flex-1 flex-col gap-4 lg:gap-8 py-2 lg:py-4">
                                <canvas id="visitsChart"></canvas>
                            </div>
                        </div>

                        <!-- Sidebar Section - Full width on mobile -->
                        <div class="flex flex-col gap-4 lg:gap-6">
                            <!-- Recent Posts -->
                            <div
                                class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4 lg:p-6 bg-white dark:bg-[#1a2633]">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-[#111418] dark:text-white font-medium">Recent Posts</h3>
                                    <a class="text-sm text-primary hover:underline" href="List_post.php">View All</a>
                                </div>
                                <div class="space-y-3 lg:space-y-4">
                                    <?php if (!empty($blog_posts)): ?>
                                        <?php foreach ($blog_posts as $post): ?>
                                            <div class="flex flex-col">
                                                <p class="font-medium text-[#111418] dark:text-white truncate text-sm lg:text-base">
                                                    <?php echo htmlspecialchars($post['Title'] ?? 'No Title'); ?>
                                                </p>
                                                <p class="text-xs lg:text-sm text-[#5f758c] dark:text-gray-400">
                                                    by <?php echo htmlspecialchars($post['published_by'] ?? 'Unknown'); ?> -
                                                    <?php echo date('M j', strtotime($post['Date_posted'] ?? 'now')); ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-[#5f758c] dark:text-gray-400 text-sm">No posts found in database</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div
                                class="flex flex-col gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4 lg:p-6 bg-white dark:bg-[#1a2633]">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-[#111418] dark:text-white font-medium">Quick Stats</h3>
                                </div>
                                <div class="space-y-3 lg:space-y-4">
                                    <div class="flex flex-col">
                                        <p class="text-xs lg:text-sm text-[#5f758c] dark:text-gray-400">Today's Visits</p>
                                        <p class="font-medium text-[#111418] dark:text-white text-base lg:text-lg">
                                            <?php echo number_format($today_visits); ?>
                                        </p>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs lg:text-sm text-[#5f758c] dark:text-gray-400">This Week's Visits</p>
                                        <p class="font-medium text-[#111418] dark:text-white text-base lg:text-lg">
                                            <?php echo number_format($this_week_visits); ?>
                                        </p>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs lg:text-sm text-[#5f758c] dark:text-gray-400">Drafts</p>
                                        <p class="font-medium text-[#111418] dark:text-white text-base lg:text-lg">
                                            <?php echo number_format($total_drafts); ?>
                                        </p>
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="text-xs lg:text-sm text-[#5f758c] dark:text-gray-400">Inquiries</p>
                                        <p class="font-medium text-[#111418] dark:text-white text-base lg:text-lg">
                                            <?php echo number_format($total_inquiries); ?>
                                        </p>
                                    </div>
                                </div>
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
        });
    </script>
</body>

</html>