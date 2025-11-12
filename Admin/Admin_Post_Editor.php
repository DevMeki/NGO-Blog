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

$post_title = $post_content = $image_paths_json = $Categorries = $Tags = $Featured = "";
$post_titleErr = $post_contentErr = $post_imagesErr = $TagErr = "";

require_once '../Backend/post_upload.php';

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
    <link rel="apple-touch-icon" href="./Assets/img/logo bg.png">
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
                class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#dbe0e6] px-10 py-3 bg-white">
                <div class="flex items-center gap-4">
                    <div class="size-6 ">
                        <img src="../Assets/img/logo.png">
                    </div>
                    <h2 class="text-orange-600 text-lg font-bold leading-tight tracking-[-0.015em]">
                        Admin <?php
                        echo $username;
                        ?>
                    </h2>
                </div>
                <div class="flex flex-1 justify-end gap-8">
                    <div class="flex items-center gap-9"><a class="text-sm font-medium leading-normal"
                            href="../Blog.php">Back
                            to Blog</a></div>
                    <a href="?confirm=yes">
                        <button
                            class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-orange-600 text-white text-sm font-bold leading-normal tracking-[0.015em]">
                            <span class="truncate">Logout</span>
                        </button>
                    </a>
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <form class="lg:col-span-2 space-y-6" method="post"
                        action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="post_form"
                        enctype="multipart/form-data">
                        <div class="flex flex-wrap justify-between gap-3 p-4">
                            <div class="flex min-w-72 flex-col gap-3">
                                <p class="text-4xl font-black leading-tight tracking-[-0.033em]">
                                    Create New Post</p>
                                <p class="text-gray-500 text-base font-normal leading-normal">Fill in
                                    the details below to create a new blog post.</p>
                            </div>
                        </div>
                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <label class="flex flex-col">
                                <p class="text-base font-medium leading-normal pb-2">
                                    Post Title</p>
                                <input
                                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg focus:outline-0 focus:ring-0 border border-[#dbe0e6] bg-white focus:border-orange-600 h-14 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                    placeholder="Enter the title of your post" value="" name="post_title" type="text"
                                    id="post_title" />

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

                                        <div class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-t-lg text-text-primary focus:outline-0 focus:ring-0 border-0 bg-white focus:border-primary min-h-72 placeholder:text-gray-400 p-[15px] text-base font-normal leading-normal"
                                            id="post_content" contenteditable="true" role="textbox">
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
                                                <div class="flex items-center gap-1">
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200"
                                                        type="button" onclick="boldText()">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linejoin="round"
                                                                d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 0 0 0-8.25H6.75Zm0 0v.38m0 16.122h6.747a4.5 4.5 0 0 0 0-9.001h-7.5v9h.753Zm0 0v-.37m0-15.751h6a3.75 3.75 0 1 1 0 7.5h-6m0-7.5v7.5m0 0v8.25m0-8.25h6.375a4.125 4.125 0 0 1 0 8.25H6.75m.747-15.38h4.875a3.375 3.375 0 0 1 0 6.75H7.497v-6.75Zm0 7.5h5.25a3.75 3.75 0 0 1 0 7.5h-5.25v-7.5Z" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200"
                                                        type="button" onclick="italicText()">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M5.248 20.246H9.05m0 0h3.696m-3.696 0 5.893-16.502m0 0h-3.697m3.697 0h3.803" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200"
                                                        type="button" onclick="underlineText()">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M17.995 3.744v7.5a6 6 0 1 1-12 0v-7.5m-2.25 16.502h16.5" />
                                                        </svg>

                                                    </button>
                                                    <button
                                                        class="flex items-center justify-center p-1.5 rounded-md hover:bg-gray-200"
                                                        type="button" onclick="createLink()">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                                        </svg>

                                                    </button>

                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm text-gray-500 italic">Draft
                                                        saved</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-100 p-6 rounded-xl shadow-sm">
                            <p class="text-base font-bold text-center leading-normal pb-2">Add Images</p>

                            <input type="file" name="post_images[]" id="post_images_input" accept="image/*" multiple
                                class="hidden" onchange="handleNewFileSelection(this.files)">

                            <div id="image_preview_container" class="flex flex-wrap gap-4 items-start">
                                <button type="button" onclick="document.getElementById('post_images_input').click()"
                                    class="add-image-button h-56 w-56 border-2 border-dashed border-[#dbe0e6] rounded-lg flex items-center justify-center bg-white text-gray-400 hover:border-orange-600 transition duration-200">
                                    <i class="bi bi-plus-lg text-2xl"></i>
                                </button>
                            </div>

                            <p class="text-[#fd0303] text-base font-medium leading-normal pb-2" id="FeaturedImageErr">
                                <?php echo $post_imagesErr; ?>
                            </p>
                        </div>
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
                                    class="w-full flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-gray-200 text-sm font-bold leading-normal tracking-[0.015em]">
                                    <span class="truncate">Save Draft</span>
                                </button>

                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm">
                            <h3 class="text-lg font-bold mb-4">Categories &amp; Tags
                            </h3>
                            <form class="space-y-4" method="post"
                                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <div>
                                    <label class="text-sm font-medium">Categories</label>
                                    <select name="Categories"
                                        class="mt-1 form-select p-2 w-full rounded-lg border border-[#dbe0e6] bg-white focus:border-orange-600 dark:focus:border-orange-600">
                                        <option>Announcements</option>
                                        <option>Product Updates</option>
                                        <option>Company News</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-medium">Tags</label>
                                    <input
                                        class="mt-1 p-2 form-input w-full rounded-lg border border-[#dbe0e6] bg-white focus:border-orange-600 dark:focus:border-orange-600"
                                        placeholder="Add tags, comma-separated" name="Tags" />
                                </div>
                                <p class="text-[#fd0303] text-base font-medium leading-normal pb-2"
                                    id="FeaturedImageErr">
                                    <?php echo $TagErr; ?>
                                </p>
                                <div class="flex items-center">
                                    <input class="h-4 w-4 focus:ring-orange-600 border-gray-300 rounded"
                                        id="featured-post" type="checkbox" name="Featured" value="featured" />
                                    <label class="ml-2 block text-sm" for="featured-post">Mark as Featured
                                        Post</label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // 🗂️ File Management & Previews
        // Global DataTransfer object to manage the file list before form submission
        let filesToUpload = new DataTransfer();

        // 1. Handles files selected via the hidden input field (called by onchange)
        function handleNewFileSelection(newFiles) {
            if (newFiles.length === 0) return;

            // Add new files to the DataTransfer object
            for (let i = 0; i < newFiles.length; i++) {
                // Basic check to ensure it's an image before adding
                if (newFiles[i].type.startsWith('image/')) {
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
                            <div class="relative size-17 rounded-lg overflow-hidden shadow-md group ">
                                <img src="${e.target.result}" alt="Preview" class="w-50 h-50 object-cover">
                                <button type="button" onclick="removeFile(${index})"
                                    class="font-bold absolute top-0 right-0 size-10 rounded-full bg-red-600 text-black flex items-center justify-center opacity-100 transition duration-300 transform translate-x-1 -translate-y-1 group-hover:translate-x-0 group-hover:translate-y-0">
                                    <i class="bi bi-x-lg text-3xl"></i>
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
            // **size-16** class used for small add button size
            const addButtonHtml = `
            <button type="button" onclick="document.getElementById('post_images_input').click()"
                class="add-image-button size-10 border-2 border-dashed border-[#dbe0e6] rounded-lg flex items-center justify-center bg-white text-gray-400 hover:border-orange-600 transition duration-200">
                <i class="bi bi-plus-lg text-2xl"></i>
            </button>
        `;
            container.insertAdjacentHTML('beforeend', addButtonHtml);
        }

        // --- Validation Function Update ---

        function Post_validation() {
            const post_title = document.getElementById("post_title").value.trim();
            const post_content1 = document.getElementById("post_content").innerText.trim();
            // Check the count from the live DataTransfer object
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

            // Image validation check
            if (uploaded_files_count === 0) {
                FeaturedImageErr1.innerHTML = "At least one image must be uploaded";
                isValid = false;
            } else {
                document.getElementById('post_images_input').files = filesToUpload.files;
            }

            if (isValid) {
                document.getElementById("post_form").submit();
            }
        }

        // --- Content Editor Functions (Keep these as they are) ---
        function formatText(command, value = null) {
            document.getElementById('post_content').focus();
            document.execCommand(command, false, value);
        }

        function boldText() {
            formatText('bold');
        }

        function italicText() {
            formatText('italic');
        }

        function underlineText() {
            formatText('underline');
        }

        function createLink() {
            const url = prompt("Enter the URL:", "http://");
            if (url && url !== "http://") {
                formatText('createLink', url);
            }
        }

        // Initial render call to show the "+" button when the page loads
        document.addEventListener('DOMContentLoaded', renderPreviews);
    </script>


</body>

</html>