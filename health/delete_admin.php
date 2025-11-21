<?php
session_start();
include 'db_connect.php';

// ✅ Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Unauthorized access. Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

// ✅ Check if admin ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid admin ID.'); window.history.back();</script>";
    exit();
}

$admin_id = $_GET['id'];

// ✅ Fetch admin details before deletion (for audit log)
$fetch_sql = "SELECT fullname, email, username, role FROM users WHERE user_id = ?";
$fetch_stmt = $conn->prepare($fetch_sql);
$fetch_stmt->bind_param("i", $admin_id);
$fetch_stmt->execute();
$admin_data = $fetch_stmt->get_result()->fetch_assoc();
$fetch_stmt->close();

if (!$admin_data) {
    echo "<script>alert('Admin account not found.'); window.history.back();</script>";
    exit();
}

// ✅ Prevent deleting yourself
if ($admin_id == $_SESSION['user_id']) {
    echo "<script>alert('You cannot delete your own account.'); window.history.back();</script>";
    exit();
}

// ✅ Delete admin record
$delete_sql = "DELETE FROM users WHERE user_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $admin_id);

if ($delete_stmt->execute()) {

    // ✅ Log to audit trail
    $action = "Delete Admin Account";
    $details = "Deleted admin: {$admin_data['fullname']} ({$admin_data['email']}) - Username: {$admin_data['username']}";
    $status_log = "Success";

    $log_sql = "INSERT INTO audit_trail (user_id, action, details, status, timestamp)
                VALUES (?, ?, ?, ?, NOW())";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("isss", $_SESSION['user_id'], $action, $details, $status_log);
    $log_stmt->execute();
    $log_stmt->close();

    // ✅ Success message
    echo "<script>
        alert('✅ Admin \"{$admin_data['fullname']}\" has been successfully deleted.');
        window.location.href = 'emp.php';
    </script>";

} else {
    // ❌ Error handling
    echo "<script>
        alert('❌ Error deleting admin: " . addslashes($conn->error) . "');
        window.history.back();
    </script>";
}

$delete_stmt->close();
$conn->close();
?>
