<?php
session_start();
require_once '../Backend/Config.php';
// require_once '../Backend/track_visits.php';

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

// Fetch admin username
$username = $_SESSION['username'];

// Check for logout confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_destroy();
    header('Location: Admin_login.php');
    exit();
}

// Fetch inquiries from database
$inquiries = [];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

try {
    // Build query with filters
    $sql = "SELECT Inquiry_id, Name, Email, Subject, Message, Date_Sent, status 
            FROM inquiry 
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if (!empty($search)) {
        $sql .= " AND (Name LIKE ? OR Email LIKE ? OR Subject LIKE ? OR Message LIKE ?)";
        $search_term = "%$search%";
        $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
        $types .= 'ssss';
    }
    
    if ($status_filter !== 'all') {
        $sql .= " AND status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }
    
    $sql .= " ORDER BY Date_Sent DESC";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $inquiries[] = $row;
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error fetching inquiries: " . $e->getMessage());
}

// $conn->close();
?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Inquiries</title>

    <link rel="icon" href="../Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="./Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
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
                        "success": "#10B981",
                        "custom-blue": "#3B82F6",
                        "custom-green": "#10B981"
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
        /* Mobile card animation */
        .mobile-card {
            transition: all 0.3s ease;
        }
        
        .mobile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .dark .mobile-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#374151] dark:text-gray-300">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root">
        <div class="flex min-h-screen">
            <?php include 'Admin_nav.php' ?>

            <main class="flex-1 md:ml-64 p-3 lg:p-4">
                <div class="w-full max-w-7xl mx-auto">
                    <!-- PageHeading -->
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
                        <h1 class="text-slate-900 dark:text-white text-4xl font-black leading-tight tracking-tight">
                            Contact Inquiries</h1>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            Total: <span class="font-semibold text-slate-900 dark:text-white"><?php echo count($inquiries); ?></span> inquiries
                        </div>
                    </div>
                    
                    <!-- Search and Filter Bar -->
                    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <form method="GET" action="" id="filterForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- SearchBar -->
                                <div class="lg:col-span-2">
                                    <label class="flex flex-col w-full">
                                        <div class="flex w-full flex-1 items-stretch rounded-lg h-12">
                                            <div class="text-slate-500 dark:text-slate-400 flex bg-slate-100 dark:bg-slate-800 items-center justify-center pl-4 rounded-l-lg">
                                                <span class="material-symbols-outlined">search</span>
                                            </div>
                                            <input name="search" id="searchInput"
                                                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/50 border-none bg-slate-100 dark:bg-slate-800 h-full placeholder:text-slate-500 dark:placeholder:text-slate-400 px-4 rounded-l-none pl-2 text-base font-normal leading-normal"
                                                placeholder="Search by name, email, or subject..." 
                                                value="<?php echo htmlspecialchars($search); ?>" />
                                        </div>
                                    </label>
                                </div>
                                <!-- Chips / Filter -->
                                <div class="flex items-center">
                                    <select name="status" id="statusFilter" onchange="document.getElementById('filterForm').submit()"
                                        class="form-select w-full h-12 rounded-lg border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-primary/50 focus:border-primary/50">
                                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
                                        <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                                        <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Desktop Table (hidden on mobile) -->
                    <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden hidden md:block">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                                <thead class="text-xs text-slate-700 dark:text-slate-300 uppercase bg-slate-50 dark:bg-slate-800">
                                    <tr>
                                        <th class="px-6 py-3 font-medium" scope="col">Sender</th>
                                        <th class="px-6 py-3 font-medium" scope="col">Subject</th>
                                        <th class="px-6 py-3 font-medium" scope="col">Message Preview</th>
                                        <th class="px-6 py-3 font-medium" scope="col">Received</th>
                                        <th class="px-6 py-3 font-medium" scope="col">Status</th>
                                        <th class="px-6 py-3 font-medium text-right" scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="inquiriesTableBody">
                                    <?php if (empty($inquiries)): ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center">
                                                <div class="text-center py-8">
                                                    <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500">inbox</span>
                                                    <h3 class="mt-2 text-lg font-medium text-slate-900 dark:text-white">No inquiries found</h3>
                                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                                        <?php echo empty($search) && $status_filter === 'all' ? 
                                                            'No submissions from your contact form yet.' : 
                                                            'No inquiries match your search criteria.' ?>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($inquiries as $inquiry): ?>
                                            <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch ($inquiry['status']) {
                                                case 'new':
                                                    $status_class = 'bg-custom-blue/10 text-custom-blue';
                                                    $status_text = 'New';
                                                    break;
                                                case 'read':
                                                    $status_class = 'bg-custom-green/10 text-custom-green';
                                                    $status_text = 'Read';
                                                    break;
                                                case 'archived':
                                                    $status_class = 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
                                                    $status_text = 'Archived';
                                                    break;
                                                default:
                                                    $status_class = 'bg-custom-blue/10 text-custom-blue';
                                                    $status_text = 'New';
                                            }
                                            
                                            // Format date
                                            $date_sent = date('M j, Y', strtotime($inquiry['Date_Sent']));
                                            $time_ago = getTimeAgo($inquiry['Date_Sent']);
                                            
                                            // Truncate message preview
                                            $message_preview = strlen($inquiry['Message']) > 100 ? 
                                                substr($inquiry['Message'], 0, 100) . '...' : 
                                                $inquiry['Message'];
                                            ?>
                                            <tr class="bg-white dark:bg-slate-900 border-b dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="font-semibold text-slate-900 dark:text-white">
                                                        <?php echo htmlspecialchars($inquiry['Name']); ?>
                                                    </div>
                                                    <div class="text-slate-500 dark:text-slate-400">
                                                        <?php echo htmlspecialchars($inquiry['Email']); ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                                    <?php echo htmlspecialchars($inquiry['Subject']); ?>
                                                </td>
                                                <td class="px-6 py-4 max-w-sm" title="<?php echo htmlspecialchars($inquiry['Message']); ?>">
                                                    <?php echo htmlspecialchars($message_preview); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-slate-900 dark:text-white"><?php echo $time_ago; ?></div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo $date_sent; ?></div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-medium <?php echo $status_class; ?>">
                                                        <span class="size-1.5 rounded-full 
                                                            <?php echo $inquiry['status'] === 'new' ? 'bg-custom-blue' : 
                                                                  ($inquiry['status'] === 'read' ? 'bg-custom-green' : 'bg-gray-400'); ?>">
                                                        </span>
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <div class="flex justify-end gap-2">
                                                        <a href="open_Inquiry.php?id=<?php echo $inquiry['Inquiry_id']; ?>"
                                                           class="p-2 text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                           title="View Inquiry">
                                                           <button  class="px-3 py-1 bg-blue-500 text-amber-50 rounded-lg">View</button>
                                                        </a>
                                                        <button onclick="markAsRead(<?php echo $inquiry['Inquiry_id']; ?>, this)"
                                                                class="p-2 text-slate-500 dark:text-slate-400 hover:text-custom-green dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                                                title="Mark as Read"
                                                                <?php echo $inquiry['status'] === 'read' ? 'disabled' : ''; ?>>
                                                            <span class="material-symbols-outlined text-base">check_circle</span>
                                                        </button>
                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card Layout (visible on mobile only) -->
                    <div class="mt-6 md:hidden space-y-4">
                        <?php if (empty($inquiries)): ?>
                            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 text-center">
                                <span class="material-symbols-outlined text-5xl text-slate-400 dark:text-slate-500">inbox</span>
                                <h3 class="mt-2 text-lg font-medium text-slate-900 dark:text-white">No inquiries found</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    <?php echo empty($search) && $status_filter === 'all' ? 
                                        'No submissions from your contact form yet.' : 
                                        'No inquiries match your search criteria.' ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($inquiries as $inquiry): ?>
                                <?php
                                $status_class = '';
                                $status_text = '';
                                switch ($inquiry['status']) {
                                    case 'new':
                                        $status_class = 'bg-custom-blue/10 text-custom-blue';
                                        $status_text = 'New';
                                        break;
                                    case 'read':
                                        $status_class = 'bg-custom-green/10 text-custom-green';
                                        $status_text = 'Read';
                                        break;
                                    case 'archived':
                                        $status_class = 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400';
                                        $status_text = 'Archived';
                                        break;
                                    default:
                                        $status_class = 'bg-custom-blue/10 text-custom-blue';
                                        $status_text = 'New';
                                }
                                
                                // Format date for mobile
                                $date_sent = date('M j, Y', strtotime($inquiry['Date_Sent']));
                                $time_ago = getTimeAgo($inquiry['Date_Sent']);
                                
                                // Truncate message preview for mobile
                                $message_preview_mobile = strlen($inquiry['Message']) > 120 ? 
                                    substr($inquiry['Message'], 0, 120) . '...' : 
                                    $inquiry['Message'];
                                ?>
                                <div class="mobile-card bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                                    <!-- Card Header -->
                                    <div class="p-4 border-b border-slate-100 dark:border-slate-800">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium <?php echo $status_class; ?>">
                                                        <span class="size-1.5 rounded-full 
                                                            <?php echo $inquiry['status'] === 'new' ? 'bg-custom-blue' : 
                                                                  ($inquiry['status'] === 'read' ? 'bg-custom-green' : 'bg-gray-400'); ?>">
                                                        </span>
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                                        <?php echo $time_ago; ?>
                                                    </span>
                                                </div>
                                                <h3 class="font-semibold text-slate-900 dark:text-white text-lg mb-1">
                                                    <?php echo htmlspecialchars($inquiry['Subject']); ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Body -->
                                    <div class="p-4 space-y-3">
                                        <!-- Sender Info -->
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">From</div>
                                            <div class="font-medium text-slate-900 dark:text-white">
                                                <?php echo htmlspecialchars($inquiry['Name']); ?>
                                            </div>
                                            <div class="text-sm text-slate-600 dark:text-slate-300">
                                                <?php echo htmlspecialchars($inquiry['Email']); ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Message Preview -->
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Message</div>
                                            <p class="text-sm text-slate-700 dark:text-slate-300 line-clamp-3">
                                                <?php echo htmlspecialchars($message_preview_mobile); ?>
                                            </p>
                                        </div>
                                        
                                        <!-- Date Received -->
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Received</div>
                                            <div class="text-sm text-slate-900 dark:text-white">
                                                <?php echo $date_sent; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Footer - Actions -->
                                    <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                                        <div class="flex justify-between items-center">
                                            <a href="open_Inquiry.php?id=<?php echo $inquiry['Inquiry_id']; ?>"
                                               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors flex-1 mr-2 text-center">
                                                <span class="material-symbols-outlined text-sm">visibility</span>
                                                View Inquiry
                                            </a>
                                            
                                            <button onclick="markAsRead(<?php echo $inquiry['Inquiry_id']; ?>, this)"
                                                    class="inline-flex items-center justify-center gap-2 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                                                    title="Mark as Read"
                                                    <?php echo $inquiry['status'] === 'read' ? 'disabled' : ''; ?>>
                                                <span class="material-symbols-outlined text-base">check_circle</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 p-4 rounded-lg text-white shadow-lg transition-all duration-300 opacity-0 transform translate-x-full z-50"></div>

    <script>
        // JavaScript functionality
        function markAsRead(inquiryId, button) {
            fetch(`../Backend/update_inquiry_status.php?id=${inquiryId}&status=read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Inquiry marked as read', 'success');
                    
                    // Update UI for desktop table
                    const desktopRow = document.querySelector(`tr [onclick*="${inquiryId}"]`)?.closest('tr');
                    if (desktopRow) {
                        const statusBadge = desktopRow.querySelector('.inline-flex.items-center');
                        statusBadge.className = 'inline-flex items-center gap-1.5 rounded-full bg-custom-green/10 px-2 py-1 text-xs font-medium text-custom-green';
                        statusBadge.innerHTML = '<span class="size-1.5 rounded-full bg-custom-green"></span>Read';
                        const desktopButton = desktopRow.querySelector(`[onclick*="${inquiryId}"]`);
                        desktopButton.disabled = true;
                        desktopButton.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    
                    // Update UI for mobile card
                    const mobileCard = button.closest('.mobile-card');
                    if (mobileCard) {
                        const statusBadge = mobileCard.querySelector('.inline-flex.items-center');
                        statusBadge.className = 'inline-flex items-center gap-1 rounded-full bg-custom-green/10 px-2 py-1 text-xs font-medium text-custom-green';
                        statusBadge.innerHTML = '<span class="size-1.5 rounded-full bg-custom-green"></span>Read';
                        button.disabled = true;
                        button.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                } else {
                    showToast('Error: ' + data.error, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Network error. Please try again.', 'error');
            });
        }

        function showToast(message, type) {
            const toast = document.getElementById('toast');
            const bgColor = type === 'success' ? 'bg-green-600' : 
                           type === 'error' ? 'bg-red-600' : 
                           'bg-blue-600';
            
            toast.textContent = message;
            toast.className = `fixed top-4 right-4 p-4 rounded-lg text-white shadow-lg transition-all duration-300 z-50 ${bgColor} opacity-100 transform translate-x-0`;
            
            setTimeout(() => {
                toast.classList.remove('opacity-100', 'transform', 'translate-x-0');
                toast.classList.add('opacity-0', 'transform', 'translate-x-full');
            }, 3000);
        }

        // Real-time search with debounce
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });

        // Enter key submission
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('filterForm').submit();
            }
        });
    </script>

    <?php include 'Nav_script.php'; ?>
</body>

</html>

<?php
// Helper function to get time ago
function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $time_difference = time() - $time;

    if ($time_difference < 1) { return 'less than 1 second ago'; }
    $condition = array( 
        12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60       => 'month',
        24 * 60 * 60            => 'day',
        60 * 60                 => 'hour',
        60                      => 'minute',
        1                       => 'second'
    );

    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;
        if ($d >= 1) {
            $t = round($d);
            return $t . ' ' . $str . ($t > 1 ? 's' : '') . ' ago';
        }
    }
}