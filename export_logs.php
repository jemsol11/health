<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "barangay_health_center";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter parameters from URL
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filter_user = isset($_GET['filter_user']) ? $_GET['filter_user'] : '';
$filter_action = isset($_GET['filter_action']) ? $_GET['filter_action'] : '';

// Build WHERE clause
$where_conditions = [];
if (!empty($date_from)) {
    $where_conditions[] = "DATE(a.timestamp) >= '" . $conn->real_escape_string($date_from) . "'";
}
if (!empty($date_to)) {
    $where_conditions[] = "DATE(a.timestamp) <= '" . $conn->real_escape_string($date_to) . "'";
}
if (!empty($filter_user)) {
    $where_conditions[] = "u.username = '" . $conn->real_escape_string($filter_user) . "'";
}
if (!empty($filter_action)) {
    $where_conditions[] = "a.action = '" . $conn->real_escape_string($filter_action) . "'";
}

$where_sql = '';
if (count($where_conditions) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $where_conditions);
}

// Fetch filtered logs
$sql = "SELECT a.timestamp, u.username, u.role, a.action, a.details, a.status
        FROM audit_trail a
        LEFT JOIN users u ON a.user_id = u.user_id
        $where_sql
        ORDER BY a.timestamp DESC";

$result = $conn->query($sql);

// Set headers for CSV download
$filename = "audit_logs_" . date('Y-m-d_H-i-s') . ".csv";
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['Timestamp', 'User', 'Role', 'Action', 'Details', 'Status']);

// Write data rows
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['timestamp'],
            $row['username'] ? $row['username'] : 'Unknown',
            $row['role'] ? $row['role'] : '-',
            $row['action'],
            $row['details'],
            $row['status']
        ]);
    }
}

fclose($output);
$conn->close();
exit();
?>