<?php
session_start();
require_once '../Backend/Config.php';

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

// Initialize variables - REMOVED DUPLICATES
$draft = [];
$draft_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$post_title = $post_content = $Categories = $Tags = $Featured = "";
$post_titleErr = $post_contentErr = $post_imagesErr = $TagErr = $DraftErr = "";
$success_message = "";
$draft_success_message = "";
$error_message = "";

// Fetch draft details if ID is provided
if ($draft_id > 0) {
    try {
        // Fetch draft details
        $sql = "SELECT draft_id, Title, Content, Categories, Tags, Featured 
                FROM draft 
                WHERE draft_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $draft_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $draft = $result->fetch_assoc();

                // Populate form fields with draft data
                $post_title = htmlspecialchars($draft['Title'] ?? '');
                $post_content = htmlspecialchars($draft['Content'] ?? '');
                $Categories = htmlspecialchars($draft['Categories'] ?? '');
                $Tags = htmlspecialchars($draft['Tags'] ?? '');
                $Featured = htmlspecialchars($draft['Featured'] ?? '');
            } else {
                $error = "Draft not found.";
            }
            $stmt->close();
        } else {
            $error = "Failed to prepare statement.";
        }
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch admin username
$username = $_SESSION['username'];

// Check for logout confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_destroy();
    header('Location: Admin_login.php');
    exit();
}

// Check for success messages from backend
if (isset($_GET['success'])) {
    if ($_GET['success'] == 'post') {
        $success_message = "Post published successfully!";
    } elseif ($_GET['success'] == 'draft') {
        $draft_success_message = "Draft saved successfully!";
    }
}

