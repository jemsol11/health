<?php
session_start();
include "db_connect.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('No patient selected.'); window.location='pat.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Fetch patient info
$result = $conn->query("SELECT * FROM patients WHERE patient_id = $id");
if ($result->num_rows == 0) {
    echo "<script>alert('Patient not found.'); window.location='pat.php';</script>";
    exit;
}
$patient = $result->fetch_assoc();

// Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname     = $_POST['fullname'];
    $dob          = $_POST['dob'];
    $age          = $_POST['age'];
    $sex          = $_POST['sex'];
    $blood_type   = $_POST['blood_type'];
    $purok        = $_POST['purok'];
    $contact      = $_POST['contact'];
    $civil_status = $_POST['civil_status'];
    $occupation   = $_POST['occupation'];
    $emergency    = $_POST['emergency'];

    $stmt = $conn->prepare("UPDATE patients 
        SET fullname=?, dob=?, age=?, sex=?, blood_type=?, purok=?, contact=?, civil_status=?, occupation=?, emergency_contact=?
        WHERE patient_id=?");
    $stmt->bind_param("ssisssssssi", $fullname, $dob, $age, $sex, $blood_type, $purok, $contact, $civil_status, $occupation, $emergency, $id);

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

    if ($stmt->execute()) {
        // Audit trail
        $action = "Edited Patient";
        $details = "Updated record for patient: $fullname (Patient ID: $id)";
        $status = "Success";
        $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                                 VALUES (?, ?, ?, ?, ?)");
        
        // --- FIXED BIND PARAM ---
        $audit->bind_param("issss", $user_id, $employee_id, $action, $details, $status);
        
        $audit->execute();
        $audit->close();

        echo "<script>alert('Patient updated successfully!'); window.location='pat.php';</script>";
    } else {
        echo "<script>alert('Error updating patient.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Patient</title>
  <link rel="stylesheet" href="styles/edit_patient.css">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <main class="content">
    <h1>Edit Patient Information</h1>

    <form method="POST" style="max-width:600px;">
      <label>Full Name:</label>
      <input type="text" name="fullname" value="<?= htmlspecialchars($patient['fullname']); ?>" required>

      <label>Date of Birth:</label>
      <input type="date" name="dob" value="<?= htmlspecialchars($patient['dob']); ?>" required>

      <label>Age:</label>
      <input type="number" name="age" value="<?= htmlspecialchars($patient['age']); ?>">

      <label>Sex:</label>
      <select name="sex" required>
        <option <?= $patient['sex'] == 'Male' ? 'selected' : '' ?>>Male</option>
        <option <?= $patient['sex'] == 'Female' ? 'selected' : '' ?>>Female</option>
      </select>

      <label>Blood Type:</label>
      <input type="text" name="blood_type" value="<?= htmlspecialchars($patient['blood_type']); ?>">

      <label>Purok:</label>
      <input type="text" name="purok" value="<?= htmlspecialchars($patient['purok']); ?>" required>

      <label>Contact:</label>
      <input type="text" name="contact" value="<?= htmlspecialchars($patient['contact']); ?>" required>

      <label>Civil Status:</label>
      <input type="text" name="civil_status" value="<?= htmlspecialchars($patient['civil_status']); ?>">

      <label>Occupation:</label>
      <input type="text" name="occupation" value="<?= htmlspecialchars($patient['occupation']); ?>">

      <label>Emergency Contact:</label>
      <input type="text" name="emergency" value="<?= htmlspecialchars($patient['emergency_contact']); ?>">

      <br><br>
      <button type="submit" class="btn primary">Save Changes</button>
      <a href="pat.php" class="btn outline">Cancel</a>
    </form>
  </main>
</body>
</html>