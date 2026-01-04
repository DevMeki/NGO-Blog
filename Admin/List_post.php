<?php
session_start();
require_once '../Backend/Config.php';
// require_once '../Backend/track_visits.php';

// LIVE SEARCH HANDLER 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search_action']) && $_POST['search_action'] === 'live_search') {

    while (ob_get_level())
        ob_end_clean();

    header('Content-Type: application/json');

    try {
        if (!isset($conn)) {
            throw new Exception('Database connection not available');
        }

        $searchTerm = trim($_POST['search_term'] ?? '');

        if (empty($searchTerm) || strlen($searchTerm) < 2) {
            echo json_encode([]);
            exit();
        }

        $searchPattern = '%' . $searchTerm . '%';
        $sql = "SELECT post_id, title, content, date_posted, Categories, published_by 
                FROM blog_post 
                WHERE title LIKE ? OR published_by LIKE ? OR date_posted LIKE ?
                ORDER BY date_posted DESC 
                LIMIT 20";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $searchPattern, $searchPattern, $searchPattern);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $searchResults = [];

                while ($row = $result->fetch_assoc()) {
                    $searchResults[] = $row;
                }

                echo json_encode($searchResults);
            } else {
                throw new Exception('Query execution failed: ' . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception('Query preparation failed: ' . $conn->error);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }

    exit();
}

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

if (!isset($conn)) {
    die("Error: Database connection object (\$conn) is not available. Please include your connection file.");
}

$records_per_page = 10;
$count_query = "SELECT COUNT(*) AS total FROM blog_post";
$count_result = mysqli_query($conn, $count_query);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $records_per_page);

$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($current_page < 1)
    $current_page = 1;
if ($current_page > $total_pages)
    $current_page = $total_pages;

$offset = ($current_page - 1) * $records_per_page;

$posts = [];
$fetch_error = null;

/**
 * @param mysqli $conn 
 * @param int $limit The maximum number of posts to return.
 * @param int $offset The starting offset for pagination.
 * @return array 
 */
function getPublishedPosts($conn, $limit = 10, $offset = 0)
{
    $sql = "SELECT post_id, title, content, date_posted, Categories, published_by FROM blog_post ORDER BY date_posted DESC LIMIT ? OFFSET ?";

    $posts = [];

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $limit, $offset);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $posts[] = $row;
            }
            $stmt->close();
        } else {
            // Log execution error
            error_log("Failed to execute post fetch query: " . $stmt->error);
        }
    } else {
        // Log preparation error
        error_log("Failed to prepare post fetch statement: " . $conn->error);
    }

    return $posts;
}


// Fetching Logic
$posts = getPublishedPosts($conn, 10, $offset);

if (empty($posts) && $conn->error) {
    $fetch_error = "Could not retrieve posts due to a database error.";
} elseif (empty($posts)) {
    $fetch_error = "No blog posts have been published yet.";
}

?>

<?php
$DEBUG_MODE = false; // Set to false for production

