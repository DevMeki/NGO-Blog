<?php

session_start();
require_once '../Backend/Config.php';

//check if admin is logged in
if (!isset($_SESSION['username'])) {
    header('Location: Admin_login.php');
    exit();
}

$username = $_SESSION['username'];

if (!isset($conn)) {
    die("Error: Database connection object (\$conn) is not available.");
}

$upload_dir = dirname(__DIR__) . '/Assets/uploads/'; // CORRECT absolute path
$max_file_size = 5 * 1024 * 1024; // 5MB limit
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // if (isset($_POST['action']) && $_POST['action'] == 'poblish') {

    $upload_successful = true;
    $image_paths_json = json_encode([]); // Default empty JSON for optional images

    // --- 2. Sanitize and Validate Title ---
    if (empty($_POST["post_title"])) {
        $post_titleErr = "Post Title is required.";
        $upload_successful = false;
    } else {
        $post_title = trim($_POST["post_title"]);
        // Basic length check
        if (strlen($post_title) > 255) {
            $post_titleErr = "Title cannot exceed 255 characters.";
            $upload_successful = false;
        }
    }

    // --- 3. Sanitize and Validate Content (from contenteditable div) ---
    if (empty($_POST["post_content"])) {
        $post_contentErr = "Post Content is required.";
        $upload_successful = false;
    } else {
        $post_content = $_POST["post_content"];
        // Ensure content is not just empty tags/whitespace after cleaning
        if (trim(strip_tags($post_content)) === '') {
            $post_contentErr = "Post Content cannot be empty.";
            $upload_successful = false;
        }
    }

    // --- 4. Handle File Uploads ---
    $uploaded_paths = [];

    if (isset($_FILES['post_images']) && !empty($_FILES['post_images']['name'][0])) {

        // Loop through the array of uploaded files
        for ($i = 0; $i < count($_FILES['post_images']['name']); $i++) {
            $file_name = $_FILES['post_images']['name'][$i];
            $file_tmp = $_FILES['post_images']['tmp_name'][$i];
            $file_size = $_FILES['post_images']['size'][$i];
            $file_error = $_FILES['post_images']['error'][$i];
            $file_type = $_FILES['post_images']['type'][$i];

            if ($file_error !== UPLOAD_ERR_OK) {
                $post_imagesErr = "File upload error for one or more files.";
                // $error_message = "File upload error for one or more files.";
                $upload_successful = false;
                break;
            }

            // Check file type and size
            if ($file_size > $max_file_size) {
                $post_imagesErr = "One or more images exceeded the 5MB size limit.";
                // $error_message = "One or more images exceeded the 5MB size limit.";
                $upload_successful = false;
                break;
            }
            if (!in_array($file_type, $allowed_types)) {
                $post_imagesErr = "Only JPG, PNG, and GIF images are allowed.";
                // $error_message = "Only JPG, PNG, and GIF images are allowed.";
                $upload_successful = false;
                break;
            }

            // Generate a unique file name
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid('post_img_', true) . '.' . $file_ext;
            $file_destination = $upload_dir . $new_file_name;
            $relative_path = 'Assets/uploads/' . $new_file_name; // CORRECT web path for DB

            // Move the uploaded file
            if (move_uploaded_file($file_tmp, $file_destination)) {
                $uploaded_paths[] = $relative_path;
            } else {
                $post_imagesErr = "Failed to move one or more uploaded files.";
                // $error_message = "Failed to move one or more uploaded files.";
                $upload_successful = false;
                break;
            }
        }

        // Store the array of paths as a JSON string for the database
        $image_paths_json = json_encode($uploaded_paths);

    } else {
        // Image upload is optional now
    }


    // --- Handle Video Uploads ---
    $uploaded_video_paths = [];
    $max_video_size = 50 * 1024 * 1024; // 50MB
    $allowed_video_types = ['video/mp4', 'video/webm', 'video/ogg'];

    if (isset($_FILES['post_videos']) && !empty($_FILES['post_videos']['name'][0])) {
        for ($i = 0; $i < count($_FILES['post_videos']['name']); $i++) {
            $video_name = $_FILES['post_videos']['name'][$i];
            $video_tmp = $_FILES['post_videos']['tmp_name'][$i];
            $video_size = $_FILES['post_videos']['size'][$i];
            $video_error = $_FILES['post_videos']['error'][$i];
            $video_type = $_FILES['post_videos']['type'][$i];

            if ($video_error !== UPLOAD_ERR_OK) {
                // $post_videosErr = "Video upload error.";
                continue;
            }

            if ($video_size > $max_video_size) {
                // $post_videosErr = "Video too large.";
                $upload_successful = false;
                break;
            }

            if (!in_array($video_type, $allowed_video_types)) {
                // $post_videosErr = "Invalid video type.";
                $upload_successful = false;
                break;
            }

            $video_ext = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
            $new_video_name = uniqid('post_vid_', true) . '.' . $video_ext;
            $video_destination = $upload_dir . $new_video_name;
            $video_relative_path = 'Assets/uploads/' . $new_video_name;

            if (move_uploaded_file($video_tmp, $video_destination)) {
                $uploaded_video_paths[] = $video_relative_path;
            }
        }
    }

    // --- Verify Media Presence (Backend Check) ---
    // Check if we have at least one image OR one video
    $has_image = !empty($uploaded_paths);
    $has_video = !empty($uploaded_video_paths);

    if (!$has_image && !$has_video) {
        $post_imagesErr = "At least one image or video is required.";
        $upload_successful = false;
    }

    $Categories = isset($_POST["Categories"]) ? $_POST["Categories"] : '';
    $Featured = isset($_POST['Featured']) ? $_POST['Featured'] : '';
    $Tags = test_input($_POST["Tags"]);
    if (strlen($Tags) > 255) {
        $TagErr = "Tag cannot exceed 255 characters.";
        $upload_successful = false;
    }
    $published_by = $username;

    // --- 5. Insert Data into Database ---
    if ($upload_successful) {

        // SQL statement with placeholders
        $sql = "INSERT INTO blog_post (title, content, image_path, date_posted, Categories, Tags, Featured, published_by	
) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (s=string)
            $stmt->bind_param("sssssss", $db_title, $db_content, $db_image_paths, $db_Categories, $db_Tags, $db_Featured, $db_published_by);

            // Set parameters
            $db_title = $post_title;
            $db_content = $post_content;
            $db_image_paths = $image_paths_json;
            $db_Categories = $Categories;
            $db_Tags = $Tags;
            $db_Featured = $Featured;
            $db_published_by = $published_by;

            // Execute the prepared statement
            if ($stmt->execute()) {
                $new_post_id = $conn->insert_id;

                // --- 6. Insert Videos into post_videos table ---
                if (!empty($uploaded_video_paths)) {
                    $video_sql = "INSERT INTO post_videos (post_id, video_path) VALUES (?, ?)";
                    if ($v_stmt = $conn->prepare($video_sql)) {
                        foreach ($uploaded_video_paths as $v_path) {
                            $v_stmt->bind_param("is", $new_post_id, $v_path);
                            $v_stmt->execute();
                        }
                        $v_stmt->close();
                    }
                }

                header("Location: ../Admin/Admin_Post_Editor.php?post_status=success");
                if ($success) {
                    echo "success";
                } else {
                    echo "Error: " . $error_message;
                }
                exit();
            } else {
                // Database execution error
                echo "Error: Could not execute query. " . $stmt->error;
            }

            // Close statement
            $stmt->close();
        } else {
            // Database preparation error
            echo "Error: Could not prepare statement. " . $conn->error;
        }
    } else {
    }
}


// Function to clean and sanitize input (optional, but good practice for other forms)
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>