<?php
// analytics_functions.php
require_once 'Config.php';

class WebsiteAnalytics {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Get total visits across all pages
    public function getTotalVisits() {
        $sql = "SELECT COUNT(*) as total_visits FROM visitor_logs";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['total_visits'];
        }
        return 0;
    }
    
    // Get unique visitors (by IP)
    public function getUniqueVisitors() {
        $sql = "SELECT COUNT(DISTINCT ip_address) as unique_visitors FROM visitor_logs";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['unique_visitors'];
        }
        return 0;
    }
    
    // Get today's visits
    public function getTodayVisits() {
        $sql = "SELECT COUNT(*) as today_visits FROM visitor_logs WHERE DATE(visit_time) = CURDATE()";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['today_visits'];
        }
        return 0;
    }
    
    // Get this week's visits
    public function getThisWeekVisits() {
        $sql = "SELECT COUNT(*) as week_visits FROM visitor_logs WHERE YEARWEEK(visit_time, 1) = YEARWEEK(CURDATE(), 1)";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['week_visits'];
        }
        return 0;
    }
    
    // Get most visited pages
    public function getPopularPages($limit = 5) {
        $sql = "SELECT page_visited, COUNT(*) as visits 
                FROM visitor_logs 
                GROUP BY page_visited 
                ORDER BY visits DESC 
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $pages = [];
        while ($row = $result->fetch_assoc()) {
            $pages[] = $row;
        }
        
        $stmt->close();
        return $pages;
    }
    
    // Get visits by date range (for charts)
    public function getVisitsByDate($days = 7) {
        $sql = "SELECT DATE(visit_time) as visit_date, COUNT(*) as visits 
                FROM visitor_logs 
                WHERE visit_time >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                GROUP BY DATE(visit_time) 
                ORDER BY visit_date ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $visits_data = [];
        while ($row = $result->fetch_assoc()) {
            $visits_data[] = $row;
        }
        
        $stmt->close();
        return $visits_data;
    }
}

// Create global instance
$analytics = new WebsiteAnalytics($conn);
?>