<?php
// track_visits.php
require_once 'Config.php';

function trackPageVisit($conn) {
    // Get visitor information
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
    $page_visited = $_SERVER['REQUEST_URI']; // Full page URL
    $page_name = basename($_SERVER['PHP_SELF']); // Just the filename
    
    // Insert visitor data
    $sql = "INSERT INTO visitor_logs (ip_address, user_agent, page_visited, referrer, visit_time) 
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $ip_address, $user_agent, $page_visited, $referrer);
    
    if ($stmt->execute()) {
        return true;
    } else {
        error_log("Failed to track visit: " . $stmt->error);
        return false;
    }
    
    $stmt->close();
}

// Track the visit
trackPageVisit($conn);
?>