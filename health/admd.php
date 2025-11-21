<?php
include 'db_connect.php';
include 'navbar.php';

// 1. Function to run all three Python forecast scripts
function try_run_all_forecasts() {
    $scripts = [
        'forecast.py',
        'forecast_seasonal.py',
        'forecast_demand.py'
    ];
    
    $script_dir = 'C:\xampp\htdocs\healthbackup\health\\'; // Base directory for scripts
    
    // Commands to try (prioritizing the generic 'python' if PATH is set)
    $commands = [
        'python',
        // Fallback to explicit paths for stability
        '"C:\Users\Laptop Supplier PH\AppData\Local\Programs\Python\Python312\python.exe"',
        'py' 
    ];

    $success_count = 0;
    
    foreach ($scripts as $script_name) {
        $script_path = $script_dir . $script_name;
        
        foreach ($commands as $cmd) {
            // Execute command + redirect output (2>&1) to prevent the PHP script from hanging
            $full_cmd = "$cmd \"$script_path\" 2>&1"; 
            @exec($full_cmd, $out, $ret);
            
            if ($ret === 0) {
                $success_count++;
                break; // Stop trying commands for this script
            }
        }
    }
    return $success_count == count($scripts);
}

// 2. Tiered Data Loading Function
function load_tiered_forecast_data() {
    $gbr_file = 'forecast_results.json';
    $prophet_file = 'seasonal_forecast.json';
    $lr_file = 'forecast_demand.json';

    $final_data = [];
    
    // Load all three results
    $gbr_data = file_exists($gbr_file) ? json_decode(file_get_contents($gbr_file), true) : [];
    $prophet_data = file_exists($prophet_file) ? json_decode(file_get_contents($prophet_file), true) : [];
    $lr_data = file_exists($lr_file) ? json_decode(file_get_contents($lr_file), true) : [];

    // Combine all keys from all sources
    $all_meds = array_keys(array_merge($gbr_data, $lr_data));
    $all_meds = array_unique($all_meds);

    foreach ($all_meds as $med_name) {
        $prediction = 0;
        
        // Tier 1: GBR (If GBR predicts 0, fall through to next tier)
        if (isset($gbr_data[$med_name]) && $gbr_data[$med_name] > 0) {
            $prediction = $gbr_data[$med_name];
        } 
        
        // Tier 2: Prophet (Check if GBR was skipped or gave zero)
        else if (isset($prophet_data[$med_name])) {
            // Prophet result is an array with 'next_month_pred'
            $prophet_pred = $prophet_data[$med_name]['next_month_pred'] ?? 0;
            if ($prophet_pred > 0) {
                 $prediction = $prophet_pred;
            }
        }

        // Tier 3: Linear Regression (Check if GBR/Prophet were skipped or gave zero)
        else if (isset($lr_data[$med_name]) && $lr_data[$med_name] > 0) {
            $prediction = $lr_data[$med_name];
        }

        // Final fallback: Use the highest value found (or 0)
        $final_data[$med_name] = round($prediction, 2);
    }
    
    return $final_data;
}


// === EXECUTION ===
// Run all forecasts
try_run_all_forecasts();

// Load the best available forecast data using the tiered system
$forecastData = load_tiered_forecast_data();

$medNames = array_keys($forecastData);
$predictedValues = array_values($forecastData);

// === SUMMARY CARD QUERIES ===
$totalPatients = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()['total'] ?? 0;
$totalStock = $conn->query("SELECT SUM(quantity) AS total FROM medicines")->fetch_assoc()['total'] ?? 0;
$lowStock = $conn->query("SELECT COUNT(*) AS low FROM medicines WHERE quantity <= 10")->fetch_assoc()['low'] ?? 0;
$today = date('Y-m-d');
$todayVisits = $conn->query("SELECT COUNT(*) AS today FROM medicine_assistance WHERE DATE(date_given) = '$today'")->fetch_assoc()['today'] ?? 0;

