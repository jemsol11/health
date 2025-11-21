<?php
// Start the session to access it
session_start();

// Database connection (matching your login.php)
$host = "localhost";
$user = "root"; // default in XAMPP
$pass = "";     // default no password
$db   = "barangay_health_center";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- UNIVERSAL LOGGER FIX ---
// Check if EITHER an admin OR a staff is logged in
if (isset($_SESSION['user_id']) || isset($_SESSION['employee_id'])) {

    $user_id_for_log = NULL;
    $employee_id_for_log = NULL;
    $username_for_log = $_SESSION['username'] ?? 'Unknown'; // Get username (fullname for staff)
    $role_for_log = $_SESSION['role'] ?? 'Unknown';

    if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
        $employee_id_for_log = $_SESSION['employee_id']; // Get staff ID
    } else if (isset($_SESSION['user_id'])) {
        $user_id_for_log = $_SESSION['user_id'];         // Get admin ID
    }

    // Log successful logout to audit_trail
    $action = "Logout";
    $details = "User '$username_for_log' ($role_for_log) logged out successfully.";
    $status = "Success";
    
    // Use the query that logs BOTH IDs
    $log_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status) VALUES (?, ?, ?, ?, ?)";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("issss", $user_id_for_log, $employee_id_for_log, $action, $details, $status);
    $log_stmt->execute();
    $log_stmt->close();
    
    // Optional: Unset specific session variables for cleanup
    // We can unset all keys we know about
    unset($_SESSION['user_id']);
    unset($_SESSION['employee_id']);
    unset($_SESSION['username']);
    unset($_SESSION['role']);

} else {
    // No valid session – no logging needed, just clean up
}
// --- END OF FIX ---

// Destroy the entire session
session_destroy();

// Delete the session cookie for full cleanup (optional but recommended for security)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Close DB connection
$conn->close();

// Redirect to the login page
header("Location: log.php");

// Stop execution to prevent any further code from running
exit();
?>
