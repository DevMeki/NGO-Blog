<?php
session_start();
require_once '../Backend/Config.php';
// require_once '../Backend/track_visits.php';

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

// Fetch admin username and ID
$username = $_SESSION['username'];
$current_admin_id = $_SESSION['admin_id'] ?? 0;

// Fetch admins from database
$admins = [];
$fetch_error = null;

$sql = "SELECT admin_id, username, last_login, Admin_status, Token, created_at FROM admins ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
} else {
    $fetch_error = "No administrators found.";
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    $admin_id = intval($_POST['admin_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    
    $response = ['success' => false, 'message' => ''];
    
    if ($admin_id <= 0) {
        $response['message'] = 'Invalid admin ID';
        echo json_encode($response);
        exit();
    }
    
    if ($admin_id == $current_admin_id) {
        $response['message'] = 'You cannot modify your own account';
        echo json_encode($response);
        exit();
    }
    
    if (!in_array($action, ['activate', 'deactivate', 'delete'])) {
        $response['message'] = 'Invalid action';
        echo json_encode($response);
        exit();
    }
    
    try {
        if ($action === 'activate' || $action === 'deactivate') {
            $new_status = $action === 'activate' ? 'active' : 'inactive';
            $sql = "UPDATE admins SET Admin_status = ? WHERE admin_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_status, $admin_id);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = "Admin {$action}d successfully";
                $response['new_status'] = $new_status;
            } else {
                $response['message'] = 'Admin not found or status already set';
            }
            $stmt->close();
            
        } elseif ($action === 'delete') {
            $sql = "DELETE FROM admins WHERE admin_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $admin_id);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Admin deleted successfully';
            } else {
                $response['message'] = 'Admin not found';
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        $response['message'] = 'Database error';
    }
    
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Admins</title>

    <link rel="icon" href="../Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="../Assets/img/logo bg.png">
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
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        }
        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }
        .toast.success {
            background-color: #10B981;
        }
        .toast.error {
            background-color: #EF4444;
        }
        
        /* Modal styles */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }
        .modal-backdrop.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            max-width: 28rem;
            width: 90%;
            margin: 1rem;
        }
        .dark .modal-content {
            background: #1f2937;
        }

        /* Mobile card styles */
        .mobile-card {
            transition: all 0.2s ease;
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
                <div class="flex-1">
                    <div class="flex flex-col gap-4 sm:gap-6 max-w-7xl mx-auto">
                        <!-- PageHeading -->
                        <div class="flex flex-wrap justify-between items-center gap-3 sm:gap-4">
                            <div class="flex flex-col gap-1">
                                <p class="text-[#111418] dark:text-white text-2xl sm:text-3xl font-bold tracking-tight">Manage Administrators</p>
                                <p class="text-[#5f758c] dark:text-slate-400 text-sm sm:text-base font-normal leading-normal">
                                    Remove or edit administrator accounts.</p>
                            </div>
                        </div>

                        <!-- Token Generator -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 py-2">
                            <div class="w-full sm:w-auto">
                                <button id="generateTokenBtn" type="button"
                                    class="flex w-full sm:w-auto min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-orange-400 hover:bg-orange-500 text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors">
                                    <span class="truncate">Generate Token</span>
                                </button>
                            </div>
                            <div class="text-gray-950 dark:text-gray-300 pt-2 items-center px-4 font-bold rounded-lg w-full border border-amber-500 dark:border-amber-600 h-10 bg-white dark:bg-gray-800">
                                <p id="Token_Display" class="text-sm sm:text-base">Click button to generate token</p>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Table (hidden on mobile) -->
                    <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-background-dark hidden md:block">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/40">
                                        <th class="px-4 py-3 text-left text-sm font-medium text-[#111418] dark:text-white whitespace-nowrap">Administrator</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-[#111418] dark:text-white whitespace-nowrap">Token</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-[#111418] dark:text-white whitespace-nowrap">Last Login</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-[#111418] dark:text-white whitespace-nowrap">Status</th>
                                        <th class="px-4 py-3 text-left text-sm font-medium text-[#111418] dark:text-white whitespace-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adminTableBody">
                                    <?php if ($fetch_error): ?>
                                        <tr>
                                            <td colspan="5" class="px-4 py-4 text-center text-red-600">
                                                <?php echo htmlspecialchars($fetch_error); ?>
                                            </td>
                                        </tr>
                                    <?php elseif (!empty($admins)): ?>
                                        <?php foreach ($admins as $admin): 
                                            $last_login = $admin['last_login'] ? date("Y-m-d", strtotime($admin['last_login'])) : 'Never';
                                            $status_class = $admin['Admin_status'] === 'active' ?
                                                'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                                'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300';
                                            $status_text = $admin['Admin_status'] === 'active' ? 'Active' : 'Inactive';
                                            $is_current_user = ($current_admin_id > 0 && $admin['admin_id'] == $current_admin_id);
                                        ?>
                                            <tr id="admin-<?php echo $admin['admin_id']; ?>" class="border-t border-t-slate-200 dark:border-t-slate-800">
                                                <td class="px-4 py-2">
                                                    <div class="flex items-center gap-3">
                                                        <div>
                                                            <p class="font-medium text-[#111418] dark:text-white">
                                                                <?php echo htmlspecialchars($admin['username']); ?>
                                                                <?php if ($is_current_user): ?>
                                                                    <span class="text-xs text-blue-600 ml-1">(You)</span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-[#5f758c] dark:text-slate-400 font-mono">
                                                    <?php echo htmlspecialchars($admin['Token']); ?>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-[#5f758c] dark:text-slate-400">
                                                    <?php echo $last_login; ?>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span id="status-<?php echo $admin['admin_id']; ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2">
                                                    <div class="flex items-center gap-2">
                                                        <?php if (!$is_current_user): ?>
                                                            <?php if ($admin['Admin_status'] === 'active'): ?>
                                                                <button data-admin-id="<?php echo $admin['admin_id']; ?>" data-action="deactivate" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>"
                                                                   class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/50 text-orange-600 dark:text-orange-400 transition-colors"
                                                                   title="Deactivate Admin">
                                                                    <span class="material-symbols-outlined text-xl">toggle_off</span>
                                                                </button>
                                                            <?php else: ?>
                                                                <button data-admin-id="<?php echo $admin['admin_id']; ?>" data-action="activate" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>"
                                                                   class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 text-green-600 dark:text-green-400 transition-colors"
                                                                   title="Activate Admin">
                                                                    <span class="material-symbols-outlined text-xl">toggle_on</span>
                                                                </button>
                                                            <?php endif; ?>
                                                            <button data-admin-id="<?php echo $admin['admin_id']; ?>" data-action="delete" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>"
                                                               class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 transition-colors"
                                                               title="Delete Admin">
                                                                <span class="material-symbols-outlined text-xl">delete</span>
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="text-xs text-gray-500">No actions available</span>
                                                        <?php endif; ?>
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
                    <div class="mt-4 md:hidden space-y-3">
                        <?php if ($fetch_error): ?>
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-slate-800 p-4 text-center">
                                <p class="text-red-600 dark:text-red-400"><?php echo htmlspecialchars($fetch_error); ?></p>
                            </div>
                        <?php elseif (!empty($admins)): ?>
                            <?php foreach ($admins as $admin): 
                                $last_login = $admin['last_login'] ? date("M j, Y", strtotime($admin['last_login'])) : 'Never';
                                $status_class = $admin['Admin_status'] === 'active' ?
                                    'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' :
                                    'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300';
                                $status_text = $admin['Admin_status'] === 'active' ? 'Active' : 'Inactive';
                                $is_current_user = ($current_admin_id > 0 && $admin['admin_id'] == $current_admin_id);
                                $created_at = date("M j, Y", strtotime($admin['created_at']));
                            ?>
                                <div id="admin-card-<?php echo $admin['admin_id']; ?>" class="mobile-card bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-slate-800 overflow-hidden">
                                    <!-- Card Header -->
                                    <div class="p-3 sm:p-4 border-b border-slate-100 dark:border-slate-700">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span id="card-status-<?php echo $admin['admin_id']; ?>" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                    <?php if ($is_current_user): ?>
                                                        <span class="text-xs text-blue-600 dark:text-blue-400 px-2 py-0.5 border border-blue-200 dark:border-blue-700 rounded-full">You</span>
                                                    <?php endif; ?>
                                                </div>
                                                <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-1">
                                                    <?php echo htmlspecialchars($admin['username']); ?>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Body -->
                                    <div class="p-3 sm:p-4 space-y-3">
                                        <!-- Token Info -->
                                        <div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Token</div>
                                            <div class="text-sm font-mono text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700/50 p-2 rounded break-all">
                                                <?php echo htmlspecialchars($admin['Token']); ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Account Info -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Last Login</div>
                                                <div class="text-sm text-slate-900 dark:text-white">
                                                    <?php echo $last_login; ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 mb-1">Created</div>
                                                <div class="text-sm text-slate-900 dark:text-white">
                                                    <?php echo $created_at; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Footer - Actions -->
                                    <div class="p-3 sm:p-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                        <?php if (!$is_current_user): ?>
                                            <div class="flex flex-col sm:flex-row gap-2">
                                                <?php if ($admin['Admin_status'] === 'active'): ?>
                                                    <button data-admin-id="<?php echo $admin['admin_id']; ?>" data-action="deactivate" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>"
                                                       class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors flex-1">
                                                        <span class="material-symbols-outlined text-base">toggle_off</span>
                                                        <span class="text-sm font-medium">Deactivate</span>
                                                    </button>
                                                <?php else: ?>
                                                    <button data-admin-id="<?php echo $admin['admin_id']; ?>" data-action="activate" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>"
                                                       class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors flex-1">
                                                        <span class="material-symbols-outlined text-base">toggle_on</span>
                                                        <span class="text-sm font-medium">Activate</span>
                                                    </button>
                                                <?php endif; ?>
                                                <button data-admin-id="<?php echo $admin['admin_id']; ?>" data-action="delete" data-admin-name="<?php echo htmlspecialchars($admin['username']); ?>"
                                                   class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors flex-1">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                    <span class="text-sm font-medium">Delete</span>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-2">
                                                <span class="text-sm text-gray-500 dark:text-gray-400">No actions available for your own account</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Simple Modal -->
    <div id="modalBackdrop" class="modal-backdrop">
        <div class="modal-content">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2" id="modalTitle">Confirm Action</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4" id="modalMessage">Are you sure you want to perform this action?</p>
            <div class="flex justify-end gap-3">
                <button id="modalCancel" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white transition-colors">
                    Cancel
                </button>
                <button id="modalConfirm" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <?php include 'Nav_script.php'; ?>

    <script>
        // Simple modal management
        let currentAction = null;
        
        // Token generation
        document.getElementById('generateTokenBtn').addEventListener('click', generateToken);
        
        async function generateToken() {
            const tokenDisplay = document.getElementById('Token_Display');
            tokenDisplay.textContent = 'Generating...';
            tokenDisplay.className = 'text-gray-500 animate-pulse';

            try {
                const response = await fetch('../Backend/Token.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'generate_token=true'
                });

                const data = await response.json();

                if (data.success) {
                    tokenDisplay.textContent = data.token;
                    tokenDisplay.className = 'text-gray-950 dark:text-gray-300 font-bold';
                } else {
                    tokenDisplay.textContent = 'Error: ' + (data.error || 'Unknown error');
                    tokenDisplay.className = 'text-red-600 font-normal';
                    showToast(data.error || 'Token generation failed', 'error');
                }
            } catch (error) {
                tokenDisplay.textContent = 'Network error';
                tokenDisplay.className = 'text-red-600 font-normal';
                showToast('Network error occurred', 'error');
            }
        }

        // Admin action buttons - handle both desktop and mobile
        document.addEventListener('click', function(e) {
            if (e.target.closest('.admin-action-btn')) {
                const button = e.target.closest('.admin-action-btn');
                const adminId = button.getAttribute('data-admin-id');
                const action = button.getAttribute('data-action');
                const adminName = button.getAttribute('data-admin-name');
                
                showConfirmationModal(adminId, action, adminName);
            }
        });

        function showConfirmationModal(adminId, action, adminName) {
            let message = '';
            let buttonText = 'Confirm';
            let buttonColor = 'bg-red-600';

            switch(action) {
                case 'activate':
                    message = `Are you sure you want to activate ${adminName}?`;
                    buttonText = 'Activate';
                    buttonColor = 'bg-green-600';
                    break;
                case 'deactivate':
                    message = `Are you sure you want to deactivate ${adminName}? They will lose access to the admin panel.`;
                    buttonText = 'Deactivate';
                    buttonColor = 'bg-orange-600';
                    break;
                case 'delete':
                    message = `Are you sure you want to permanently delete ${adminName}? This action cannot be undone.`;
                    buttonText = 'Delete';
                    buttonColor = 'bg-red-600';
                    break;
            }

            currentAction = { adminId, action, adminName };
            
            document.getElementById('modalTitle').textContent = `Confirm ${action.charAt(0).toUpperCase() + action.slice(1)}`;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalConfirm').textContent = buttonText;
            document.getElementById('modalConfirm').className = `px-4 py-2 ${buttonColor} text-white rounded hover:${buttonColor.replace('600', '700')} transition-colors`;
            
            document.getElementById('modalBackdrop').classList.add('active');
        }

        function hideModal() {
            document.getElementById('modalBackdrop').classList.remove('active');
            currentAction = null;
        }

        // Modal event listeners
        document.getElementById('modalCancel').addEventListener('click', hideModal);
        document.getElementById('modalConfirm').addEventListener('click', executeAction);

        // Close modal when clicking backdrop
        document.getElementById('modalBackdrop').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });

        async function executeAction() {
            if (!currentAction) return;

            const { adminId, action, adminName } = currentAction;
            hideModal();

            try {
                const formData = new FormData();
                formData.append('ajax', 'true');
                formData.append('admin_id', adminId);
                formData.append('action', action);

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    
                    // Update UI for desktop table
                    if (action === 'delete') {
                        // Remove desktop row
                        const desktopRow = document.getElementById(`admin-${adminId}`);
                        if (desktopRow) desktopRow.remove();
                        
                        // Remove mobile card
                        const mobileCard = document.getElementById(`admin-card-${adminId}`);
                        if (mobileCard) mobileCard.remove();
                        
                    } else if (action === 'activate' || action === 'deactivate') {
                        // Update desktop status
                        const desktopStatus = document.getElementById(`status-${adminId}`);
                        const newStatus = data.new_status;
                        
                        if (desktopStatus) {
                            if (newStatus === 'active') {
                                desktopStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300';
                                desktopStatus.textContent = 'Active';
                                
                                // Update desktop buttons
                                const row = document.getElementById(`admin-${adminId}`);
                                if (row) {
                                    const buttonsCell = row.querySelector('td:last-child div');
                                    if (buttonsCell) {
                                        buttonsCell.innerHTML = `
                                            <button data-admin-id="${adminId}" data-action="deactivate" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/50 text-orange-600 dark:text-orange-400 transition-colors"
                                               title="Deactivate Admin">
                                                <span class="material-symbols-outlined text-xl">toggle_off</span>
                                            </button>
                                            <button data-admin-id="${adminId}" data-action="delete" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 transition-colors"
                                               title="Delete Admin">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        `;
                                    }
                                }
                            } else {
                                desktopStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300';
                                desktopStatus.textContent = 'Inactive';
                                
                                // Update desktop buttons
                                const row = document.getElementById(`admin-${adminId}`);
                                if (row) {
                                    const buttonsCell = row.querySelector('td:last-child div');
                                    if (buttonsCell) {
                                        buttonsCell.innerHTML = `
                                            <button data-admin-id="${adminId}" data-action="activate" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 text-green-600 dark:text-green-400 transition-colors"
                                               title="Activate Admin">
                                                <span class="material-symbols-outlined text-xl">toggle_on</span>
                                            </button>
                                            <button data-admin-id="${adminId}" data-action="delete" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center size-8 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 transition-colors"
                                               title="Delete Admin">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        `;
                                    }
                                }
                            }
                        }
                        
                        // Update mobile card
                        const mobileStatus = document.getElementById(`card-status-${adminId}`);
                        const mobileCard = document.getElementById(`admin-card-${adminId}`);
                        
                        if (mobileStatus && mobileCard) {
                            if (newStatus === 'active') {
                                mobileStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300';
                                mobileStatus.textContent = 'Active';
                                
                                // Update mobile buttons
                                const footer = mobileCard.querySelector('.border-t');
                                if (footer) {
                                    footer.innerHTML = `
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <button data-admin-id="${adminId}" data-action="deactivate" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors flex-1">
                                                <span class="material-symbols-outlined text-base">toggle_off</span>
                                                <span class="text-sm font-medium">Deactivate</span>
                                            </button>
                                            <button data-admin-id="${adminId}" data-action="delete" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors flex-1">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                                <span class="text-sm font-medium">Delete</span>
                                            </button>
                                        </div>
                                    `;
                                }
                            } else {
                                mobileStatus.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300';
                                mobileStatus.textContent = 'Inactive';
                                
                                // Update mobile buttons
                                const footer = mobileCard.querySelector('.border-t');
                                if (footer) {
                                    footer.innerHTML = `
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <button data-admin-id="${adminId}" data-action="activate" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors flex-1">
                                                <span class="material-symbols-outlined text-base">toggle_on</span>
                                                <span class="text-sm font-medium">Activate</span>
                                            </button>
                                            <button data-admin-id="${adminId}" data-action="delete" data-admin-name="${adminName}"
                                               class="admin-action-btn flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors flex-1">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                                <span class="text-sm font-medium">Delete</span>
                                            </button>
                                        </div>
                                    `;
                                }
                            }
                        }
                    }
                } else {
                    showToast(data.message, 'error');
                }
            } catch (error) {
                showToast('Network error occurred', 'error');
            }
        }

        // Toast notifications
        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
</body>
</html>