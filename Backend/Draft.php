<?php
session_start();
require_once 'Config.php'; // Changed path since this is in Backend folder

// Set header for JSON response
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$username = $_SESSION['username'];

// Debug logging
error_log("Draft.php: Script started");
error_log("Draft.php: POST data: " . print_r($_POST, true));
error_log("Draft.php: FILES data: " . print_r($_FILES, true));

if (!isset($conn)) {
    error_log("Draft.php: Database connection failed");
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// Initialize variables
$response = ['status' => 'error', 'message' => 'Unknown error'];
$upload_successful = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Draft.php: POST request received");

    // Check if it's empty
    if (empty($_POST["post_title"]) && empty($_POST["post_content"])) {
        $response = ['status' => 'error', 'message' => 'Cannot save an entirely empty draft. Please add a title or content.'];
        $upload_successful = false;
    } else {
        // Title validation
        $post_title = trim($_POST["post_title"] ?? '');
        if (strlen($post_title) > 255) {
            $response = ['status' => 'error', 'message' => 'Title cannot exceed 255 characters.'];
            $upload_successful = false;
        }
        
        $post_content = $_POST["post_content"] ?? '';
        $Categories = $_POST["Categories"] ?? '';
        $Featured = $_POST['Featured'] ?? 'No';
        $Tags = trim($_POST["Tags"] ?? '');

        if (strlen($Tags) > 255) {
            $response = ['status' => 'error', 'message' => 'Tags cannot exceed 255 characters.'];
            $upload_successful = false;
        }
    }

    // Insert into database if validation passed
    if ($upload_successful) {
        error_log("Draft.php: Attempting database insert");
        
        try {
            // Check if we're updating an existing draft
            $draft_id = $_POST['draft_id'] ?? 0;
            
            if ($draft_id > 0) {
                // Update existing draft
                $sql = "UPDATE draft SET title = ?, content = ?, Categories = ?, Tags = ?, Featured = ?, date_posted = NOW() WHERE draft_id = ? AND Created_by = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sssssis", $post_title, $post_content, $Categories, $Tags, $Featured, $draft_id, $username);
                }
            } else {
                // Insert new draft
                $sql = "INSERT INTO draft (title, content, date_posted, Categories, Tags, Featured, Created_by) VALUES (?, ?, NOW(), ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssssss", $post_title, $post_content, $Categories, $Tags, $Featured, $username);
                }
            }

            if ($stmt && $stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Draft saved successfully!'];
                error_log("Draft.php: Database insert successful");
            } else {
                $error = $stmt ? $stmt->error : $conn->error;
                $response = ['status' => 'error', 'message' => 'Database error: ' . $error];
                error_log("Draft.php: Database error: " . $error);
            }

            if ($stmt) {
                $stmt->close();
            }
        } catch (Exception $e) {
            $response = ['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()];
            error_log("Draft.php: Exception: " . $e->getMessage());
        }
    } else {
        error_log("Draft.php: Validation failed");
    }
} else {
    $response = ['status' => 'error', 'message' => 'Invalid request method'];
    error_log("Draft.php: Invalid request method - " . $_SERVER["REQUEST_METHOD"]);
}

// Send JSON response
echo json_encode($response);
error_log("Draft.php: Response sent: " . json_encode($response));
exit();
?>