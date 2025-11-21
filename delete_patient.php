<?php
session_start();
include "db_connect.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('No patient selected.'); window.location='pat.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Get name for audit log
$result = $conn->query("SELECT fullname FROM patients WHERE patient_id = $id");
if ($result->num_rows == 0) {
    echo "<script>alert('Patient not found.'); window.location='pat.php';</script>";
    exit;
}
$fullname = $result->fetch_assoc()['fullname'];

// ✅ Determine current user/employee
$user_id = null;
$employee_id = null;
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin' && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } elseif ($_SESSION['role'] === 'staff' && isset($_SESSION['employee_id'])) {
        $employee_id = $_SESSION['employee_id'];
    }
}

// Delete patient
if ($conn->query("DELETE FROM patients WHERE patient_id = $id")) {
    // Audit trail
    $action = "Deleted Patient";
    $details = "Removed patient record: $fullname (Patient ID: $id)";
    $status = "Success";
    $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                             VALUES (?, ?, ?, ?, ?)");
    
    // --- FIXED BIND PARAM ---
    $audit->bind_param("issss", $user_id, $employee_id, $action, $details, $status);
    
    $audit->execute();
    $audit->close();

    echo "<script>alert('Patient deleted successfully!'); window.location='pat.php';</script>";
} else {
    echo "<script>alert('Error deleting patient.'); window.location='pat.php';</script>";
}
?>