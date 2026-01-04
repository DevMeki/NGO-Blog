<?php
include 'Backend/Config.php'; // Ensure this path is correct
session_start();
require_once 'Backend/track_visits.php';

if (!isset($conn)) {
    die("Error: Database connection object (\$conn) is not available. Please include your connection file.");
}

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$post = null;
$error_message = null;

$formatted_date = "";
$read_time = "";
$hero_image_url = "";
$other_images = [];
$page_title = "Post Not Found | Blog Post";
$published_by = "";

if ($post_id > 0) {
    $escaped_post_id = mysqli_real_escape_string($conn, $post_id);
    $query = "SELECT * FROM blog_post WHERE post_id = '$escaped_post_id' LIMIT 1";
    $query_run = mysqli_query($conn, $query);

    if ($query_run && mysqli_num_rows($query_run) > 0) {
        $post = mysqli_fetch_assoc($query_run);

        // --- Process Post Data ---
        $image_paths = json_decode($post['Image_path'] ?? '[]', true) ?: [];

        if (!empty($image_paths) && is_array($image_paths)) {
            $hero_image_url = htmlspecialchars($image_paths[0]);
            $other_images = array_slice($image_paths, 1);
        } else {
            $hero_image_url = 'https://placehol.co/1200x600/1f9c7b/ffffff?text=' . urlencode(htmlspecialchars($post['title'] ?? 'No Title'));
            $other_images = [];
        }

        $formatted_date = date('F j, Y', strtotime($post['Date_posted']));
        $page_title = $post['Title'] . " | Blog Post";
        $read_time = "7 min read";
        $published_by = $post['published_by'];

        // --- Fetch Videos ---
        $video_paths = [];
        $v_sql = "SELECT video_path FROM post_videos WHERE post_id = ?";
        if ($v_stmt = $conn->prepare($v_sql)) {
            $v_stmt->bind_param("i", $post_id);
            if ($v_stmt->execute()) {
                $v_result = $v_stmt->get_result();
                while ($row = $v_result->fetch_assoc()) {
                    $video_paths[] = $row['video_path'];
                }
            }
            $v_stmt->close();
        }

    } else {
        $error_message = 'Post with ID ' . htmlspecialchars($post_id) . " not found";
    }
} else {
    $error_message = "Invalid or missing post ID.";
}


$posts = [];
$fetch_error = null;

/**
 * @param mysqli $conn 
 * @param int $limit The maximum number of posts to return.
 * @param int $offset The starting offset for pagination.
 * @return array 
 */
