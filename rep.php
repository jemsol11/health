<?php
// rep.php (final)
include('navbar.php');
include('db_connect.php');

// 1. UPDATED FUNCTION: Runs all three forecast scripts (Best Effort)
function try_run_all_forecasts() {
    $scripts = [
        'forecast.py',
        'forecast_seasonal.py',
        'forecast_demand.py' // Include Linear Regression
    ];
    
    // Commands to try (prioritizing generic commands if PATH is set)
    $commands = [
        'python',
        'py',
        'python3' 
    ];

    $success_count = 0;
    
    foreach ($scripts as $script_name) {
        $script_path = __DIR__ . DIRECTORY_SEPARATOR . $script_name;
        
        foreach ($commands as $cmd) {
            // Execute command + redirect output (2>&1)
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

// 2. NEW FUNCTION: Implements the GBR -> Prophet -> LR tiered fallback logic
function get_final_forecast_data() {
    $gbr_file = __DIR__ . '/forecast_results.json';
    $prophet_file = __DIR__ . '/seasonal_forecast.json';
    $lr_file = __DIR__ . '/forecast_demand.json';

    $final_next_month = [];
    $final_quarterly = [];

    // Load all three results
    $gbr_data = file_exists($gbr_file) ? json_decode(file_get_contents($gbr_file), true) : [];
    $prophet_data = file_exists($prophet_file) ? json_decode(file_get_contents($prophet_file), true) : [];
    $lr_data = file_exists($lr_file) ? json_decode(file_get_contents($lr_file), true) : [];

    // Combine all keys from all sources to get a master list of meds
    $all_meds = array_unique(array_merge(array_keys($gbr_data), array_keys($prophet_data), array_keys($lr_data)));

    foreach ($all_meds as $med_name) {
        $pred_nm = 0; // Next Month Prediction
        $pred_qr = 0; // Quarterly Prediction (initially same as NM or Prophet's QR)

        // Tier 1: GBR (Best accuracy, requires > 5 months data)
        if (isset($gbr_data[$med_name]) && $gbr_data[$med_name] > 0) {
            $pred_nm = $gbr_data[$med_name];
            // Quarterly is set to NM since GBR doesn't output quarterly average
            $pred_qr = $gbr_data[$med_name]; 
        } 
        
        // Tier 2: Prophet (Seasonal fallback, uses < 5 months data)
        else if (isset($prophet_data[$med_name])) {
            $prophet_nm = $prophet_data[$med_name]['next_month_pred'] ?? 0;
            $prophet_qr = $prophet_data[$med_name]['quarter_avg_pred'] ?? 0;
            
            if ($prophet_nm > 0) {
                 $pred_nm = $prophet_nm;
            }
            // Prophet's quarterly average is usually more reliable than repeating NM
            if ($prophet_qr > 0) {
                $pred_qr = $prophet_qr;
            } else {
                $pred_qr = $pred_nm;
            }
        }

        // Tier 3: Linear Regression (Basic trend fallback)
        else if (isset($lr_data[$med_name]) && $lr_data[$med_name] > 0) {
            $pred_nm = $lr_data[$med_name];
            // Quarterly is set to NM
            $pred_qr = $lr_data[$med_name];
        }

        $final_next_month[$med_name] = round($pred_nm, 2);
        $final_quarterly[$med_name] = round($pred_qr, 2);
    }
    
    return ['next_month' => $final_next_month, 'quarterly' => $final_quarterly];
}


// --- EXECUTION ---
// non-blocking attempt to run all forecasts
@try_run_all_forecasts();

// Load the final, merged forecast data
$final_forecasts = get_final_forecast_data();

$forecastDataFinal = $final_forecasts['next_month'];
$quarterlyDataFinal = $final_forecasts['quarterly']; 
// --- End of Forecast Setup ---

// SUMMARY CARDS
$total_patients = $conn->query("SELECT COUNT(*) AS total FROM patients")->fetch_assoc()['total'] ?? 0;
$total_stock = $conn->query("SELECT SUM(quantity) AS total_stock FROM medicines")->fetch_assoc()['total_stock'] ?? 0;
$low_stock = $conn->query("SELECT COUNT(*) AS low FROM medicines WHERE quantity <= 10")->fetch_assoc()['low'] ?? 0;
// Visits are better counted from medicine_assistance or a dedicated records table, not patient registration
$todays_visits = $conn->query("SELECT COUNT(DISTINCT patient_id) AS visits FROM medicine_assistance WHERE DATE(date_given) = CURDATE()")->fetch_assoc()['visits'] ?? 0;


// CHART DATA: inventory/top dispensed/monthly/patients/age
$inventory_data = []; $res = $conn->query("SELECT med_name, quantity FROM medicines ORDER BY quantity DESC LIMIT 10"); if ($res) while ($r = $res->fetch_assoc()) $inventory_data[] = $r;
$dispensed_data = []; $res = $conn->query("SELECT m.med_name, SUM(ma.quantity_given) AS total_dispensed FROM medicine_assistance ma JOIN medicines m ON ma.med_id = m.med_id GROUP BY ma.med_id ORDER BY total_dispensed DESC LIMIT 5"); if ($res) while ($r = $res->fetch_assoc()) $dispensed_data[] = $r;
$monthly_dispense = []; $res = $conn->query("SELECT MONTH(date_given) AS month, YEAR(date_given) AS year, SUM(quantity_given) AS total FROM medicine_assistance WHERE date_given IS NOT NULL GROUP BY YEAR(date_given), MONTH(date_given) ORDER BY YEAR(date_given), MONTH(date_given)"); if ($res) while ($r = $res->fetch_assoc()) $monthly_dispense[] = $r;
$patient_months = []; $res = $conn->query("SELECT MONTH(date_registered) AS month, YEAR(date_registered) AS year, COUNT(*) AS count FROM patients WHERE date_registered IS NOT NULL GROUP BY YEAR(date_registered), MONTH(date_registered) ORDER BY YEAR(date_registered), MONTH(date_registered)"); if ($res) while ($r = $res->fetch_assoc()) $patient_months[] = $r;

// age groups (safe SQL: CAST to unsigned only if numeric)
$age_groups = [];
// This query block must be perfectly clean:
$res = $conn->query("
    SELECT 
      CASE 
        WHEN age LIKE '%month%' THEN 'Infant (0-1yr)'
        WHEN age REGEXP '^[0-9]+' AND CAST(age AS UNSIGNED) < 13 THEN 'Child (1-12yrs)'
        WHEN age REGEXP '^[0-9]+' AND CAST(age AS UNSIGNED) BETWEEN 13 AND 19 THEN 'Teen (13-19yrs)'
        WHEN age REGEXP '^[0-9]+' AND CAST(age AS UNSIGNED) BETWEEN 20 AND 39 THEN 'Adult (20-39yrs)'
        WHEN age REGEXP '^[0-9]+' AND CAST(age AS UNSIGNED) BETWEEN 40 AND 59 THEN 'Middle Age (40-59yrs)'
        ELSE 'Senior (60+ yrs)'
      END AS age_group,
      COUNT(*) AS count
    FROM patients
    WHERE age IS NOT NULL AND age != ''
    GROUP BY age_group
");
if ($res) while ($r = $res->fetch_assoc()) $age_groups[] = $r;

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reports & Analytics | Bagong Silang Health Center</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="styles/rep.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
  .report-actions { display:flex; gap:10px; align-items:center; margin-bottom:12px; }
  .report-btn { background:#9A3F3F; color:#fff; border:none; padding:8px 14px; border-radius:8px; cursor:pointer; font-weight:bold; box-shadow:0 3px 6px rgba(0,0,0,0.06); }
  .report-btn.green { background:#14b56f; }
  .small-muted { font-size:13px; color:#666; }
  .spinner { border:3px solid rgba(0,0,0,0.08); border-left:3px solid #fff; border-radius:50%; width:16px; height:16px; display:inline-block; vertical-align:middle; margin-right:6px; animation:spin .8s linear infinite; }
  @keyframes spin { to { transform:rotate(360deg);} }
</style>
</head>
<body>
  <div class="dashboard">
    <div class="summary-cards">
      <div class="card">Total Patients<br><strong><?= htmlspecialchars($total_patients) ?></strong></div>
      <div class="card">Medicines in Stock<br><strong><?= htmlspecialchars($total_stock) ?></strong></div>
      <div class="card">Low Stock Alerts<br><strong><?= htmlspecialchars($low_stock) ?></strong></div>
      <div class="card">Today's Visits<br><strong><?= htmlspecialchars($todays_visits) ?></strong></div>
    </div>

    <div class="tabs">
      <button class="tab-btn active" onclick="openTab('inventory', event)">Inventory Reports</button>
      <button class="tab-btn" onclick="openTab('medicine', event)">Medicine Usage</button>
      <button class="tab-btn" onclick="openTab('patient', event)">Patient Activity</button>
    </div>

        <div id="inventory" class="tab-content active">
      <div class="report-actions">
        <button id="openGenerateInventory" class="report-btn">📄 Generate Report</button>
        <button id="exportInventory" class="report-btn">📊 Export CSV</button>
      </div>

      <section class="charts">
        <div class="chart-box">
          <h3>Top 10 Inventory Levels</h3>
          <canvas id="inventoryChart"></canvas>
        </div>
        <div class="chart-box">
          <h3>Stock Forecast (Predicted High-Demand Medicines)</h3>
          <canvas id="forecastChart"></canvas>
        </div>
      </section>
      <p class="small-muted" style="text-align:right">🕓 Last updated: <?= date("M d, Y h:i A") ?></p>
    </div>

        <div id="medicine" class="tab-content">
      <div class="report-actions">
        <button id="openGenerateMedicine" class="report-btn">📄 Generate Report</button>
        <button id="exportMedicine" class="report-btn">📊 Export CSV</button>
      </div>

      <section class="charts">
        <div class="chart-box">
          <h3>Top 5 Dispensed Medicines</h3>
          <canvas id="dispensedChart"></canvas>
        </div>
        <div class="chart-box">
          <h3>Monthly Dispensing Trend</h3>
          <canvas id="monthlyDispenseChart"></canvas>
        </div>
      </section>
    </div>

        <div id="patient" class="tab-content">
      <div class="report-actions">
        <button id="openGeneratePatient" class="report-btn">📄 Generate Report</button>
        <button id="exportPatient" class="report-btn">📊 Export CSV</button>
      </div>

      <section class="charts">
        <div class="chart-box">
          <h3>Patient Visits per Month</h3>
          <canvas id="patientChart"></canvas>
        </div>
        <div class="chart-box">
          <h3>Age Distribution</h3>
          <canvas id="ageChart"></canvas>
        </div>
      </section>
    </div>
  </div>

    <div id="generateModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:9999;">
    <div style="width:520px; margin:6% auto; background:#fff; border-radius:8px; padding:18px;">
      <h3 style="margin-bottom:8px;">Generate Report (Printable)</h3>
      <form id="generateForm" method="POST" action="report_generate.php" target="_blank">
        <div style="margin-bottom:8px;">
          <label>Report Type</label>
          <select name="type" id="reportType" required style="width:100%; padding:8px; margin-top:6px;">
            <option value="inventory">Inventory</option>
            <option value="medicine">Medicine Usage</option>
            <option value="patient">Patient Activity</option>
          </select>
        </div>
        <div style="display:flex; gap:8px; margin-bottom:8px;">
          <div style="flex:1;">
            <label>From</label>
            <input type="date" name="start" class="form-control" style="width:100%; padding:8px; margin-top:6px;">
          </div>
          <div style="flex:1;">
            <label>To</label>
            <input type="date" name="end" class="form-control" style="width:100%; padding:8px; margin-top:6px;">
          </div>
        </div>
        <div style="margin-bottom:8px;">
          <label>Optional filter (medicine/patient)</label>
          <input type="text" name="filter" placeholder="Leave blank for all" style="width:100%; padding:8px; margin-top:6px;">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:8px;">
          <button type="button" id="closeGenerate" class="report-btn" style="background:#ccc; color:#222;">Cancel</button>
          <button type="submit" class="report-btn">Generate</button>
        </div>
      </form>
    </div>
  </div>

<script>
  // PHP -> JS data
  const inventoryData = <?= json_encode($inventory_data) ?>;
  const dispensedData = <?= json_encode($dispensed_data) ?>;
  const monthlyData = <?= json_encode($monthly_dispense) ?>;
  const patientMonths = <?= json_encode($patient_months) ?>;
  const ageGroups = <?= json_encode($age_groups) ?>;
  // ✅ NEW: Use the final, merged and tiered data
  const forecastDataFinal = <?= json_encode($forecastDataFinal) ?>; 
  const quarterlyDataFinal = <?= json_encode($quarterlyDataFinal) ?>;

  // Inventory chart
  const invCtx = document.getElementById('inventoryChart').getContext('2d');
  new Chart(invCtx, {
    type: 'bar',
    data: {
      labels: inventoryData.map(d=>d.med_name),
      datasets: [{ label:'Current Stock Quantity', data: inventoryData.map(d=>+d.quantity), backgroundColor:'#9A3F3F', borderRadius:6 }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
  });

  // ✅ UPDATED Forecast Chart: Uses the clean, tiered data from PHP
  const meds = Object.keys(forecastDataFinal);
  const nextMonth = Object.values(forecastDataFinal);
  const quarterly = Object.values(quarterlyDataFinal);

  const fCtx = document.getElementById('forecastChart').getContext('2d');
  new Chart(fCtx, {
    type: 'bar',
    data: {
      labels: meds,
      datasets: [
        { label: 'Predicted Demand (Next Month)', data: nextMonth, backgroundColor:'#9A3F3F' },
        { label: 'Quarterly Average Demand', data: quarterly, backgroundColor:'#C98B8B' }
      ]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ title:{ display:true, text:'Stock Forecast Overview', color:'#3E0703' } } }
  });

  // Top dispensed pie
  new Chart(document.getElementById('dispensedChart'), {
    type:'pie',
    data:{ labels: dispensedData.map(d=>d.med_name), datasets:[{ data: dispensedData.map(d=>+d.total_dispensed), backgroundColor:['#9A3F3F','#C16262','#EAD2D2','#b45b5b','#703030'] }] },
    options:{ responsive:true }
  });

  // Monthly dispensing trend
  if (monthlyData.length === 0) {
    document.getElementById('monthlyDispenseChart').parentElement.innerHTML = '<div style="padding:40px;color:#999;text-align:center;">No dispensing data available to plot.</div>';
  } else {
    // Enhance label to include year if available
    const labels = monthlyData.map(d => d.year ? `Month ${d.month}, ${d.year}` : `Month ${d.month}`);
    new Chart(document.getElementById('monthlyDispenseChart'), {
      type:'line',
      data:{ labels: labels, datasets:[{ label:'Total Dispensed', data: monthlyData.map(d=>+d.total), borderColor:'#9A3F3F', fill:false }] },
      options:{ responsive:true }
    });
  }

  // Patient visits chart
  if (patientMonths.length === 0) {
    document.getElementById('patientChart').parentElement.innerHTML = '<div style="padding:40px;color:#999;text-align:center;">No patient registration data available.</div>';
  } else {
    // Enhance label to include year if available
    const labels = patientMonths.map(d => d.year ? `Month ${d.month}, ${d.year}` : `Month ${d.month}`);
    new Chart(document.getElementById('patientChart'), {
      type:'bar',
      data:{ labels: labels, datasets:[{ label:'Visits', data: patientMonths.map(d=>+d.count), backgroundColor:'#9A3F3F' }] },
      options:{ responsive:true }
    });
  }

  // Age distribution
  if (ageGroups.length === 0) {
    document.getElementById('ageChart').parentElement.innerHTML = '<div style="padding:40px;color:#999;text-align:center;">No age data available.</div>';
  } else {
    new Chart(document.getElementById('ageChart'), {
      type:'doughnut',
      data:{ labels: ageGroups.map(d=>d.age_group), datasets:[{ data: ageGroups.map(d=>+d.count), backgroundColor:['#9A3F3F','#C16262','#EAD2D2','#b45b5b','#703030'] }] },
      options:{ responsive:true }
    });
  }

  // Tabs
  function openTab(tabName, event) {
    const contents = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');
    contents.forEach(c=>c.classList.remove('active'));
    buttons.forEach(b=>b.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    if (event) event.currentTarget.classList.add('active');
  }

  // Generate Report modal open/close
  const modal = document.getElementById('generateModal');
  ['openGenerateInventory','openGenerateMedicine','openGeneratePatient'].forEach(id=>{
    const el = document.getElementById(id);
    if (el) el.addEventListener('click', ()=> {
      if (id==='openGenerateInventory') document.getElementById('reportType').value='inventory';
      if (id==='openGenerateMedicine') document.getElementById('reportType').value='medicine';
      if (id==='openGeneratePatient') document.getElementById('reportType').value='patient';
      modal.style.display='block';
    });
  });
  document.getElementById('closeGenerate').addEventListener('click', ()=> modal.style.display='none');

  // Export CSV buttons
  document.getElementById('exportInventory').addEventListener('click', ()=> window.location.href='export_csv.php?type=inventory');
  document.getElementById('exportMedicine').addEventListener('click', ()=> window.location.href='export_csv.php?type=medicine');
  document.getElementById('exportPatient').addEventListener('click', ()=> window.location.href='export_csv.php?type=patient');

  // Keep modal open briefly then close after form submit
  document.getElementById('generateForm').addEventListener('submit', ()=> {
    setTimeout(()=> modal.style.display='none', 400);
  });

</script>
</body>
</html>