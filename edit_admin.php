<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "barangay_health_center";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Ensure POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
    exit();
}

// Get and sanitize input
$user_id  = intval($_POST['user_id'] ?? 0);
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm  = trim($_POST['confirm_password'] ?? '');
$role     = trim($_POST['role'] ?? 'admin');

// Basic validation
if (empty($user_id) || empty($fullname) || empty($email) || empty($username)) {
    echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit();
}

if (!empty($password) && $password !== $confirm) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit();
}

// Check if email already exists for a different admin
$check_sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("si", $email, $user_id);
$check_stmt->execute();
$check_stmt->store_result();
if ($check_stmt->num_rows > 0) {
    echo "<script>alert('Email already exists. Please use a different one.'); window.history.back();</script>";
    $check_stmt->close();
    exit();
}
$check_stmt->close();

// Update query
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET fullname = ?, email = ?, username = ?, password = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $fullname, $email, $username, $hashed_password, $role, $user_id);
} else {
    $sql = "UPDATE users SET fullname = ?, email = ?, username = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $fullname, $email, $username, $role, $user_id);
}

if ($stmt->execute()) {
    // ✅ Add audit trail log
    if (isset($_SESSION['user_id'])) {
        $action = "Edit Admin";
        $details = "Updated admin account (ID: $user_id, Email: $email)";
        $status_log = "Success";
        $log_sql = "INSERT INTO audit_trail (user_id, action, details, status) VALUES (?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("isss", $_SESSION['user_id'], $action, $details, $status_log);
        $log_stmt->execute();
        $log_stmt->close();
    }

    header("Location: emp.php?success=1&msg=" . urlencode("Admin successfully updated!"));
    exit();
} else {
    echo "<script>alert('Error updating admin: " . addslashes($conn->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
