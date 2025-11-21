<?php
session_start();
include 'db_connect.php';

// Get and sanitize form inputs
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm = trim($_POST['confirm_password'] ?? '');
$role = 'admin';

// Check required fields
if (empty($fullname) || empty($email) || empty($username) || empty($password)) {
    echo "<script>alert('Please fill all required fields.'); window.history.back();</script>";
    exit();
}

// Check password match
if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
    exit();
}

// Check if username or email already exists
$check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "<script>alert('Username or Email already exists.'); window.history.back();</script>";
    exit();
}
$stmt->close();

// Encrypt password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into users table
$sql = "INSERT INTO users (fullname, username, email, password, role, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $fullname, $username, $email, $hashed_password, $role);

if ($stmt->execute()) {
    $new_admin_id = $conn->insert_id;

    // ✅ Audit trail logging
    if (isset($_SESSION['user_id'])) {
        $action = "Create Admin";
        $details = "Created new admin account (Email: $email, Username: $username)";
        $status = "Success";

        $audit_sql = "INSERT INTO audit_trail (user_id, action, details, status) VALUES (?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $audit_stmt->bind_param("isss", $_SESSION['user_id'], $action, $details, $status);
        $audit_stmt->execute();
        $audit_stmt->close();
    }

    echo "<script>alert('Admin account created successfully!'); window.location='emp.php';</script>";
} else {
    echo "<script>alert('Error creating admin account: " . $conn->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
