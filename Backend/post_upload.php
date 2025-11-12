<?php

if (!isset($conn)) {
    die("Error: Database connection object (\$conn) is not available.");
}

$upload_dir = dirname(__DIR__) . '/Assets/uploads/'; // CORRECT absolute path
$max_file_size = 5 * 1024 * 1024; // 5MB limit
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $upload_successful = true;

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
                $upload_successful = false;
                break;
            }

            // Check file type and size
            if ($file_size > $max_file_size) {
                $post_imagesErr = "One or more images exceeded the 5MB size limit.";
                $upload_successful = false;
                break;
            }
            if (!in_array($file_type, $allowed_types)) {
                $post_imagesErr = "Only JPG, PNG, and GIF images are allowed.";
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
                $upload_successful = false;
                break;
            }
        }

        // Store the array of paths as a JSON string for the database
        $image_paths_json = json_encode($uploaded_paths);

    } else {
        // Validation check for empty files
        $post_imagesErr = "At least one image is required for the post.";
        $upload_successful = false;
    }


    $Categorries = isset($_POST["Categorries"]);
    $Featured = isset($_POST['Featured']) ? 'Yes' : 'No';
    $Tags = isset($_POST["Tags"]);
    // Basic length check
    if (strlen($Tags) > 255) {
        $TagErr = "Tag cannot exceed 255 characters.";
        $upload_successful = false;
    }

    // --- 5. Insert Data into Database ---
    if ($upload_successful) {

        // SQL statement with placeholders
        $sql = "INSERT INTO blog_post (title, content, image_path, date_posted, Categories, Tags, Featured	
) VALUES (?, ?, ?, NOW(), ?, ?, ?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (s=string)
            $stmt->bind_param("ssssss", $db_title, $db_content, $db_image_paths, $db_Categories, $db_Tags, $db_Featured);

            // Set parameters
            $db_title = $post_title;
            $db_content = $post_content;
            $db_image_paths = $image_paths_json;
            $db_Categories =  $Categorries;
            $db_Tags = $Tags;
            $db_Featured = $Featured;

            // Execute the prepared statement
            if ($stmt->execute()) {
                // Success: Redirect or show confirmation message
                header("Location: Admin_dashboard.php?post_status=success");
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