<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input
    $fullname = trim($_POST['fullname']);
    $dob = $_POST['dob'];
    $age = $_POST['age'] ?: NULL;
    $sex = $_POST['sex'];
    $blood_type = $_POST['blood_type'];
    $purok = $_POST['purok'];
    $contact = $_POST['contact'];
    $civil_status = $_POST['civil_status'] ?: NULL;
    $occupation = $_POST['occupation'] ?: NULL;
    $emergency = $_POST['emergency'] ?: NULL;

    // Insert patient record
    $sql = "INSERT INTO patients 
        (fullname, dob, age, sex, blood_type, purok, contact, civil_status, occupation, emergency_contact, date_registered)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssssss", $fullname, $dob, $age, $sex, $blood_type, $purok, $contact, $civil_status, $occupation, $emergency);

    if ($stmt->execute()) {
        // ✅ Get new patient ID
        $patient_id = $conn->insert_id;

        // ✅ AUDIT TRAIL (Success)
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $action = "Add Patient";
            $details = "Added new patient record: $fullname (Patient ID: $patient_id)";
            $status = "Success";

            $audit_sql = "INSERT INTO audit_trail (user_id, action, details, status)
                          VALUES (?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $audit_stmt->bind_param("isss", $user_id, $action, $details, $status);
            $audit_stmt->execute();
            $audit_stmt->close();
        }

        // ✅ Show localhost says alert then redirect
        echo "<script>
            alert('Patient added successfully!');
            window.location.href = 'pat.php';
        </script>";
        exit;
    } else {
        // ❌ Insert failed — log to audit trail too
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $action = "Add Patient";
            $details = "Failed to add patient: $fullname (Error: {$stmt->error})";
            $status = "Failed";

            $audit_sql = "INSERT INTO audit_trail (user_id, action, details, status)
                          VALUES (?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $audit_stmt->bind_param("isss", $user_id, $action, $details, $status);
            $audit_stmt->execute();
            $audit_stmt->close();
        }

        // ❌ Show localhost says alert for error
        echo "<script>
            alert('Error inserting patient: " . addslashes($stmt->error) . "');
            window.location.href = 'pat.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
