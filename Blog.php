<?php
include 'Backend/Config.php';
session_start();
require_once 'Backend/track_visits.php';

if (!isset($conn)) {
  die("Error: Database connection object (\$conn) is not available. Please include your connection file.");
}

// Handle AJAX search requests
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['search_action']) && $_POST['search_action'] === 'live_search') {
  header('Content-Type: application/json');

  try {
    $searchTerm = trim($_POST['search_term'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (empty($searchTerm) && empty($category)) {
      echo json_encode([]);
      exit();
    }

    $sql = "SELECT bp.post_id, bp.title, bp.content, bp.image_path, bp.date_posted, bp.Categories, bp.published_by, GROUP_CONCAT(pv.video_path) as video_paths 
                FROM blog_post bp
                LEFT JOIN post_videos pv ON bp.post_id = pv.post_id 
                WHERE 1=1";

    $params = [];
    $types = '';

    if (!empty($searchTerm)) {
      $sql .= " AND (title LIKE ? OR content LIKE ? OR published_by LIKE ?)";
      $searchPattern = '%' . $searchTerm . '%';
      $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern]);
      $types .= 'sss';
    }

    if (!empty($category) && $category !== 'all') {
      $sql .= " AND Categories = ?";
      $params[] = $category;
      $types .= 's';
    }

    $sql .= " GROUP BY bp.post_id ORDER BY bp.date_posted DESC LIMIT 20";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
      $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
      $posts[] = $row;
    }

    echo json_encode($posts);
    exit();

  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit();
  }
}

// Get all unique categories for filter buttons
$categories = [];
$category_query = "SELECT DISTINCT Categories FROM blog_post WHERE Categories IS NOT NULL AND Categories != ''";
$category_result = mysqli_query($conn, $category_query);
if ($category_result) {
  while ($row = mysqli_fetch_assoc($category_result)) {
    $categories[] = $row['Categories'];
  }
}

// Check if we have a category filter in URL
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Pagination setup with category filter
$records_per_page = 12;

// Build base query
$base_query = "SELECT COUNT(*) AS total FROM blog_post WHERE 1=1";
$count_query = "SELECT COUNT(*) AS total FROM blog_post WHERE 1=1";
$data_query = "SELECT post_id, title, content, image_path, date_posted, Categories, published_by FROM blog_post WHERE 1=1";

// Add category filter if specified
if ($selected_category !== 'all') {
  $count_query .= " AND Categories = ?";
  $data_query .= " AND Categories = ?";
}

$count_query .= " ORDER BY date_posted DESC";
$data_query .= " ORDER BY date_posted DESC LIMIT ? OFFSET ?";

// Get total count
if ($stmt = $conn->prepare($count_query)) {
  if ($selected_category !== 'all') {
    $stmt->bind_param("s", $selected_category);
  }
  $stmt->execute();
  $count_result = $stmt->get_result();
  $total_rows = $count_result->fetch_assoc()['total'];
  $total_pages = ceil($total_rows / $records_per_page);
  $stmt->close();
} else {
  $total_rows = 0;
  $total_pages = 1;
}

$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($current_page < 1)
  $current_page = 1;
if ($current_page > $total_pages)
  $current_page = $total_pages;

$offset = ($current_page - 1) * $records_per_page;

$posts = [];
$fetch_error = null;

// Fetch posts with pagination and category filter
function getPublishedPosts($conn, $limit = 12, $offset = 0, $category = 'all')
{
  $sql = "SELECT bp.post_id, bp.title, bp.content, bp.image_path, bp.date_posted, bp.Categories, bp.published_by, GROUP_CONCAT(pv.video_path) as video_paths 
            FROM blog_post bp
            LEFT JOIN post_videos pv ON bp.post_id = pv.post_id 
            WHERE 1=1";

  if ($category !== 'all') {
    $sql .= " AND bp.Categories = ?";
  }

  $sql .= " GROUP BY bp.post_id ORDER BY bp.date_posted DESC LIMIT ? OFFSET ?";

  $posts = [];

  if ($stmt = $conn->prepare($sql)) {
    if ($category !== 'all') {
      $stmt->bind_param("sii", $category, $limit, $offset);
    } else {
      $stmt->bind_param("ii", $limit, $offset);
    }

    if ($stmt->execute()) {
      $result = $stmt->get_result();

      while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
      }
      $stmt->close();
    } else {
      error_log("Failed to execute post fetch query: " . $stmt->error);
    }
  } else {
    error_log("Failed to prepare post fetch statement: " . $conn->error);
  }

  return $posts;
}

