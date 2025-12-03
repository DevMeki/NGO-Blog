<?php
session_start();
require_once '../Backend/Config.php';

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

// Initialize variables
$posts = [];
$fetch_error = null;
$search_results = null;
$is_search = false;

// Handle Live Search
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search_action']) && $_POST['search_action'] === 'live_search') {
    header('Content-Type: application/json');

    try {
        $searchTerm = trim($_POST['search_term'] ?? '');

        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            echo json_encode([]);
            exit();
        }

        $searchPattern = '%' . $searchTerm . '%';
        $sql = "SELECT draft_id, Title, Content, Date_posted, Categories, Created_by 
                FROM draft 
                WHERE Title LIKE ? OR Created_by LIKE ? OR Categories LIKE ?
                ORDER BY Date_posted DESC 
                LIMIT 20";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $searchPattern, $searchPattern, $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();

        $searchResults = [];
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }

        echo json_encode($searchResults);
        exit();

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}

// Handle Delete Action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {
    header('Content-Type: application/json');

    $draft_id = intval($_POST['draft_id'] ?? 0);

    if ($draft_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid draft ID']);
        exit();
    }

    try {
        $sql = "DELETE FROM draft WHERE draft_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $draft_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => "Draft deleted successfully"]);
        } else {
            echo json_encode(['success' => false, 'error' => "Database error: " . $stmt->error]);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit();
}

// Pagination setup
$records_per_page = 10;
$count_query = "SELECT COUNT(*) AS total FROM draft";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $records_per_page);

$current_page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $records_per_page;

// Fetch posts
function getDrafts($conn, $limit = 10, $offset = 0)
{
    $sql = "SELECT draft_id, Title, Content, Date_posted, Categories, Created_by 
            FROM draft 
            ORDER BY Date_posted DESC 
            LIMIT ? OFFSET ?";

    $posts = [];

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();
    }

    return $posts;
}

$posts = getDrafts($conn, $records_per_page, $offset);

if (empty($posts) && $conn->error) {
    $fetch_error = "Could not retrieve drafts due to a database error.";
} elseif (empty($posts)) {
    $fetch_error = "No drafts have been created yet.";
}
?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Draft List - Admin Panel</title>

    <link rel="icon" href="../Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="../Assets/img/logo bg.png">
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
                        "success": "#10B981",
                        "danger": "#EF4444"
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

