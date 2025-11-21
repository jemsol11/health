<?php
session_start();
include "db_connect.php";

if (!isset($_GET['id'])) {
    echo "No patient selected.";
    exit();
}

$patient_id = intval($_GET['id']);

// Fetch patient details
$patient_sql = "SELECT * FROM patients WHERE patient_id = ?";
$stmt = $conn->prepare($patient_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$patient) {
    echo "Patient not found.";
    exit();
}

// Fetch medical records
$records_sql = "SELECT * FROM medical_records WHERE patient_id = ? ORDER BY date DESC";
$stmt = $conn->prepare($records_sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$records = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Patient Details - <?= htmlspecialchars($patient['fullname']); ?></title>
  <link rel="stylesheet" href="styles/view_patient.css">
  
</head>
<body>
<?php include "navbar.php"; ?>

<div class="container">
  <h2>👤 <?= htmlspecialchars($patient['fullname']); ?></h2>

  <div class="card">
    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($patient['dob']); ?></p>
    <p><strong>Age:</strong> <?= htmlspecialchars($patient['age']); ?></p>
    <p><strong>Sex:</strong> <?= htmlspecialchars($patient['sex']); ?></p>
    <p><strong>Blood Type:</strong> <?= htmlspecialchars($patient['blood_type']); ?></p>
    <p><strong>Purok:</strong> <?= htmlspecialchars($patient['purok']); ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($patient['contact']); ?></p>
    <p><strong>Civil Status:</strong> <?= htmlspecialchars($patient['civil_status']); ?></p>
    <p><strong>Occupation:</strong> <?= htmlspecialchars($patient['occupation']); ?></p>
    <p><strong>Emergency Contact:</strong> <?= htmlspecialchars($patient['emergency_contact']); ?></p>
  </div>

  <h3>🩺 Medical Records</h3>

  <?php if ($records->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Doctor</th>
          <th>Complaint</th>
          <th>Diagnosis</th>
          <th>Treatment</th>
          <th>Medication</th>
          <th>BP</th>
          <th>Temp (°C)</th>
          <th>Weight (kg)</th>
          <th>Height (cm)</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $records->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['date']); ?></td>
            <td><?= htmlspecialchars($row['doctor']); ?></td>
            <td><?= nl2br(htmlspecialchars($row['complaint'])); ?></td>
            <td><?= nl2br(htmlspecialchars($row['diagnosis'])); ?></td>
            <td><?= nl2br(htmlspecialchars($row['treatment'])); ?></td>
            <td><?= nl2br(htmlspecialchars($row['medication'])); ?></td>
            <td><?= htmlspecialchars($row['bp']); ?></td>
            <td><?= htmlspecialchars($row['temperature']); ?></td>
            <td><?= htmlspecialchars($row['weight']); ?></td>
            <td><?= htmlspecialchars($row['height']); ?></td>
            <td><?= nl2br(htmlspecialchars($row['notes'])); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="no-records">
      <p>No medical records found for this patient.</p>
    </div>
  <?php endif; ?>

  <a href="pat.php" class="back-btn">← Back to Patients</a>
</div>
</body>
</html>
