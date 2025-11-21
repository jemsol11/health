<?php
include 'navbar.php';
include 'db_connect.php';

// ====================== SUMMARY CARDS =========================

// Total Patients
$total_patients_query = $conn->query("SELECT COUNT(*) AS total FROM patients");
$total_patients = $total_patients_query->fetch_assoc()['total'];

// Total Medicine Assistance Given
$total_assistance_query = $conn->query("SELECT COUNT(*) AS total FROM medicine_assistance");
$total_assistance = $total_assistance_query->fetch_assoc()['total'];

// ====================== CHART DATA (Last 7 Days) =========================
$chart_query = $conn->query("
  SELECT DATE(date_given) AS date, SUM(quantity_given) AS total_given
  FROM medicine_assistance
  WHERE date_given >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY DATE(date_given)
  ORDER BY date ASC
");

$dates = [];
$quantities = [];
while ($row = $chart_query->fetch_assoc()) {
  $dates[] = $row['date'];
  $quantities[] = $row['total_given'];
}

// ====================== RECENT RECORDS =========================

// Recent Patients (Last 5)
$recent_patients = $conn->query("
  SELECT fullname, age, sex, purok, date_registered 
  FROM patients 
  ORDER BY date_registered DESC 
  LIMIT 5
");

// Recent Medicine Dispensing (Last 5)
$recent_dispensing = $conn->query("
  SELECT ma.date_given, p.fullname, m.med_name, ma.quantity_given
  FROM medicine_assistance ma
  JOIN patients p ON ma.patient_id = p.patient_id
  JOIN medicines m ON ma.med_id = m.med_id
  ORDER BY ma.date_given DESC
  LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Dashboard</title>
  <link rel="stylesheet" href="styles/admd.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f6e5e5;
      margin: 0;
      padding: 0;
    }
    .summary-cards {
      display: flex;
      justify-content: space-around;
      margin: 30px 20px;
      gap: 20px;
    }
    .card {
      background: #f7e5cbff;
      padding: 20px;
      border-radius: 10px;
      flex: 1;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card h3 {
      color: #9A3F3F;
      margin-bottom: 10px;
    }
    .card p {
      font-size: 1.8rem;
      font-weight: bold;
    }
    .chart-section {
      background: #fff;
      margin: 20px;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .chart-section h3 {
      color: #9A3F3F;
      margin-bottom: 15px;
    }
    .updates {
      display: flex;
      gap: 20px;
      margin: 20px;
    }
    .update-box {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      flex: 1;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .update-box h4 {
      color: #9A3F3F;
      margin-bottom: 10px;
      border-bottom: 2px solid #9A3F3F;
      padding-bottom: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      text-align: center;
    }
    th, td {
      padding: 8px 10px;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #f6e5e5;
      color: #9A3F3F;
    }
    .quick-actions {
      text-align: center;
      margin: 30px 0;
    }
    .btn {
      margin: 5px;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      color: #fff;
      font-size: 1rem;
    }
    .green { background: #a5af4cff; }
    .purple { background: #9A3F3F; }
  </style>
</head>
<body>
  <main class="dashboard">

    <!-- Summary Cards -->
    <section class="summary-cards">
      <div class="card">
        <h3>Total Patients</h3>
        <p><?php echo $total_patients; ?></p>
      </div>
      <div class="card">
        <h3>Medicine Assistance Given</h3>
        <p><?php echo $total_assistance; ?></p>
      </div>
    </section>

    <!-- Chart Section -->
    <section class="chart-section">
      <h3>Medicine Dispensing (Last 7 Days)</h3>
      <canvas id="dispenseChart" height="100"></canvas>
    </section>

    <!-- Updates Section -->
    <section class="updates">
      <!-- Recent Patients -->
      <div class="update-box">
        <h4>Recent Patients Added</h4>
        <table>
          <tr><th>Name</th><th>Age/Sex</th><th>Purok</th><th>Date Added</th></tr>
          <?php while ($row = $recent_patients->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['fullname']); ?></td>
              <td><?php echo htmlspecialchars($row['age'] . '/' . $row['sex']); ?></td>
              <td><?php echo htmlspecialchars($row['purok']); ?></td>
              <td><?php echo date("M d, Y", strtotime($row['date_registered'])); ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>

      <!-- Recent Medicine Dispensing -->
      <div class="update-box">
        <h4>Recent Medicine Dispensing</h4>
        <table>
          <tr><th>Patient</th><th>Medicine</th><th>Qty</th><th>Date</th></tr>
          <?php while ($row = $recent_dispensing->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['fullname']); ?></td>
              <td><?php echo htmlspecialchars($row['med_name']); ?></td>
              <td><?php echo htmlspecialchars($row['quantity_given']); ?></td>
              <td><?php echo date("M d, Y", strtotime($row['date_given'])); ?></td>
            </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </section>

    <!-- Quick Actions -->
    <section class="quick-actions">
      <button class="btn green" onclick="window.location.href='pat.php'">Add Patient</button>
      <button class="btn purple" onclick="window.location.href='med.php'">Dispense Medicine</button>
    </section>

  </main>

  <script>
    const ctx = document.getElementById('dispenseChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [{
          label: 'Medicines Dispensed',
          data: <?php echo json_encode($quantities); ?>,
          backgroundColor: 'rgba(154, 63, 63, 0.8)',
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
</body>
</html>