// 1. Check if this is an AJAX request for deletion
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete') {

    header('Content-Type: application/json');

    $post_id = intval($_POST['post_id'] ?? 0);

    if ($post_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid post ID provided.'
        ]);
        exit();
    }

    // SIMULATION MODE (when no connection or debug mode)
    if (!isset($conn) || $DEBUG_MODE) {
        echo json_encode([
            'success' => true,
            'message' => "Post deleted successfully. (SIMULATED)",
        ]);
        exit();
    }

    // REAL DATABASE OPERATION
    // First, fetch the post to get image paths before deleting
    $fetch_sql = "SELECT Image_path FROM blog_post WHERE post_id = ?";
    $image_paths = [];
    if ($fetch_stmt = $conn->prepare($fetch_sql)) {
        $fetch_stmt->bind_param("i", $post_id);
        if ($fetch_stmt->execute()) {
            $fetch_result = $fetch_stmt->get_result();
            if ($row = $fetch_result->fetch_assoc()) {
                $image_paths = json_decode($row['Image_path'] ?? '[]', true) ?: [];
            }
        }
        $fetch_stmt->close();
    }

    // Fetch video paths from post_videos table
    $video_paths = [];
    $video_fetch_sql = "SELECT video_path FROM post_videos WHERE post_id = ?";
    if ($video_stmt = $conn->prepare($video_fetch_sql)) {
        $video_stmt->bind_param("i", $post_id);
        if ($video_stmt->execute()) {
            $video_result = $video_stmt->get_result();
            while ($row = $video_result->fetch_assoc()) {
                $video_paths[] = $row['video_path'];
            }
        }
        $video_stmt->close();
    }

    // Delete associated media files (images and videos)
    $files_deleted = 0;
    $files_failed = 0;
    $upload_dir = dirname(__DIR__) . '/Assets/uploads/'; // Absolute path to uploads directory

    // Combine all media paths (images and videos)
    $all_media_paths = array_merge($image_paths, $video_paths);

    foreach ($all_media_paths as $media_path) {
        // Extract filename from relative path (e.g., "Assets/uploads/filename.jpg" -> "filename.jpg")
        $filename = basename($media_path);
        $full_path = $upload_dir . $filename;

        if (file_exists($full_path)) {
            if (unlink($full_path)) {
                $files_deleted++;
            } else {
                $files_failed++;
                error_log("Failed to delete file: " . $full_path);
            }
        }
    }

    // Now delete the post from database
    $sql = "DELETE FROM blog_post WHERE post_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "Post deleted successfully.";
                if ($files_deleted > 0) {
                    $message .= " {$files_deleted} media file(s) deleted.";
                }
                if ($files_failed > 0) {
                    $message .= " {$files_failed} media file(s) could not be deleted.";
                }
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => "Post not found or already deleted."]);
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => "Database execution failed: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        // Prepare statement failed
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database preparation failed: ' . $conn->error]);
    }

    exit();
}

?>