function getPublishedPosts($conn, $limit = 5, $offset = 0)
{
    $sql = "SELECT post_id, Title, Date_posted FROM blog_post ORDER BY date_posted DESC LIMIT ? OFFSET ?";

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


// --- Fetching Logic ---
// Fetch the first 12 posts
$posts = getPublishedPosts($conn, 5, 0);

if (empty($posts) && $conn->error) {
    $fetch_error = "Could not retrieve posts due to a database error.";
} elseif (empty($posts)) {
    $fetch_error = "No blog posts have been published yet.";
}

?>

<!DOCTYPE html>

<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <!-- Load Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link href="./style.css" rel="stylesheet">
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <link rel="icon" href="./Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="./Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="./Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="./Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#1f9c7b">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">

</head>

<body class="bg-background-light font-display text-[#111418]">
    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">

        <?php
        include 'Header.php';
        ?>

        <main class="container mx-auto px-4 py-8 lg:py-12">
            <div class="max-w-4xl mx-auto">

                <?php if ($error_message): ?>
                    <!-- Error Message Display -->
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg my-10" role="alert">
                        <p class="font-bold">Error</p>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                        <div class="mt-4">
                            <a href="Blog.php" class="text-sm font-medium text-red-700 hover:text-red-900 underline">
                                &larr; Back to Blog posts
                            </a>
                        </div>
                    </div>

                <?php elseif ($post): ?>

                    <!-- Breadcrumbs -->
                    <div class="flex flex-start gap-2 mb-6">
                        <a class="text-[#5f758c] hover:text-blue-600 text-base font-medium leading-normal"
                            href="home.php">Home</a>
                        <span class="text-[#5f758c] text-base font-medium leading-normal">/</span>
                        <a class="text-[#5f758c] hover:text-blue-600 text-base font-medium leading-normal"
                            href="blog.php">Blog</a>
                        <span class="text-[#5f758c] text-base font-medium leading-normal">/</span>
                        <span class="text-[#111418] text-base font-medium leading-normal truncate">
                            <?php echo htmlspecialchars($post['Title']); ?>
                        </span>
                    </div>
                </div>

                <div class="flex flex-col lg:flex-row gap-12 max-w-7xl mx-auto">
                    <!-- Main Article Column -->
                    <div class="w-full lg:w-2/3">
                        <article class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <!-- Article Header -->
                            <div class="p-6 md:p-8">
                                <h1
                                    class="text-[#111418] tracking-tight text-3xl md:text-4xl font-bold leading-tight text-left pb-3">
                                    <?php echo htmlspecialchars($post['Title']); ?>
                                </h1>
                                <p class="text-[#5f758c] text-sm font-normal leading-normal">Published on
                                    <?php echo $formatted_date; ?> •
                                    By <span><?php echo $published_by; ?></span> •
                                    <?php echo $read_time; ?>
                                </p>
                            </div>
                            <!-- Featured Image (hero image) -->
                            <!-- Hero Media (Image or Video) -->
                            <div class="w-full bg-center bg-no-repeat bg-cover flex flex-col justify-end overflow-hidden h-[400px] md:h-[500px] relative bg-black"
                                data-alt="<?php echo htmlspecialchars($post['Title']); ?>">
                                <?php if (!empty($image_paths)): ?>
                                    <a href="<?php echo $hero_image_url; ?>" class="lightbox-trigger block w-full h-full">
                                        <img src="<?php echo $hero_image_url; ?>"
                                            class="w-full h-full object-contain rounded-lg transition duration-300 hover:opacity-90 cursor-pointer"
                                            alt="<?php echo htmlspecialchars($post['Title']); ?>">
                                    </a>
                                <?php elseif (!empty($video_paths)): ?>
                                    <video src="<?php echo htmlspecialchars($video_paths[0]); ?>" controls
                                        class="w-full h-full object-contain rounded-lg"></video>
                                <?php else: ?>
                                    <a href="<?php echo $hero_image_url; ?>" class="lightbox-trigger block w-full h-full">
                                        <img src="<?php echo $hero_image_url; ?>"
                                            class="w-full h-full object-contain rounded-lg transition duration-300 hover:opacity-90 cursor-pointer"
                                            alt="<?php echo htmlspecialchars($post['Title']); ?>">
                                    </a>
                                <?php endif; ?>
                            </div>
                            <!-- Article Body -->
                            <div class="p-6 md:p-8 prose prose-lg max-w-none text-[#333]">
                                <?php echo $post['Content']; ?>
                                <p class="mt-2">Author: <?php echo $published_by; ?></p>
                            </div>

                            <?php
                            // Determine which videos to show in the gallery
                            $gallery_videos = [];
                            if (!empty($image_paths)) {
                                // If hero was an image, show ALL videos
                                $gallery_videos = $video_paths;
                            } elseif (!empty($video_paths)) {
                                // If hero was the first video, show the rest
                                $gallery_videos = array_slice($video_paths, 1);
                            }
                            ?>

                            <!-- Video Gallery -->
                            <?php if (!empty($gallery_videos)): ?>
                                <div class="px-6 md:px-8 mt-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-2xl font-bold mb-6 text-[#111418]">Videos</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <?php foreach ($gallery_videos as $vid_path): ?>
                                            <div
                                                class="rounded-lg overflow-hidden shadow-lg border border-gray-100 h-[300px] bg-black">
                                                <video src="<?php echo htmlspecialchars($vid_path); ?>" controls
                                                    class="w-full h-full object-contain rounded-lg"></video>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Image Gallery -->
                            <?php if (!empty($other_images)): ?>
                                <div class="px-6 md:px-8 mt-4 pt-8 border-t border-gray-100">
                                    <h3 class="text-2xl font-bold mb-6 text-[#111418]">Additional Imagery</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                        <?php foreach ($other_images as $image_url): ?>
                                            <div
                                                class="rounded-lg overflow-hidden shadow-lg border border-gray-100 h-[300px] bg-black">
                                                <a href="<?php echo htmlspecialchars($image_url); ?>" class="lightbox-trigger block w-full h-full">
                                                    <img src="<?php echo htmlspecialchars($image_url); ?>"
                                                        class="w-full h-full object-contain rounded-lg transition duration-300 hover:opacity-90 cursor-pointer"
                                                        onerror="this.onerror = null; this.src='https://placehold.co/600x400/cccccc/333333?text=Image+Unavailable';" />
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Social Share -->
                            <div class="px-6 md:px-8 pb-8">
                                <!-- Social Share -->
                                <div class="flex items-center gap-4 py-6 border-t border-b border-gray-200">
                                    <span class="text-sm font-semibold text-gray-600">Share this post:</span>
                                    <div class="flex gap-2">
                                        <!-- Twitter/X -->
                                        <a class="flex items-center justify-center size-10 rounded-full bg-gray-100 hover:bg-blue-500 hover:text-white transition-colors duration-200"
                                            data-alt="Share on Twitter/X"
                                            href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['Title'] ?? 'Check out this post'); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="bi bi-twitter-x text-lg"></i>
                                        </a>

                                        <!-- Facebook -->
                                        <a class="flex items-center justify-center size-10 rounded-full bg-gray-100 hover:bg-blue-600 hover:text-white transition-colors duration-200"
                                            data-alt="Share on Facebook"
                                            href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="bi bi-facebook text-lg"></i>
                                        </a>

                                        <!-- LinkedIn -->
                                        <a class="flex items-center justify-center size-10 rounded-full bg-gray-100 hover:bg-blue-700 hover:text-white transition-colors duration-200"
                                            data-alt="Share on LinkedIn"
                                            href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <i class="bi bi-linkedin text-lg"></i>
                                        </a>

                                        <!-- WhatsApp -->
                                        <button class="flex items-center justify-center size-10 rounded-full bg-gray-100 hover:bg-green-500 hover:text-white transition-colors duration-200 whatsapp-share-btn"
                                                data-alt="Share on WhatsApp"
                                                type="button">
                                            <i class="bi bi-whatsapp text-lg"></i>
                                        </button>

                                        <!-- Copy Link -->
                                        <button class="flex items-center justify-center size-10 rounded-full bg-gray-100 hover:bg-gray-600 hover:text-white transition-colors duration-200 copy-link-btn"
                                                data-alt="Copy Link"
                                                type="button">
                                            <i class="bi bi-link-45deg text-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Copy Success Message -->
                                <div id="copy-success" class="hidden mt-2 text-sm text-green-600 font-medium">
                                    <i class="bi bi-check-circle-fill mr-1"></i>Link copied to clipboard!
                                </div>
                            </div>
                        </article>
                    </div>
                    <!-- Sidebar Column -->
                    <aside class="w-full lg:w-1/3 space-y-8 lg:sticky lg:top-28 lg:self-start">
                        <!-- Search Bar -->
                        <!-- <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h4 class="text-lg font-bold mb-4 text-[#111418]">Search</h4>
                            <div class="flex w-full flex-1 items-stretch rounded-lg h-full border-2 border-blue-200">
                                <div
                                    class="text-gray-500 flex border-solid bg-white items-center justify-center pl-4 rounded-l-lg border-r-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="size-6">
                                        <path fill-rule="evenodd"
                                            d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                                            clip-rule="evenodd" />
                                    </svg>

                                </div>
                                <input
                                    class="form-input flex h-10 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-gray-800 focus:outline-2 focus:ring-1 bg-white focus:border-1 placeholder:text-gray-600 px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                                    placeholder="Search for articles..." value="" />
                            </div>
                        </div> -->
                        <!-- Categories -->
                        <!-- <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h4 class="text-lg font-bold mb-4 text-[#111418]">Categories</h4>
                            <ul class="space-y-2">
                                <li><a class="text-gray-600 hover:text-blue-600" href="#">Artificial Intelligence</a></li>
                                <li><a class="text-gray-600 hover:text-blue-600" href="#">Design Trends</a></li>
                                <li><a class="text-gray-600 hover:text-blue-600" href="#">User Experience</a></li>
                                <li><a class="text-gray-600 hover:text-blue-600" href="#">Technology</a></li>
                                <li><a class="text-gray-600 hover:text-blue-600" href="#">Future of Work</a></li>
                            </ul>
                        </div> -->
                        <!-- Recent Posts -->
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h4 class="text-lg font-bold mb-4 text-[#111418]">Recent Posts</h4>
                            <?php if ($fetch_error): ?>
                                <div class="col-span-full bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg"
                                    role="alert">
                                    <p class="font-bold">Notice</p>
                                    <p><?php echo htmlspecialchars($fetch_error); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php foreach ($posts as $post):
                                $post_title = htmlspecialchars($post['Title']);
                                $post_date = date('F j, Y', strtotime($post['Date_posted']));
                                // $post_link = "post.php?id=" . urlencode($post['post_id']);
                                ?>
                                <div class="space-y-4">
                                    <a class="block group border-amber-600"
                                        href="post.php?id=<?php echo htmlspecialchars($post['post_id']); ?>">
                                        <p class="font-semibold text-gray-800 group-hover:text-blue-600">
                                            <?php echo $post_title ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo $post_date; ?>
                                        </p>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </aside>
                </div>
            <?php endif; // Close the if ($post) block ?>
        </main>
        <!-- Footer -->
        <?php
        include 'footer.php';
        ?>

        <!-- Lightbox Modal -->
        <div id="lightbox-modal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="relative max-w-4xl max-h-screen p-4">
                <button id="lightbox-close" class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300 z-60">
                    &times;
                </button>
                <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain">
            </div>
        </div>

        <script>
            // Lightbox functionality
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('lightbox-modal');
                const modalImg = document.getElementById('lightbox-image');
                const closeBtn = document.getElementById('lightbox-close');
                const triggers = document.querySelectorAll('.lightbox-trigger');

                // Open lightbox
                triggers.forEach(trigger => {
                    trigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        const imgSrc = this.getAttribute('href');
                        modalImg.src = imgSrc;
                        modal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden'; // Prevent scrolling
                    });
                });

                // Close lightbox
                closeBtn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto'; // Restore scrolling
                });

                // Close lightbox when clicking outside the image
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = 'auto'; // Restore scrolling
                    }
                });

                // Close lightbox with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                        document.body.style.overflow = 'auto'; // Restore scrolling
                    }
                });

                // Social Sharing Functionality
                const postUrl = window.location.href;
                const postTitle = <?php echo json_encode($post['Title'] ?? 'Check out this post'); ?>;

                // WhatsApp Share Button
                const whatsappBtn = document.querySelector('.whatsapp-share-btn');
                if (whatsappBtn) {
                    whatsappBtn.addEventListener('click', async function() {
                        try {
                            // Copy link to clipboard first
                            await navigator.clipboard.writeText(postUrl);

                            // Show success message
                            showCopySuccess();

                            // Open WhatsApp with pre-filled message
                            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(postTitle + ' - ' + postUrl)}`;
                            window.open(whatsappUrl, '_blank');
                        } catch (err) {
                            console.error('Failed to copy link: ', err);
                            // Fallback: just open WhatsApp
                            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(postTitle + ' - ' + postUrl)}`;
                            window.open(whatsappUrl, '_blank');
                        }
                    });
                }

                // Copy Link Button
                const copyBtn = document.querySelector('.copy-link-btn');
                if (copyBtn) {
                    copyBtn.addEventListener('click', async function() {
                        try {
                            await navigator.clipboard.writeText(postUrl);
                            showCopySuccess();
                        } catch (err) {
                            console.error('Failed to copy link: ', err);
                            // Fallback: show alert
                            alert('Link: ' + postUrl);
                        }
                    });
                }

                // Show copy success message
                function showCopySuccess() {
                    const successMsg = document.getElementById('copy-success');
                    if (successMsg) {
                        successMsg.classList.remove('hidden');
                        setTimeout(() => {
                            successMsg.classList.add('hidden');
                        }, 3000); // Hide after 3 seconds
                    }
                }
            });
        </script>
    </div>
</body>

</html>