<body class="bg-background-light dark:bg-background-dark font-display text-gray-700 dark:text-gray-300">
    <div class="flex min-h-screen">
        <?php include 'Admin_nav.php' ?>

        <main class="flex-1 md:ml-64 p-4 sm:p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Manage Drafts</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1 sm:mt-2 text-sm sm:text-base">Create, edit, and manage your blog post drafts</p>
                    </div>
                    <a href="Admin_Post_Editor.php"
                        class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-medium transition-colors w-full sm:w-auto justify-center">
                        <span class="material-symbols-outlined">add_circle</span>
                        Create New Draft
                    </a>
                </div>

                <!-- Search Section -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 sm:p-4 mb-6">
                    <div class="w-full">
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 material-symbols-outlined">search</span>
                            <input type="text" id="searchInput"
                                placeholder="Search drafts by title, author, or category..."
                                class="w-full pl-10 pr-4 py-2.5 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm sm:text-base">
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 px-1">Type at least 2 characters to search</p>
                        <div id="searchResults"
                            class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg hidden max-h-64 overflow-y-auto">
                        </div>
                    </div>
                </div>

                <!-- Desktop Table (hidden on mobile) -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hidden md:block">
                    <?php if ($fetch_error): ?>
                        <div class="p-6 sm:p-8 text-center">
                            <span class="material-symbols-outlined text-5xl sm:text-6xl text-gray-400 mb-3 sm:mb-4">drafts</span>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Drafts Available</h3>
                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($fetch_error); ?></p>
                        </div>
                    <?php elseif (empty($posts)): ?>
                        <div class="p-6 sm:p-8 text-center">
                            <span class="material-symbols-outlined text-5xl sm:text-6xl text-gray-400 mb-3 sm:mb-4">drafts</span>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Drafts Created Yet</h3>
                            <p class="text-gray-600 dark:text-gray-400">Get started by creating your first draft.</p>
                            <a href="Admin_Post_Editor.php"
                                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-lg font-medium transition-colors mt-3 sm:mt-4">
                                <span class="material-symbols-outlined">add_circle</span>
                                Create First Draft
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Title</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Preview</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Author</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date Created</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                    <?php foreach ($posts as $post):
                                        $snippet = strip_tags($post['Content']);
                                        $snippet = strlen($snippet) > 100 ? substr($snippet, 0, 100) . '...' : $snippet;
                                        $formatted_date = date("M j, Y", strtotime($post['Date_posted']));
                                        ?>
                                        <tr data-draft-id="<?php echo $post['draft_id']; ?>"
                                            class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo htmlspecialchars($post['Title']); ?>
                                                </div>
                                                <?php if ($post['Categories']): ?>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        <?php echo htmlspecialchars($post['Categories']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3">
                                                <div class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                                    <?php echo htmlspecialchars($snippet); ?>
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                <?php echo htmlspecialchars($post['Created_by']); ?>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                <?php echo $formatted_date; ?>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center gap-2 sm:gap-3">
                                                    <a href="Admin_Post_Editor.php?id=<?php echo htmlspecialchars($post['draft_id']); ?>">
                                                        <button class="text-primary hover:text-primary/80 transition-colors"
                                                            title="Edit Draft">
                                                            <span class="material-symbols-outlined text-lg">edit</span>
                                                        </button>
                                                    </a>

                                                    <button onclick="showDeleteModal(<?php echo $post['draft_id']; ?>)"
                                                        class="text-danger hover:text-danger/80 transition-colors"
                                                        title="Delete Draft">
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile Card Layout (visible on mobile only) -->
                <div class="md:hidden space-y-3">
                    <?php if ($fetch_error): ?>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                            <span class="material-symbols-outlined text-5xl text-gray-400 mb-3">drafts</span>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Drafts Available</h3>
                            <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($fetch_error); ?></p>
                        </div>
                    <?php elseif (empty($posts)): ?>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                            <span class="material-symbols-outlined text-5xl text-gray-400 mb-3">drafts</span>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Drafts Created Yet</h3>
                            <p class="text-gray-600 dark:text-gray-400">Get started by creating your first draft.</p>
                            <a href="Admin_Post_Editor.php"
                                class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-lg font-medium transition-colors mt-4 w-full justify-center">
                                <span class="material-symbols-outlined">add_circle</span>
                                Create First Draft
                            </a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post):
                            $snippet = strip_tags($post['Content']);
                            $snippet = strlen($snippet) > 80 ? substr($snippet, 0, 80) . '...' : $snippet;
                            $formatted_date = date("M j, Y", strtotime($post['Date_posted']));
                        ?>
                            <div class="mobile-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                <!-- Card Header -->
                                <div class="p-3 sm:p-4 border-b border-gray-100 dark:border-gray-700">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-1">
                                                <?php echo htmlspecialchars($post['Title']); ?>
                                            </h3>
                                            <?php if ($post['Categories']): ?>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <?php echo htmlspecialchars($post['Categories']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card Body -->
                                <div class="p-3 sm:p-4 space-y-3">
                                    <!-- Content Preview -->
                                    <div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Preview</div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            <?php echo htmlspecialchars($snippet); ?>
                                        </p>
                                    </div>
                                    
                                    <!-- Author and Date -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Author</div>
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($post['Created_by']); ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created</div>
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                <?php echo $formatted_date; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card Footer - Actions -->
                                <div class="p-3 sm:p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                                    <div class="flex gap-2">
                                        <a href="Admin_Post_Editor.php?id=<?php echo htmlspecialchars($post['draft_id']); ?>"
                                           class="flex items-center justify-center gap-2 flex-1 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-base">edit</span>
                                            <span class="text-sm font-medium">Edit</span>
                                        </a>
                                        <button onclick="showDeleteModal(<?php echo $post['draft_id']; ?>)"
                                            class="flex items-center justify-center gap-2 flex-1 px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                            <span class="text-sm font-medium">Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
                        <div class="text-sm text-gray-600 dark:text-gray-400 text-center sm:text-left">
                            Showing <?php echo (($current_page - 1) * $records_per_page) + 1; ?> to
                            <?php echo min($current_page * $records_per_page, $total_rows); ?> of <?php echo $total_rows; ?>
                            drafts
                        </div>
                        <div class="flex flex-col xs:flex-row items-center gap-3 w-full sm:w-auto">
                            <?php if ($current_page > 1): ?>
                                <a href="?page=<?php echo $current_page - 1; ?>"
                                    class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors w-full sm:w-auto">
                                    <span class="material-symbols-outlined text-lg">chevron_left</span>
                                    <span>Previous</span>
                                </a>
                            <?php endif; ?>

                            <div class="flex items-center gap-1 flex-wrap justify-center">
                                <?php 
                                // Show limited pagination on mobile
                                $max_visible_pages = 5;
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $start_page + $max_visible_pages - 1);
                                $start_page = max(1, $end_page - $max_visible_pages + 1);
                                
                                for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <?php if ($i == $current_page): ?>
                                        <span
                                            class="px-3 py-2 bg-primary text-white rounded-lg font-medium text-sm"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $i; ?>"
                                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <?php if ($current_page < $total_pages): ?>
                                <a href="?page=<?php echo $current_page + 1; ?>"
                                    class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors w-full sm:w-auto">
                                    <span>Next</span>
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 items-center flex justify-center z-50 hidden p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-md mx-auto transform transition-all fade-in">
            <div class="flex items-start gap-3 sm:gap-4">
                <div
                    class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-red-100 dark:bg-red-900/20">
                    <span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Draft</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base" id="modalMessage">Are you sure you want to delete this draft? This action cannot be undone.</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-4 sm:mt-6">
                <button onclick="hideDeleteModal()"
                    class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors order-2 sm:order-1">
                    Cancel
                </button>
                <button onclick="confirmDelete()" id="confirmDeleteBtn"
                    class="px-4 py-2.5 bg-danger hover:bg-danger/90 text-white rounded-lg transition-colors order-1 sm:order-2">
                    Delete Draft
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed top-4 right-4 p-3 sm:p-4 rounded-lg text-white shadow-lg transition-all duration-300 transform translate-x-full z-50 max-w-xs sm:max-w-sm text-sm sm:text-base">
    </div>

    <script>
        let currentDraftId = null;
        let searchTimeout = null;

        // Delete Modal Functions
        function showDeleteModal(draftId) {
            currentDraftId = draftId;
            const modal = document.getElementById('deleteModal');
            const message = document.getElementById('modalMessage');

            message.textContent = `Are you sure you want to delete draft #${draftId}? This action cannot be undone.`;
            modal.classList.remove('hidden');
        }

        function hideDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            currentDraftId = null;
        }

        async function confirmDelete() {
            if (!currentDraftId) return;

            const draftId = currentDraftId;
            hideDeleteModal();

            try {
                showToast('Deleting draft...', 'info');

                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete&draft_id=${draftId}`
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Draft deleted successfully!', 'success');
                    removeDraftFromUI(draftId);
                } else {
                    showToast(`Error: ${result.error}`, 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                showToast('Failed to delete draft. Please try again.', 'error');
            }
        }

        function removeDraftFromUI(draftId) {
            // Remove desktop row
            const desktopRow = document.querySelector(`[data-draft-id="${draftId}"]`);
            if (desktopRow) {
                desktopRow.style.opacity = '0';
                desktopRow.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    desktopRow.remove();
                    checkEmptyState();
                }, 300);
            }
            
            // Remove mobile card
            const mobileCards = document.querySelectorAll('.mobile-card');
            mobileCards.forEach(card => {
                const deleteBtn = card.querySelector(`[onclick*="${draftId}"]`);
                if (deleteBtn) {
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        card.remove();
                        checkEmptyState();
                    }, 300);
                }
            });
        }

        function checkEmptyState() {
            const remainingDesktop = document.querySelectorAll('[data-draft-id]');
            const remainingMobile = document.querySelectorAll('.mobile-card');
            if (remainingDesktop.length === 0 && remainingMobile.length === 0) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }

        // Search Functionality
        document.getElementById('searchInput').addEventListener('input', function (e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();
            const resultsContainer = document.getElementById('searchResults');

            if (searchTerm.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => performSearch(searchTerm), 300);
        });

        async function performSearch(searchTerm) {
            const resultsContainer = document.getElementById('searchResults');

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `search_action=live_search&search_term=${encodeURIComponent(searchTerm)}`
                });

                const results = await response.json();
                displaySearchResults(results, searchTerm);

            } catch (error) {
                resultsContainer.innerHTML = `
                    <div class="p-3 text-red-600 dark:text-red-400 text-sm">
                        Search failed: ${error.message}
                    </div>
                `;
                resultsContainer.classList.remove('hidden');
            }
        }

        function displaySearchResults(results, searchTerm) {
            const resultsContainer = document.getElementById('searchResults');

            if (!results || results.length === 0) {
                resultsContainer.innerHTML = '<div class="p-3 text-gray-500 dark:text-gray-400 text-sm">No drafts found</div>';
                resultsContainer.classList.remove('hidden');
                return;
            }

            // Show mobile layout for search results
            if (window.innerWidth < 768) {
                const mobileResults = results.map(draft => {
                    const snippet = draft.Content ? draft.Content.substring(0, 80) + '...' : '';
                    const date = new Date(draft.Date_posted).toLocaleDateString();
                    
                    return `
                        <div class="p-3 border-b border-gray-200 dark:border-gray-600 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                             onclick="selectSearchResult(${draft.draft_id})">
                            <div class="font-medium text-gray-900 dark:text-white text-sm">${highlightText(draft.Title, searchTerm)}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">${highlightText(snippet, searchTerm)}</div>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400">By ${highlightText(draft.Created_by, searchTerm)}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${date}</span>
                            </div>
                        </div>
                    `;
                }).join('');
                
                resultsContainer.innerHTML = mobileResults;
            } else {
                const resultsHTML = results.map(draft => `
                    <div class="p-4 border-b border-gray-200 dark:border-gray-600 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                         onclick="selectSearchResult(${draft.draft_id})">
                        <div class="font-medium text-gray-900 dark:text-white">${highlightText(draft.Title, searchTerm)}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300 mt-1">By ${highlightText(draft.Created_by, searchTerm)}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">${new Date(draft.Date_posted).toLocaleDateString()}</div>
                    </div>
                `).join('');

                resultsContainer.innerHTML = resultsHTML;
            }
            
            resultsContainer.classList.remove('hidden');
        }

        function highlightText(text, searchTerm) {
            if (!searchTerm) return text;
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-800">$1</span>');
        }

        function selectSearchResult(draftId) {
            document.getElementById('searchInput').value = '';
            document.getElementById('searchResults').classList.add('hidden');
            
            // Scroll to the draft in desktop table
            const desktopElement = document.querySelector(`[data-draft-id="${draftId}"]`);
            if (desktopElement) {
                desktopElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                desktopElement.classList.add('bg-yellow-100', 'dark:bg-yellow-900');
                setTimeout(() => {
                    desktopElement.classList.remove('bg-yellow-100', 'dark:bg-yellow-900');
                }, 2000);
            }
            
            // Scroll to the draft in mobile card
            const mobileCards = document.querySelectorAll('.mobile-card');
            mobileCards.forEach(card => {
                const editBtn = card.querySelector(`[href*="${draftId}"]`);
                if (editBtn) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    card.classList.add('bg-yellow-100', 'dark:bg-yellow-900');
                    setTimeout(() => {
                        card.classList.remove('bg-yellow-100', 'dark:bg-yellow-900');
                    }, 2000);
                }
            });
        }

        // Toast Notification
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                info: 'bg-blue-600',
                warning: 'bg-yellow-600'
            };

            toast.textContent = message;
            toast.className = `fixed top-4 right-4 p-3 sm:p-4 rounded-lg text-white shadow-lg transition-all duration-300 z-50 ${colors[type]} transform translate-x-0 max-w-xs sm:max-w-sm text-sm sm:text-base`;

            setTimeout(() => {
                toast.classList.add('transform', 'translate-x-full');
            }, 4000);
        }

        // Close search results when clicking outside
        document.addEventListener('click', function (e) {
            if (!e.target.closest('#searchInput') && !e.target.closest('#searchResults')) {
                document.getElementById('searchResults').classList.add('hidden');
            }
        });

        // Handle escape key for modals
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideDeleteModal();
            }
        });
    </script>

    <?php include 'Nav_script.php'; ?>
</body>

</html>