<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Posts List</title>

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
    <style>
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
        
        /* Mobile search results */
        .mobile-search-result {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }
        .dark .mobile-search-result {
            border-bottom-color: #4b5563;
        }
        .mobile-search-result:last-child {
            border-bottom: none;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark font-display text-[#374151] dark:text-gray-300">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root">
        <div class="flex min-h-screen">
            <?php include 'Admin_nav.php' ?>
            <main class="flex-1 p-4 sm:p-6 md:p-8 md:ml-64">
                <div class="max-w-7xl mx-auto">
                    <!-- PageHeading -->
                    <div class="flex flex-wrap justify-between gap-4 items-center mb-4 sm:mb-6">
                        <p class="text-gray-900 dark:text-white text-2xl sm:text-3xl md:text-4xl font-black leading-tight tracking-[-0.033em]">
                            Manage Blog Posts</p>
                        <a href="Admin_Post_Editor.php" class="w-full sm:w-auto">
                            <button
                                class="flex w-full sm:w-auto min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary hover:bg-primary/90 text-white text-sm font-bold leading-normal tracking-[0.015em] transition-colors">
                                <span class="material-symbols-outlined mr-2 text-base">add_circle</span>
                                <span class="truncate">Create New Post</span>
                            </button>
                        </a>
                    </div>
                    
                    <!-- Search and Filters -->
                    <div class="flex flex-col gap-4 mb-4 sm:mb-6">
                        <!-- SearchBar -->
                        <div class="flex-1">
                            <label class="flex flex-col h-12 w-full">
                                <div
                                    class="flex w-full flex-1 items-stretch rounded-lg h-full bg-white dark:bg-background-dark border border-gray-200 dark:border-gray-700">
                                    <div class="text-gray-500 dark:text-gray-400 flex items-center justify-center pl-3 sm:pl-4">
                                        <span class="material-symbols-outlined text-sm sm:text-base">search</span>
                                    </div>
                                    <input
                                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden text-gray-900 dark:text-white focus:outline-0 focus:ring-0 border-none bg-transparent h-full placeholder:text-gray-500 dark:placeholder:text-gray-400 pl-2 pr-3 sm:pr-4 text-sm sm:text-base font-normal leading-normal"
                                        placeholder="Search by title or author..." id="searchInput" type="text" />
                                </div>
                            </label>

                            <!-- RESULTS CONTAINER -->
                            <div id="resultsContainer" class="mt-2"></div>
                        </div>
                    </div>
                    
                    <!-- Desktop Table (hidden on mobile) -->
                    <div class="hidden md:block overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark">
                        <?php if ($fetch_error): ?>
                            <div class="col-span-full bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg"
                                role="alert">
                                <p class="font-bold">Notice</p>
                                <p><?php echo htmlspecialchars($fetch_error); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Title</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Content</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Author</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date Published</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Delete</th>
                                    </tr>
                                </thead>
                                <?php foreach ($posts as $post):
                                    // Truncate content for a snippet (e.g., first 20 characters)
                                    $snippet = substr(strip_tags($post['content']), 0, 20) . (strlen(strip_tags($post['content'])) > 100 ? '...' : '');

                                    // Format date
                                    $formatted_date = date("M j, Y", strtotime($post['date_posted']));
                                    ?>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr data-post-id="<?php echo $post['post_id']; ?>">
                                            <td
                                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                                <?php if ($post['Categories']): ?>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        <?php echo htmlspecialchars($post['Categories']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td
                                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white max-w-xs truncate">
                                                <?php echo htmlspecialchars($snippet); ?>
                                            </td>
                                            <td
                                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo htmlspecialchars($post['published_by']); ?>
                                            </td>
                                            <td
                                                class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <?php echo $formatted_date; ?>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="text-danger hover:text-danger/80 ml-4"
                                                    onclick="show_Modal(<?php echo $post['post_id']; ?>)">
                                                    <span class="material-symbols-outlined text-xl">delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card Layout (visible on mobile only) -->
                    <div class="md:hidden space-y-3">
                        <?php if ($fetch_error): ?>
                            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg"
                                role="alert">
                                <p class="font-bold">Notice</p>
                                <p><?php echo htmlspecialchars($fetch_error); ?></p>
                            </div>
                        <?php elseif (!empty($posts)): ?>
                            <?php foreach ($posts as $post): 
                                // Truncate content for mobile
                                $snippet_mobile = strip_tags($post['content']);
                                $snippet_mobile = strlen($snippet_mobile) > 100 ? substr($snippet_mobile, 0, 100) . '...' : $snippet_mobile;
                                
                                // Format date
                                $formatted_date = date("M j, Y", strtotime($post['date_posted']));
                            ?>
                                <div class="mobile-card bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                    <!-- Card Header -->
                                    <div class="p-3 sm:p-4 border-b border-gray-100 dark:border-gray-700">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white mb-1">
                                                    <?php echo htmlspecialchars($post['title']); ?>
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
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Content Preview</div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                                <?php echo htmlspecialchars($snippet_mobile); ?>
                                            </p>
                                        </div>
                                        
                                        <!-- Author and Date -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Author</div>
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    <?php echo htmlspecialchars($post['published_by']); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Published</div>
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    <?php echo $formatted_date; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Card Footer - Actions -->
                                    <div class="p-3 sm:p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                                        <button onclick="show_Modal(<?php echo $post['post_id']; ?>)"
                                            class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-base">delete</span>
                                            <span class="text-sm font-medium">Delete Post</span>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                                <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-500 mb-2">article</span>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No Posts Yet</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Get started by creating your first blog post.</p>
                                <a href="Admin_Post_Editor.php" class="inline-block mt-3">
                                    <button class="flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-base">add_circle</span>
                                        <span class="text-sm font-medium">Create Post</span>
                                    </button>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-center mt-4 sm:mt-6">
                        <div class="flex items-center gap-1 sm:gap-2">
                            <?php
                            $prev_page = $current_page - 1;
                            if ($current_page > 1) {
                                echo "<a href='?page=$prev_page'>
                <button class='flex items-center justify-center h-8 sm:h-9 w-8 sm:w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'>
                                <span class='material-symbols-outlined text-lg sm:text-xl'>chevron_left</span>
                            </button>
                </a>";
                            } else {
                                echo "<button class='px-3 py-1 rounded-md text-sm font-medium border border-gray-200 text-gray-600 cursor-not-allowed'
                disabled=''><span class='material-symbols-outlined text-lg sm:text-xl'>chevron_left</span></button>";
                            }
                            
                            // Show limited pagination on mobile
                            $max_visible_pages = 5;
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $start_page + $max_visible_pages - 1);
                            $start_page = max(1, $end_page - $max_visible_pages + 1);
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                if ($i == $current_page) {
                                    echo "<button class='flex items-center justify-center h-8 sm:h-9 w-8 sm:w-9 rounded-lg bg-primary text-white text-xs sm:text-sm font-medium'>$i</button>";
                                } else {
                                    echo "<a href = '?page=$i'>
                  <button class='flex items-center justify-center h-8 sm:h-9 w-8 sm:w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-xs sm:text-sm font-medium'>$i</button>
                  <a/>";
                                }
                            }
                            
                            $next_page = $current_page + 1;
                            if ($current_page < $total_pages) {
                                echo "<a href='?page=$next_page'>
                <button class = 'flex items-center justify-center h-8 sm:h-9 w-8 sm:w-9 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-background-dark text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'>
                <span class='material-symbols-outlined text-lg sm:text-xl'>chevron_right</span>
                </button>
                </a>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div id="Modal_Box" class="delete hidden fixed inset-0 bg-gray-900 bg-opacity-50 items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-4 sm:p-6 w-full max-w-md mx-auto transform transition-all scale-100">
            <div class="flex items-start">
                <div
                    class="mx-auto flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-danger/10 sm:mx-0">
                    <span class="material-symbols-outlined text-danger text-lg sm:text-xl">warning</span>
                </div>
                <div class="ml-3 sm:ml-4 text-left w-full">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">Confirm
                        Deletion</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400" id="modal_message">Are you sure you want to delete this Post?
                            This action cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 sm:mt-5 flex flex-col sm:flex-row sm:flex-row-reverse gap-2 sm:gap-3">
                <button id="Confirm_Delete_Button" onclick="Confirm_Delete()"
                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors"
                    type="button" data-post-id="">
                    Delete
                </button>
                <button onclick="Hide_Modal()"
                    class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 text-sm font-medium transition-colors"
                    type="button">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Simple Notification Message Box -->
    <div id="Message_Box"
        class="fixed top-4 right-4 p-3 sm:p-4 rounded-lg text-white shadow-lg transition-opacity duration-300 opacity-0 pointer-events-none z-50 max-w-xs sm:max-w-sm text-sm sm:text-base">
    </div>

    <script>
        let currentPostId = null; // Store post ID globally

        function Show_Message(message, type) {
            const box = document.getElementById('Message_Box');
            box.textContent = message;
            box.classList.remove('opacity-0', 'bg-green-600', 'bg-red-600');
            box.classList.add('opacity-100');

            if (type === 'success') {
                box.classList.add('bg-green-600');
            } else {
                box.classList.add('bg-red-600');
            }

            setTimeout(() => {
                box.classList.remove('opacity-100');
                box.classList.add('opacity-0');
            }, 4000);
        }

        function show_Modal(post_id) {
            const modal = document.getElementById('Modal_Box');
            const modal_message = document.getElementById('modal_message');

            modal_message.textContent = `Are you sure you want to delete Post ID ${post_id}? This action cannot be undone.`;
            currentPostId = post_id;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        async function Confirm_Delete() {
            if (!currentPostId) {
                Show_Message("Error: No post selected for deletion.", 'error');
                Hide_Modal();
                return;
            }

            const idToDelete = currentPostId;
            Hide_Modal();

            try {
                // 1. SENDING THE AJAX REQUEST
                const formData = new URLSearchParams();
                formData.append('post_id', idToDelete);
                formData.append('action', 'delete');

                const response = await fetch('List_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: formData.toString()
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                // 2. RECEIVING AND HANDLING THE RESPONSE
                const result = await response.json();

                if (result.success) {
                    Show_Message(`Post ID ${idToDelete} successfully deleted. ${result.message && result.message.includes('SIMULATED') ? '(Simulated)' : ''}`, 'success');
                    // Remove the post from UI
                    removePostFromUI(idToDelete);
                } else {
                    Show_Message(`Deletion failed: ${result.error || 'Unknown server error'}`, 'error');
                }

            } catch (error) {
                console.error("Deletion failed:", error);
                Show_Message(`Network error or failed to process request.`, 'error');
            }
        }

        function Hide_Modal() {
            const modal = document.getElementById('Modal_Box');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            currentPostId = null;
        }

        // Function to remove post from UI after deletion
        function removePostFromUI(postId) {
            // Remove desktop row
            const desktopRow = document.querySelector(`[data-post-id="${postId}"]`);
            if (desktopRow) desktopRow.remove();
            
            // Remove mobile card
            const mobileCards = document.querySelectorAll('.mobile-card');
            mobileCards.forEach(card => {
                const deleteBtn = card.querySelector(`[onclick*="${postId}"]`);
                if (deleteBtn) card.remove();
            });
            
            // Check if all posts are deleted
            const remainingPosts = document.querySelectorAll('[data-post-id], .mobile-card');
            if (remainingPosts.length === 0) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }

        // Live Search Implementation
        let searchTimeout;

        document.getElementById('searchInput').addEventListener('input', function (e) {
            const searchTerm = e.target.value.trim();

            clearTimeout(searchTimeout);

            if (searchTerm === '') {
                document.getElementById('resultsContainer').innerHTML = '';
                return;
            }

            // Show loading
            document.getElementById('resultsContainer').innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">Searching...</div>';

            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
            }, 300);
        });

        async function performSearch(searchTerm) {
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `search_action=live_search&search_term=${encodeURIComponent(searchTerm)}`
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const results = await response.json();
                displaySearchResults(results, searchTerm);

            } catch (error) {
                console.error('Search error:', error);
                document.getElementById('resultsContainer').innerHTML = '<div class="p-4 text-center text-red-500 text-sm">Search failed</div>';
            }
        }

        function displaySearchResults(results, searchTerm) {
            const container = document.getElementById('resultsContainer');

            if (!results || results.length === 0) {
                container.innerHTML = '<div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">No posts found</div>';
                return;
            }

            // Show mobile layout for search results on mobile
            if (window.innerWidth < 768) {
                const mobileResults = results.map(post => {
                    const snippet = post.content ? post.content.substring(0, 80) + '...' : '';
                    const date = new Date(post.date_posted).toLocaleDateString();
                    
                    return `
                        <div class="mobile-search-result bg-white dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="font-medium text-gray-900 dark:text-white mb-1">
                                ${highlightMatch(post.title, searchTerm)}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mb-2 line-clamp-2">
                                ${highlightMatch(snippet, searchTerm)}
                            </div>
                            <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                                <span>By ${highlightMatch(post.published_by, searchTerm)}</span>
                                <span>${date}</span>
                            </div>
                            <div class="mt-3">
                                <button onclick="show_Modal(${post.post_id})"
                                    class="flex items-center justify-center gap-1 w-full px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/30 rounded text-sm">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Delete
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
                
                container.innerHTML = `<div class="space-y-3">${mobileResults}</div>`;
            } else {
                // Desktop table for search results
                const tableHTML = `
                    <div class="mt-2 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Title</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Content</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Author</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                ${results.map(post => `
                                    <tr data-post-id="${post.post_id}">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-white">
                                            ${highlightMatch(post.title, searchTerm)}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            ${highlightMatch(post.content ? post.content.substring(0, 50) + '...' : '', searchTerm)}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            ${highlightMatch(post.published_by, searchTerm)}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                            ${highlightMatch(new Date(post.date_posted).toLocaleDateString(), searchTerm)}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <button class="text-danger hover:text-danger/80" onclick="show_Modal(${post.post_id})">
                                                <span class="material-symbols-outlined text-lg">delete</span>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
                container.innerHTML = tableHTML;
            }
        }

        function highlightMatch(text, searchTerm) {
            if (!searchTerm || !text) return text;
            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="bg-yellow-200 dark:bg-yellow-800">$1</span>');
        }
    </script>

    <?php include 'Nav_script.php'; ?>
</body>

</html>