<?php
session_start();
require_once 'Config.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$inquiry_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

if ($inquiry_id <= 0 || !in_array($status, ['read', 'archived'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit();
}

try {
    $sql = "UPDATE inquiry SET status = ? WHERE Inquiry_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $inquiry_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>