// Check for error messages from backend
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
    if (isset($_GET['type']) && $_GET['type'] == 'post') {
        $post_titleErr = $error_message;
    } elseif (isset($_GET['type']) && $_GET['type'] == 'draft') {
        $DraftErr = $error_message;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../style.css" rel="stylesheet">
    <title>Admin Post Editor</title>

    <link rel="icon" href="../Assets/img/logo bg.png" type="image/x-icon">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="16x16">
    <link rel="icon" href="../Assets/img/logo bg.png" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="../Assets/img/logo bg.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#1f9c7b">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gravitas+One&display=swap" rel="stylesheet">

</head>

<body class="bg-gray-100 font-[Public_Sans,_sans-serif] text-gray-950">

    <div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
        <div class="layout-container flex h-full grow flex-col">
            <header
                class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#dbe0e6] px-3 md:px-10 py-3 bg-white">
                <div class="flex items-center gap-4">
                    <div class="size-6 ">
                        <img src="../Assets/img/logo.png">
                    </div>
                    <h2 class="text-orange-600 text-lg font-bold leading-tight tracking-[-0.015em]">
                        Admin <?php echo htmlspecialchars($username); ?>
                    </h2>
                </div>
                <div class="flex flex-1 justify-end gap-4 md:gap-8">
                    <div class="flex items-center gap-9"><a class="text-sm font-medium leading-normal"
                            href="../Blog.php">Back to Blog</a>
                    </div>
                    <a href="Admin_Dashboard.php">
                        <button
                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-blue-300 text-black text-sm font-bold leading-normal tracking-[0.015em]">
                            <span class="truncate">Dashboard</span>
                        </button>
                    </a>
                    <a href="?confirm=yes">
                        <button
                            class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 text-black text-sm font-bold leading-normal tracking-[0.015em]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                        </button>
                    </a>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                <div class="flex flex-wrap justify-between gap-3 p-4">
                    <div class="flex min-w-72 flex-col gap-3">
                        <p class="text-4xl font-black leading-tight tracking-[-0.033em]">
                            Create New Post</p>
                        <p class="text-gray-500 text-base font-normal leading-normal">Fill in
                            the details below to create a new blog post.</p>
                    </div>
                </div>

                <!-- Success and Error Messages -->
                <?php if (!empty($success_message)): ?>
                    <div class="mx-4 mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($draft_success_message)): ?>
                    <div class="mx-4 mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
                        <?php echo htmlspecialchars($draft_success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="mx-4 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="mx-4 mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- FIXED: Added proper form action and method -->
                    <form class="lg:col-span-2 space-y-6" method="POST" id="post_form" enctype="multipart/form-data"
                        action="">
                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <label class="flex flex-col">
                                <p class="text-base font-medium leading-normal pb-2">
                                    Post Title</p>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg focus:outline-0 focus:ring-0 border border-[#dbe0e6] bg-white focus:border-orange-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                    placeholder="Enter the title of your post" value="<?php echo $post_title; ?>"
                                    name="post_title" type="text" id="post_title" />

                                <!-- error message  -->
                                <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="Post_TitleErr">
                                    <?php echo $post_titleErr; ?>
                                </p>

                            </label>
                        </div>
                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <p class=" text-base font-medium leading-normal pb-2">
                                Content</p>
                            <div class="flex flex-col min-w-40 h-full flex-1">
                                <div
                                    class="flex w-full flex-1 items-stretch rounded-lg flex-col border border-[#dbe0e6]">
                                    <div class="flex flex-1 flex-col">

                                        <!-- FIXED: Removed incorrect value attribute from contenteditable div -->
                                        <div class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-t-lg text-text-primary focus:outline-0 focus:ring-0 border-0 bg-white focus:border-primary min-h-72 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                            id="post_content" contenteditable="true" role="textbox">
                                            <?php echo $post_content; ?>
                                        </div>

                                        <!-- hidden post content for php  -->
                                        <input type="hidden" name="post_content" id="hidden_post_content">

                                        <p class="text-[#fd0303] text-base font-medium leading-normal pb-2"
                                            id="Post_ContentErr">
                                            <?php echo $post_contentErr; ?>
                                        </p>
                                        <div
                                            class="flex border-t border-t-[#dbe0e6] bg-gray-50 item-center pr-[15px] rounded-b-lg px-[15px] py-2">
                                            <div class="flex items-center gap-4 flex-1 justify-between">
                                                <!-- Editor buttons commented out as requested -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <p class="text-base font-bold text-center leading-normal pb-2">Add Images</p>
                            <p class="font-sm text-center">Image size should not exceed 5MB.</p>

                            <input type="file" name="post_images[]" id="post_images_input" accept="image/*" multiple
                                class="hidden" onchange="handleNewFileSelection(this.files)">

                            <div id="image_preview_container" class="flex flex-wrap gap-4 items-start">
                                <button type="button" onclick="document.getElementById('post_images_input').click()"
                                    class="add-image-button h-50 w-50 border-2 border-dashed border-[#dbe0e6] rounded-lg flex items-center justify-center bg-white text-gray-400 hover:border-orange-600 transition duration-200">
                                    <i class="bi bi-plus-lg text-2xl"></i>
                                </button>
                            </div>

                            <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="FeaturedImageErr">
                                <?php echo $post_imagesErr; ?>
                            </p>
                        </div>

                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <p class="text-base font-bold text-center leading-normal pb-2">Add Video</p>
                            <p class="font-sm text-center">Video size should not exceed 50MB.</p>

                            <input type="file" name="post_videos[]" id="post_videos_input" accept="video/*" multiple
                                class="hidden" onchange="handleNewVideoSelection(this.files)">

                            <div id="video_preview_container" class="flex flex-wrap gap-4 items-start">
                                <button type="button" onclick="document.getElementById('post_videos_input').click()"
                                    class="add-video-button h-50 w-50 border-2 border-dashed border-[#dbe0e6] rounded-lg flex items-center justify-center bg-white text-gray-400 hover:border-orange-600 transition duration-200">
                                    <i class="bi bi-plus-lg text-2xl"></i>
                                </button>
                            </div>

                            <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="FeaturedVideoErr">
                                <?php echo $post_videosErr ?? ''; ?>
                            </p>
                        </div>

                        <!-- Categories, Tags, Featured Post starts here  -->
                        <input type="hidden" name="Categories" id="Categories_js">
                        <input type="hidden" name="Tags" id="Tags_js">
                        <input type="hidden" name="Featured" id="Featured_js">

                    </form>
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h3 class="text-lg font-bold  mb-4">Publish Settings</h3>
                            <div class="space-y-4">
                                <button
                                    class="w-full flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-orange-600 text-white text-sm font-bold leading-normal tracking-[0.015em]"
                                    type="button" onclick="Post_validation()">
                                    <span class="truncate">Publish</span>
                                </button>
                                <button
                                    class="w-full flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-200 text-sm font-bold leading-normal tracking-[0.015em]"
                                    type="button" onclick="Draft_validation()">
                                    <span class="truncate">Save Draft</span>
                                </button>
                                <p class="text-[#fd0303] text-base font-medium leading-normal pb-2">
                                    <?php echo $DraftErr; ?>
                                </p>

                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h3 class="text-lg font-bold mb-4">Categories &amp; Tags
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium">Categories</label>
                                    <select id="categories_select"
                                        class="mt-1 form-select p-2 w-full rounded-lg border border-[#dbe0e6] bg-white focus:border-orange-600 dark:focus:border-orange-600">
                                        <option value="Announcements" <?php echo ($Categories == 'Announcements') ? 'selected' : ''; ?>>Announcements</option>
                                        <option value="Product Updates" <?php echo ($Categories == 'Product Updates') ? 'selected' : ''; ?>>Product Updates</option>
                                        <option value="Company News" <?php echo ($Categories == 'Company News') ? 'selected' : ''; ?>>Company News</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Tags</label>
                                    <input type="text" id="Tags_input" value="<?php echo $Tags; ?>"
                                        class="mt-1 p-2 form-input w-full rounded-lg border border-[#dbe0e6] bg-white focus:border-orange-600 dark:focus:border-orange-600"
                                        placeholder="Add tags, comma-separated" />
                                </div>
                                <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="TagErr">
                                    <?php echo $TagErr; ?>
                                </p>
                                <div class="flex items-center">
                                    <input class="h-4 w-4 focus:ring-orange-600 border-gray-300 rounded"
                                        id="featured-post" type="checkbox" value="featured" <?php echo ($Featured == 'Yes') ? 'checked' : ''; ?> />
                                    <label class="ml-2 block text-sm" for="featured-post">Mark as Featured Post</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // File Management & Previews
        let filesToUpload = new DataTransfer();
        let videosToUpload = new DataTransfer();

        // 1. Handles files selected via the hidden input field (called by onchange)
        function handleNewFileSelection(newFiles) {
            if (newFiles.length === 0) return;

            // Add new files to the DataTransfer object
            for (let i = 0; i < newFiles.length; i++) {
                // Basic check to ensure it's an image before adding
                if (newFiles[i].type.startsWith('image/')) {
                    // Check file size (5MB limit)
                    if (newFiles[i].size > 5 * 1024 * 1024) {
                        alert('File "' + newFiles[i].name + '" exceeds 5MB size limit.');
                        continue;
                    }
                    filesToUpload.items.add(newFiles[i]);
                }
            }

            // Update the hidden input element with the new list of files
            document.getElementById('post_images_input').files = filesToUpload.files;

            // Render the new previews
            renderPreviews();
        }

        // 2. Removes a file from the list by its index
        function removeFile(indexToRemove) {
            // Remove the item from the DataTransfer object
            filesToUpload.items.remove(indexToRemove);

            // Update the hidden input element
            document.getElementById('post_images_input').files = filesToUpload.files;

            // Re-render the previews
            renderPreviews();
        }

        // 3. Generates and displays the image previews and the '+' button
        function renderPreviews() {
            const container = document.getElementById('image_preview_container');
            // Clear previous content but keep the + button structure temporarily
            container.innerHTML = '';

            const currentFiles = filesToUpload.files;

            // Loop through all files
            if (currentFiles.length > 0) {
                for (let i = 0; i < currentFiles.length; i++) {
                    const file = currentFiles[i];
                    const reader = new FileReader();

                    reader.onload = (function (index) {
                        return function (e) {
                            const previewHtml = `
                            <div class="relative size-30 rounded-lg overflow-hidden shadow-md group">
                                <img src="${e.target.result}" alt="Preview" class="w-40 h-40 object-cover">
                                <button type="button" onclick="removeFile(${index})"
                                    class="font-bold absolute top-0 right-0 size-6 rounded-full bg-red-600 text-white flex items-center justify-center opacity-100 transition duration-300 transform translate-x-1 -translate-y-1 group-hover:translate-x-0 group-hover:translate-y-0">
                                    <i class="bi bi-x-lg text-sm"></i>
                                </button>
                            </div>
                        `;
                            container.insertAdjacentHTML('beforeend', previewHtml);

                            // If this is the last file, insert the Add button next to it
                            if (index === currentFiles.length - 1) {
                                insertAddButton(container);
                            }
                        };
                    })(i);

                    reader.readAsDataURL(file);
                }
            } else {
                // If no files are present, just show the Add button
                insertAddButton(container);
            }

            // Clear file error message if files are present
            if (currentFiles.length > 0) {
                document.getElementById("FeaturedImageErr").innerHTML = "";
            }
        }

        // Helper function to insert the + button
        function insertAddButton(container) {
            const addButtonHtml = `
            <button type="button" onclick="document.getElementById('post_images_input').click()"
                class="add-image-button size-30 border-2 border-dashed border-[#dbe0e6] rounded-lg flex items-center justify-center bg-white text-gray-400 hover:border-orange-600 transition duration-200">
                <i class="bi bi-plus-lg text-2xl"></i>
            </button>
        `;
            container.insertAdjacentHTML('beforeend', addButtonHtml);
        }

        // --- Video Management Functions ---

        // 1. Handles videos selected
        function handleNewVideoSelection(newFiles) {
            if (newFiles.length === 0) return;

            for (let i = 0; i < newFiles.length; i++) {
                if (newFiles[i].type.startsWith('video/')) {
                    if (newFiles[i].size > 50 * 1024 * 1024) {
                        alert('File "' + newFiles[i].name + '" exceeds 50MB size limit.');
                        continue;
                    }
                    videosToUpload.items.add(newFiles[i]);
                }
            }
            document.getElementById('post_videos_input').files = videosToUpload.files;
            renderVideoPreviews();
        }

        // 2. Removes a video
        function removeVideo(indexToRemove) {
            videosToUpload.items.remove(indexToRemove);
            document.getElementById('post_videos_input').files = videosToUpload.files;
            renderVideoPreviews();
        }

        // 3. Renders video previews
        function renderVideoPreviews() {
            const container = document.getElementById('video_preview_container');
            container.innerHTML = '';
            const currentFiles = videosToUpload.files;

            if (currentFiles.length > 0) {
                for (let i = 0; i < currentFiles.length; i++) {
                    const file = currentFiles[i];
                    const videoUrl = URL.createObjectURL(file);

                    const previewHtml = `
                        <div class="relative size-30 rounded-lg overflow-hidden shadow-md group">
                            <video src="${videoUrl}" class="w-40 h-40 object-cover"></video>
                            <button type="button" onclick="removeVideo(${i})"
                                class="font-bold absolute top-0 right-0 size-6 rounded-full bg-red-600 text-white flex items-center justify-center opacity-100 transition duration-300 transform translate-x-1 -translate-y-1 group-hover:translate-x-0 group-hover:translate-y-0">
                                <i class="bi bi-x-lg text-sm"></i>
                            </button>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', previewHtml);

                    if (i === currentFiles.length - 1) {
                        insertAddVideoButton(container);
                    }
                }
            } else {
                insertAddVideoButton(container);
            }
        }

        function insertAddVideoButton(container) {
            const addButtonHtml = `
            <button type="button" onclick="document.getElementById('post_videos_input').click()"
                class="add-video-button size-30 border-2 border-dashed border-[#dbe0e6] rounded-lg flex items-center justify-center bg-white text-gray-400 hover:border-orange-600 transition duration-200">
                <i class="bi bi-plus-lg text-2xl"></i>
            </button>
        `;
            container.insertAdjacentHTML('beforeend', addButtonHtml);
        }

        //Validation Function
        function Post_validation() {
            const post_title = document.getElementById("post_title").value.trim();
            const post_content1 = document.getElementById("post_content").innerText.trim();
            const uploaded_files_count = filesToUpload.files.length;

            const Post_TitleErr1 = document.getElementById("Post_TitleErr");
            const Post_ContentErr1 = document.getElementById("Post_ContentErr");
            const FeaturedImageErr1 = document.getElementById("FeaturedImageErr");

            // Clear all previous error messages
            Post_TitleErr1.innerHTML = "";
            Post_ContentErr1.innerHTML = "";
            FeaturedImageErr1.innerHTML = "";

            let isValid = true;

            if (post_title === "") {
                Post_TitleErr1.innerHTML = "Post Title cannot be empty";
                isValid = false;
            }

            if (post_content1 === "") {
                Post_ContentErr1.innerHTML = "Post Content cannot be empty";
                isValid = false;
            } else {
                const hidden_post_content = document.getElementById("hidden_post_content");
                hidden_post_content.value = post_content1;
            }

            // Image/Video validation check (At least one is required)
            const uploaded_videos_count = videosToUpload.files.length;

            if (uploaded_files_count === 0 && uploaded_videos_count === 0) {
                FeaturedImageErr1.innerHTML = "At least one image or video must be uploaded";
                isValid = false;
            } else {
                if (uploaded_files_count > 0) document.getElementById('post_images_input').files = filesToUpload.files;
            }

            // Sync videos
            document.getElementById('post_videos_input').files = videosToUpload.files;

            // categories, tags and featured handling
            const CategoriesSelect = document.getElementById("categories_select");
            const Categories_js = document.getElementById("Categories_js");
            Categories_js.value = CategoriesSelect.value;

            const TagsInput = document.getElementById("Tags_input");
            const Tags_js = document.getElementById("Tags_js");
            Tags_js.value = TagsInput.value.trim();

            const FeaturedCheckbox = document.getElementById("featured-post");
            const Featured_js = document.getElementById("Featured_js");
            Featured_js.value = FeaturedCheckbox.checked ? "Yes" : "No";

            if (isValid) {
                // Show loading state
                const publishBtn = document.querySelector('button[onclick="Post_validation()"]');
                const originalText = publishBtn.innerHTML;
                publishBtn.innerHTML = 'Publishing...';
                publishBtn.disabled = true;

                // Submit via AJAX
                submitForm('post');

                // Re-enable button after 3 seconds (fallback)
                setTimeout(() => {
                    publishBtn.innerHTML = originalText;
                    publishBtn.disabled = false;
                }, 3000);
            }
        }

        // Initial render call to show the "+" button when the page loads
        document.addEventListener('DOMContentLoaded', function () {
            renderPreviews();
            renderVideoPreviews();

            // Set initial values for draft data
            const categoriesSelect = document.getElementById("categories_select");
            const tagsInput = document.getElementById("Tags_input");
            const featuredCheckbox = document.getElementById("featured-post");

            <?php if ($draft_id > 0): ?>
                // Set categories if draft exists
                categoriesSelect.value = "<?php echo $Categories; ?>";

                // Set tags if draft exists
                tagsInput.value = "<?php echo $Tags; ?>";

                // Set featured checkbox if draft exists
                featuredCheckbox.checked = <?php echo ($Featured == 'Yes') ? 'true' : 'false'; ?>;
            <?php endif; ?>
        });
    </script>

    <script>
        function Draft_validation() {
            const post_title = document.getElementById("post_title").value.trim();
            const post_content1 = document.getElementById("post_content").innerText.trim();
            const uploaded_files_count = filesToUpload.files.length;
            const hidden_post_content = document.getElementById("hidden_post_content");
            hidden_post_content.value = post_content1;

            const Post_TitleErr1 = document.getElementById("Post_TitleErr");
            const Post_ContentErr1 = document.getElementById("Post_ContentErr");
            const FeaturedImageErr1 = document.getElementById("FeaturedImageErr");

            // Clear all previous error messages
            Post_TitleErr1.innerHTML = "";
            Post_ContentErr1.innerHTML = "";
            FeaturedImageErr1.innerHTML = "";

            let isValid = true;

            if (post_title === "" && post_content1 === "") {
                alert("Cannot save an empty draft. Please add a title or content.");
                isValid = false;
            } else {
                document.getElementById('post_images_input').files = filesToUpload.files;
                document.getElementById('post_videos_input').files = videosToUpload.files;

                // categories, tags and featured handling
                const CategoriesSelect = document.getElementById("categories_select");
                const Categories_js = document.getElementById("Categories_js");
                Categories_js.value = CategoriesSelect.value;

                const TagsInput = document.getElementById("Tags_input");
                const Tags_js = document.getElementById("Tags_js");
                Tags_js.value = TagsInput.value.trim();

                const FeaturedCheckbox = document.getElementById("featured-post");
                const Featured_js = document.getElementById("Featured_js");
                Featured_js.value = FeaturedCheckbox.checked ? "Yes" : "No";

                if (isValid) {
                    // Show loading state
                    const draftBtn = document.querySelector('button[onclick="Draft_validation()"]');
                    const originalText = draftBtn.innerHTML;
                    draftBtn.innerHTML = 'Saving...';
                    draftBtn.disabled = true;

                    // Submit via AJAX to avoid page redirect
                    submitForm('draft');

                    // Re-enable button after 3 seconds (fallback)
                    setTimeout(() => {
                        draftBtn.innerHTML = originalText;
                        draftBtn.disabled = false;
                    }, 3000);
                }
            }
        }

        // AJAX form submission function
        function submitForm(type) {
            const form = document.getElementById('post_form');
            const formData = new FormData(form);

            // Add type to form data
            formData.append('submit_type', type);

            // Add draft_id if editing existing draft
            <?php if ($draft_id > 0): ?>
                formData.append('draft_id', <?php echo $draft_id; ?>);
            <?php endif; ?>

            // Determine the backend URL based on type
            const url = type === 'post' ? '../Backend/post_upload.php' : '../Backend/Draft.php';

            console.log('Submitting to:', url);
            console.log('Form data:', formData);

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    // Try to parse as JSON first, fallback to text
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            console.log('Non-JSON response:', text);
                            // Try to handle non-JSON responses
                            if (text.includes('success') || text.trim() === '') {
                                return { status: 'success', message: 'Operation completed successfully' };
                            } else {
                                return { status: 'error', message: text || 'Unknown error occurred' };
                            }
                        });
                    }
                })
                .then(data => {
                    console.log('Parsed response data:', data);

                    if (data.status === 'success') {
                        showMessage(data.message || `Draft saved successfully!`, 'success');

                        // Optionally clear form on successful post publication
                        if (type === 'post') {
                            setTimeout(() => {
                                document.getElementById("post_title").value = "";
                                document.getElementById("post_content").innerHTML = "";
                                document.getElementById("hidden_post_content").value = "";
                                document.getElementById("Tags_input").value = "";
                                document.getElementById("featured-post").checked = false;
                            }, 2000);
                        }
                    } else {
                        showMessage(data.message || 'Error saving draft', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showMessage('Network error: ' + error.message, 'error');
                })
                .finally(() => {
                    // Re-enable buttons
                    const publishBtn = document.querySelector('button[onclick="Post_validation()"]');
                    const draftBtn = document.querySelector('button[onclick="Draft_validation()"]');

                    if (publishBtn) {
                        publishBtn.innerHTML = 'Publish';
                        publishBtn.disabled = false;
                    }
                    if (draftBtn) {
                        draftBtn.innerHTML = 'Save Draft';
                        draftBtn.disabled = false;
                    }
                });
        }

        // Message display function
        function showMessage(message, type) {
            // Remove any existing messages
            const existingMessages = document.querySelectorAll('.dynamic-message');
            existingMessages.forEach(msg => msg.remove());

            // Create message element
            const messageDiv = document.createElement('div');
            messageDiv.className = `dynamic-message mx-4 mb-4 p-4 rounded-lg border ${type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'}`;
            messageDiv.textContent = message;

            // Insert after the page heading
            const headingContainer = document.querySelector('.flex.flex-wrap.justify-between.gap-3.p-4');
            if (headingContainer) {
                headingContainer.parentNode.insertBefore(messageDiv, headingContainer.nextSibling);
            }

            // Auto-remove success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 5000);
            }
        }
    </script>
</body>

</html>