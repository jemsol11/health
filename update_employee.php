<?php
include('db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('You must be logged in to perform this action.');
        window.location.href='login.php';
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    // Update employee record
    $update = $conn->prepare("UPDATE employee_accounts SET fullname=?, position=?, email=?, role=?, status=? WHERE id=?");
    $update->bind_param("sssssi", $fullname, $position, $email, $role, $status, $id);
    $update_success = $update->execute();

    // Log in audit trail
    $action = "Edited employee account";
    $details = "Updated employee record: Name: $fullname";
    $status_log = $update_success ? "Success" : "Failed";

    $audit = $conn->prepare("INSERT INTO audit_trail (user_id, action, details, status, timestamp) VALUES (?, ?, ?, ?, NOW())");
    $audit->bind_param("isss", $user_id, $action, $details, $status_log);
    $audit->execute();
    $audit->close();

    if ($update_success) {
        echo "<script>
            alert('Employee details updated successfully.');
            window.location.href='emp.php';
        </script>";
    } else {
        echo "<script>
            alert('Error updating employee record.');
            window.location.href='emp.php';
        </script>";
    }

    $update->close();
    $conn->close();
}
?>
