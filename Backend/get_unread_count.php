<?php
header('Content-Type: application/json');
require_once 'Config.php';

$response = [
    'success' => false,
    'count' => 0
];

if (isset($conn) && $conn instanceof mysqli && @$conn->ping()) {
    try {
        $sql = "SELECT COUNT(*) as unread FROM inquiry WHERE status = 'new'";
        $result = $conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            $response['count'] = (int) ($row['unread'] ?? 0);
            $response['success'] = true;
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
} else {
    $response['error'] = "Database connection unavailable";
}

echo json_encode($response);
?>