// Fetch posts
$posts = getPublishedPosts($conn, 12, $offset, $selected_category);

if (empty($posts) && $conn->error) {
  $fetch_error = "Could not retrieve posts due to a database error.";
} elseif (empty($posts)) {
  $fetch_error = $selected_category !== 'all' ?
    "No blog posts found in the '{$selected_category}' category." :
    "No blog posts have been published yet.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="./style.css" rel="stylesheet">
  <title>Blog</title>

  <link rel="icon" href="Assets/img/logo bg.png" type="image/x-icon">
  <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="16x16">
  <link rel="icon" href="Assets/img/logo bg.png" type="image/png" sizes="32x32">
  <link rel="apple-touch-icon" href="Assets/img/logo bg.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="theme-color" content="#1f9c7b">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">

  <style>
    .search-loading {
      display: none;
    }

    .search-loading.active {
      display: block;
    }

    .search-results-container {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: white;
      border: 1px solid #e5e7eb;
      border-radius: 0.5rem;
      margin-top: 0.5rem;
      max-height: 400px;
      overflow-y: auto;
      z-index: 50;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      display: none;
    }

    .search-results-container.active {
      display: block;
    }

    .search-result-item {
      padding: 1rem;
      border-bottom: 1px solid #f3f4f6;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .search-result-item:hover {
      background-color: #f9fafb;
    }

    .search-result-item:last-child {
      border-bottom: none;
    }

    .category-active {
      background-color: #1d4ed8 !important;
      color: white !important;
    }

    .search-result-image {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 0.375rem;
    }
  </style>
</head>

<body class="bg-gray-100 font-(family-name:public sans)">

  <?php include 'Header.php'; ?>

  <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
    <div class="layout-container flex h-full grow flex-col">
      <div class="px-4 md:px-10 lg:px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col max-w-[960px] flex-1">

          <main class="flex-1">
            <div class="@container">
              <div class="@[480px]:p-4">
                <div
                  class="flex min-h-[400px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-lg items-center justify-center p-4"
                  data-alt="Abstract gradient background from dark blue to purple"
                  style='background-image: linear-gradient(rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%), url("Assets/img/Cover 3.jpg");'>
                  <div class="flex flex-col gap-2 text-center">
                    <h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl">
                      Insights &amp; News</h1>
                    <h2 class="text-white/90 text-sm font-normal leading-normal @[480px]:text-base">
                      Stay updated with
                      our latest articles and announcements.</h2>
                  </div>
                </div>
              </div>
            </div>

            <!-- Search Section -->
            <div class="px-4 py-6 relative">
              <form id="searchForm" class="relative">
                <label class="flex flex-col min-w-40 h-12 w-full">
                  <div class="flex w-full flex-1 items-stretch rounded-lg h-full border-2 border-blue-200">
                    <div
                      class="text-gray-500 flex border-solid bg-white items-center justify-center pl-4 rounded-l-lg border-r-0">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd"
                          d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                          clip-rule="evenodd" />
                      </svg>
                    </div>
                    <input
                      class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden text-gray-800 focus:outline-2 focus:ring-1 bg-white focus:border-1 h-full placeholder:text-gray-600 px-4 border-l-0 pl-2 text-base font-normal leading-normal"
                      placeholder="Search for articles by title, content, or author..." value="" id="searchInput" />
                    <div class="items-center justify-center">
                      <button type="button" id="searchButton"
                        class="min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center h-11 px-4 hover:bg-blue-400 bg-blue-600 text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
                        Search
                      </button>
                    </div>
                  </div>
                </label>

                <!-- Loading Spinner -->
                <div id="searchLoading" class="search-loading absolute right-32 top-1/2 transform -translate-y-1/2">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                </div>

                <!-- Search Results Container -->
                <div id="searchResults" class="search-results-container"></div>
              </form>
            </div>

            <!-- Category Filters -->
            <div class="flex gap-3 p-3 flex-wrap pr-4" id="categoryFilters">
              <a href="?category=all&page=1"
                class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 <?php echo $selected_category === 'all' ? 'bg-blue-600 text-white category-active' : 'bg-white hover:bg-blue-200 text-gray-800'; ?> transition-colors">
                <p class="text-sm font-medium leading-normal">All</p>
              </a>
              <?php foreach ($categories as $category): ?>
                <a href="?category=<?php echo urlencode($category); ?>&page=1"
                  class="flex h-8 shrink-0 items-center justify-center gap-x-2 rounded-full px-4 <?php echo $selected_category === $category ? 'bg-blue-600 text-white category-active' : 'bg-white hover:bg-blue-200 text-gray-800'; ?> transition-colors">
                  <p class="text-sm font-medium leading-normal"><?php echo htmlspecialchars($category); ?></p>
                </a>
              <?php endforeach; ?>
            </div>

            <!-- Posts Grid -->
            <div id="postsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-2">
              <?php if ($fetch_error): ?>
                <div class="col-span-full bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg"
                  role="alert">
                  <p class="font-bold">Notice</p>
                  <p><?php echo htmlspecialchars($fetch_error); ?></p>
                  <?php if ($selected_category !== 'all'): ?>
                    <a href="?category=all&page=1" class="text-blue-600 hover:underline text-sm mt-2 inline-block">
                      View all posts →
                    </a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <?php foreach ($posts as $post):
                // Decode image JSON string
                $image_paths = json_decode($post['image_path'], true) ?: [];
                $image_url = !empty($image_paths) ? htmlspecialchars($image_paths[0]) : 'https://placehold.co/600x400/f3f4f6/374151?text=No+Image';

                // Truncate content for a snippet
                $snippet = substr(strip_tags($post['content']), 0, 100) . (strlen(strip_tags($post['content'])) > 100 ? '...' : '');

                // Format date
                $formatted_date = date("M j, Y", strtotime($post['date_posted']));
                // Decode video paths
                $video_paths = !empty($post['video_paths']) ? explode(',', $post['video_paths']) : [];
                $video_url = !empty($video_paths) ? htmlspecialchars($video_paths[0]) : '';
                ?>
                <div
                  class="flex flex-col gap-3 pb-3 rounded-lg overflow-hidden bg-white shadow-sm hover:shadow-lg transition-shadow duration-300 group">
                  <!-- Media Area -->
                  <div class="w-full bg-center bg-no-repeat h-64 bg-cover bg-gray-100 relative">
                    <?php if (!empty($image_paths)): ?>
                      <div class="w-full h-full bg-center bg-no-repeat bg-cover"
                        style='background-image: url("<?php echo $image_url; ?>");'
                        data-alt="<?php echo htmlspecialchars($post['title']); ?>">
                      </div>
                    <?php elseif ($video_url): ?>
                      <video src="<?php echo $video_url; ?>" controls class="w-full h-full object-cover"></video>
                    <?php else: ?>
                      <div class="w-full h-full bg-center bg-no-repeat bg-cover"
                        style='background-image: url("<?php echo $image_url; ?>");'
                        data-alt="<?php echo htmlspecialchars($post['title']); ?>">
                      </div>
                    <?php endif; ?>
                  </div>
                  <!-- Content Area -->
                  <div class="p-4 flex flex-col flex-1">
                    <?php if ($post['Categories']): ?>
                      <p class="text-blue-600 text-sm font-medium">
                        <?php echo htmlspecialchars($post['Categories']); ?>
                      </p>
                    <?php endif; ?>
                    <h3 class="text-lg font-bold leading-tight text-blue-600 mt-1">
                      <?php echo htmlspecialchars($post['title']); ?>
                    </h3>
                    <p class="text-gray-600 text-sm font-normal leading-normal mt-2 flex-1">
                      <?php echo htmlspecialchars($snippet); ?>
                    </p>

                    <div class="mt-2">
                      <p class="text-gray-500 text-xs font-normal">
                        <?php echo 'Written By: ' . htmlspecialchars($post['published_by']); ?>
                      </p>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                      <p class="text-gray-500 text-xs font-normal">
                        <?php echo $formatted_date; ?>
                      </p>
                      <a class="text-blue-600 font-bold text-sm group-hover:underline"
                        href="post.php?id=<?php echo htmlspecialchars($post['post_id']); ?>">
                        Read More →
                      </a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
              <div class="flex justify-center items-center gap-2 p-4 mt-8">
                <?php
                $prev_page = $current_page - 1;
                if ($current_page > 1) {
                  echo "<a href='?category=" . urlencode($selected_category) . "&page=$prev_page'>
                  <button class='px-3 py-1 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-200 transition-colors'>
                  Previous
                  </button>
                  </a>";
                } else {
                  echo "<button class='px-3 py-1 rounded-md text-sm font-medium bg-gray-200 text-gray-500 cursor-not-allowed'
                  disabled=''>Previous</button>";
                }

                // Show limited pagination
                $max_visible_pages = 5;
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $start_page + $max_visible_pages - 1);
                $start_page = max(1, $end_page - $max_visible_pages + 1);

                for ($i = $start_page; $i <= $end_page; $i++) {
                  if ($i == $current_page) {
                    echo "<button class='w-8 h-8 rounded-md text-sm font-medium bg-blue-600 text-white'>$i</button>";
                  } else {
                    echo "<a href='?category=" . urlencode($selected_category) . "&page=$i'>
                    <button class='w-8 h-8 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-200 transition-colors'>$i</button>
                    </a>";
                  }
                }

                $next_page = $current_page + 1;
                if ($current_page < $total_pages) {
                  echo "<a href='?category=" . urlencode($selected_category) . "&page=$next_page'>
                  <button class='px-3 py-1 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-200 transition-colors'>Next</button>
                  </a>";
                }
                ?>
              </div>
            <?php endif; ?>
          </main>

        </div>
      </div>
    </div>
  </div>
  <?php include 'Footer.php'; ?>

  <script>
    let searchTimeout;
    let currentSearchTerm = '';
    let currentCategory = '<?php echo $selected_category; ?>';

    // Elements
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');
    const searchLoading = document.getElementById('searchLoading');
    const postsGrid = document.getElementById('postsGrid');
    const categoryFilters = document.getElementById('categoryFilters');

    // Perform search function
    async function performSearch(searchTerm, category = 'all') {
      if (!searchTerm.trim() && category === 'all') {
        searchResults.classList.remove('active');
        return;
      }

      searchLoading.classList.add('active');
      searchResults.classList.add('active');
      searchResults.innerHTML = '<div class="p-4 text-center text-gray-500">Searching...</div>';

      try {
        const formData = new FormData();
        formData.append('search_action', 'live_search');
        formData.append('search_term', searchTerm);
        formData.append('category', category);

        const response = await fetch('', {
          method: 'POST',
          body: formData
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const results = await response.json();

        if (results.error) {
          searchResults.innerHTML = `<div class="p-4 text-red-500 text-center">${results.error}</div>`;
          return;
        }

        displaySearchResults(results, searchTerm, category);

      } catch (error) {
        console.error('Search error:', error);
        searchResults.innerHTML = '<div class="p-4 text-red-500 text-center">Search failed. Please try again.</div>';
      } finally {
        searchLoading.classList.remove('active');
      }
    }

    // Display search results
    function displaySearchResults(results, searchTerm, category) {
      if (!results || results.length === 0) {
        const noResultsText = !searchTerm.trim() && category !== 'all' ?
          `No posts found in "${category}" category` :
          'No posts found matching your search';
        searchResults.innerHTML = `<div class="p-4 text-center text-gray-500">${noResultsText}</div>`;
        return;
      }

      const resultsHTML = results.map(post => {
        // Decode image
        let imageUrl = 'https://placehold.co/600x400/f3f4f6/374151?text=No+Image';
        try {
          const images = JSON.parse(post.image_path);
          if (images && images.length > 0) {
            imageUrl = images[0];
          }
        } catch (e) {
          // Use default image
        }

        // Truncate content
        const content = post.content ? stripHtml(post.content).substring(0, 80) + '...' : '';
        const date = new Date(post.date_posted).toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric'
        });

        // Highlight search term
        const highlightedTitle = highlightText(post.title, searchTerm);
        const highlightedContent = highlightText(content, searchTerm);
        const highlightedAuthor = highlightText(post.published_by, searchTerm);

        return `
          <a href="post.php?id=${post.post_id}" class="search-result-item block">
            <div class="flex gap-3">
              <div class="flex-shrink-0">
                <img src="${imageUrl}" alt="${post.title}" class="search-result-image">
              </div>
              <div class="flex-1">
                <div class="flex justify-between items-start">
                  <h4 class="font-semibold text-blue-600">${highlightedTitle}</h4>
                  <span class="text-xs text-gray-500">${date}</span>
                </div>
                ${post.Categories ? `<p class="text-xs text-blue-600 mt-1">${post.Categories}</p>` : ''}
                <p class="text-sm text-gray-600 mt-1">${highlightedContent}</p>
                <p class="text-xs text-gray-500 mt-2">By ${highlightedAuthor}</p>
              </div>
            </div>
          </a>
        `;
      }).join('');

      searchResults.innerHTML = resultsHTML;
    }

    // Utility functions
    function stripHtml(html) {
      const div = document.createElement('div');
      div.innerHTML = html;
      return div.textContent || div.innerText || '';
    }

    function highlightText(text, searchTerm) {
      if (!searchTerm || !text) return text;
      const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
      return text.replace(regex, '<span class="bg-yellow-200 px-1 rounded">$1</span>');
    }

    // Event listeners
    searchInput.addEventListener('input', function (e) {
      currentSearchTerm = e.target.value.trim();
      clearTimeout(searchTimeout);

      if (currentSearchTerm.length < 2 && currentCategory === 'all') {
        searchResults.classList.remove('active');
        return;
      }

      searchTimeout = setTimeout(() => {
        performSearch(currentSearchTerm, currentCategory);
      }, 300);
    });

    searchButton.addEventListener('click', function () {
      performSearch(currentSearchTerm, currentCategory);
    });

    searchInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performSearch(currentSearchTerm, currentCategory);
      }
    });

    // Close search results when clicking outside
    document.addEventListener('click', function (e) {
      if (!e.target.closest('#searchForm') && !e.target.closest('#searchResults')) {
        searchResults.classList.remove('active');
      }
    });

    // Category filter click handlers
    categoryFilters.addEventListener('click', function (e) {
      const categoryLink = e.target.closest('a[href*="category="]');
      if (categoryLink) {
        // Update active category
        document.querySelectorAll('#categoryFilters a').forEach(link => {
          link.classList.remove('bg-blue-600', 'text-white', 'category-active');
          link.classList.add('bg-white', 'hover:bg-blue-200', 'text-gray-800');
        });

        categoryLink.classList.remove('bg-white', 'hover:bg-blue-200', 'text-gray-800');
        categoryLink.classList.add('bg-blue-600', 'text-white', 'category-active');

        // Extract category from href
        const href = categoryLink.getAttribute('href');
        const match = href.match(/category=([^&]+)/);
        if (match) {
          currentCategory = decodeURIComponent(match[1]);

          // If there's a search term, perform search with new category
          if (currentSearchTerm.trim().length >= 2) {
            performSearch(currentSearchTerm, currentCategory);
          }
        }
      }
    });

    // Clear search when category changes
    document.querySelectorAll('#categoryFilters a').forEach(link => {
      link.addEventListener('click', function () {
        searchInput.value = '';
        currentSearchTerm = '';
        searchResults.classList.remove('active');
      });
    });
  </script>
</body>

</html>