// === DISPENSING CHART (Last 7 Days) ===
$chartData = $conn->query("
  SELECT DATE(date_given) AS date, SUM(quantity_given) AS total_given
  FROM medicine_assistance
  WHERE date_given >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY DATE(date_given)
  ORDER BY date ASC
");
$dates = [];
$quantities = [];
while ($row = $chartData->fetch_assoc()) {
  $dates[] = $row['date'];
  $quantities[] = $row['total_given'];
}

// === RECENT PATIENTS ===
$recentPatients = $conn->query("
  SELECT fullname, sex, age, purok, date_registered
  FROM patients
  ORDER BY patient_id DESC
  LIMIT 5
");

// === RECENT DISPENSE ===
$recentDispense = $conn->query("
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
  <title>Admin Dashboard | Bagong Silang Health Center</title>
  <link rel="stylesheet" href="styles/admd.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
    <style>
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
    </style>
  
</head>
<body>
  <main class="dashboard">
    <section class="summary-cards">
      <div class="card"><h3>Total Patients</h3><p><?= $totalPatients ?></p></div>
      <div class="card"><h3>Medicines in Stock</h3><p><?= $totalStock ?></p></div>
      <div class="card"><h3>Low Stock Alerts</h3><p><?= $lowStock ?></p></div>
      <div class="card"><h3>Today's Visits</h3><p><?= $todayVisits ?></p></div>
    </section>

    <section class="charts">
      <div class="chart-box">
        <h3>Medicine Dispensing (Last 7 Days)</h3>
        <canvas id="dispenseChart"></canvas>
      </div>
      <div class="chart-box">
        <h3>Inventory Forecast</h3>
        <canvas id="forecastChart"></canvas>
      </div>
    </section>

    <section class="updates">
      <div class="update-box">
        <h3>Recent Patients Added</h3>
        <table>
          <tr><th>Name</th><th>Age/Sex</th><th>Purok</th><th>Date Registered</th></tr>
          <?php if ($recentPatients->num_rows > 0): ?>
            <?php while ($row = $recentPatients->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['age'].' / '.$row['sex']) ?></td>
                <td><?= htmlspecialchars($row['purok']) ?></td>
                <td><?= date("M d, Y", strtotime($row['date_registered'])) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?><tr><td colspan="4">No recent patients found.</td></tr><?php endif; ?>
        </table>
      </div>

      <div class="update-box">
        <h3>Recent Medicine Dispensing</h3>
        <table>
          <tr><th>Patient</th><th>Medicine</th><th>Qty</th><th>Date</th></tr>
          <?php if ($recentDispense->num_rows > 0): ?>
            <?php while ($row = $recentDispense->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['med_name']) ?></td>
                <td><?= htmlspecialchars($row['quantity_given']) ?></td>
                <td><?= date("M d, Y", strtotime($row['date_given'])) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?><tr><td colspan="4">No dispensing records found.</td></tr><?php endif; ?>
        </table>
      </div>
    </section>

    <section class="quick-actions">
      <button class="btn green" onclick="window.location.href='pat.php'">🧑‍⚕️ Add Patient</button>
      <button class="btn blue" onclick="window.location.href='inv.php'">📦 Add Inventory</button>
      <button class="btn purple" onclick="window.location.href='med.php'">💊 Dispense Medicine</button>
    </section>
  </main>

  <script>
    // Dispensing Chart (Last 7 days)
    new Chart(document.getElementById('dispenseChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
          label: 'Medicines Dispensed',
          data: <?= json_encode($quantities) ?>,
          backgroundColor: 'rgba(154, 63, 63, 0.8)',
          borderRadius: 6
        }]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });

    // Forecast Chart
    new Chart(document.getElementById('forecastChart'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($medNames) ?>,
        datasets: [{
          label: 'Predicted Demand (Next Month)',
          data: <?= json_encode($predictedValues) ?>,
          backgroundColor: '#9A3F3F',
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true, position: 'top' } },
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>
</body>
</html>