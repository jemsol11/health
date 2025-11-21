<?php
include('db_connect.php');
session_start();

// Ensure admin is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('You must be logged in to perform this action.');
        window.location.href='login.php';
    </script>";
    exit;
}

// Check if employee_id is provided
if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];
    $user_id = $_SESSION['user_id']; // logged-in admin ID

    // Get employee details before deleting
    $get_emp = $conn->prepare("SELECT fullname, employee_id, role, position, email, status FROM employee_accounts WHERE id = ?");
    $get_emp->bind_param("i", $employee_id);
    $get_emp->execute();
    $result = $get_emp->get_result();
    $employee = $result->fetch_assoc();

    if ($employee) {
        $fullname   = $employee['fullname'];
        $emp_code   = $employee['employee_id'];
        $role       = ucfirst($employee['role']);
        $position   = $employee['position'];
        $email      = $employee['email'];
        $status_val = ucfirst($employee['status']);
    } else {
        $fullname = 'Unknown';
        $emp_code = 'N/A';
        $role     = 'N/A';
        $position = 'N/A';
        $email    = 'N/A';
        $status_val = 'N/A';
    }

    // Delete employee record
    $delete = $conn->prepare("DELETE FROM employee_accounts WHERE id = ?");
    $delete->bind_param("i", $employee_id);
    $delete_success = $delete->execute();

    // Prepare audit trail log
    $action = "Deleted employee account";
    $details = "Removed employee record: Name: $fullname ";
    $status = $delete_success ? "Success" : "Failed";

    // Insert into audit trail
    $audit = $conn->prepare("INSERT INTO audit_trail (user_id, action, details, status, timestamp) VALUES (?, ?, ?, ?, NOW())");
    $audit->bind_param("isss", $user_id, $action, $details, $status);
    $audit->execute();
    $audit->close();

    if ($delete_success) {
        echo "<script>
            alert('Employee account deleted successfully.');
            window.location.href='emp.php';
        </script>";
    } else {
        echo "<script>
            alert('Error deleting employee account.');
            window.location.href='emp.php';
        </script>";
    }

    $get_emp->close();
    $delete->close();
    $conn->close();

} else {
    echo "<script>
        alert('No employee ID provided.');
        window.location.href='emp.php';
    </script>";
}
?>
