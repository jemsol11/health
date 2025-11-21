<?php
include 'db_connect.php';
include 'run_forecast.php';

// --- SUMMARY CARDS ---

// 1️⃣ Today's Medicines Dispensed
$todays_dispensed_result = $conn->query("
  SELECT SUM(quantity_given) AS total_today
  FROM medicine_assistance
  WHERE DATE(date_given) = CURDATE()
");
$todays_dispensed = $todays_dispensed_result->fetch_assoc()['total_today'] ?? 0;

// 2️⃣ Total Medicines Dispensed (All time)
$total_dispensed_result = $conn->query("
  SELECT SUM(quantity_given) AS total_all
  FROM medicine_assistance
");
$total_dispensed = $total_dispensed_result->fetch_assoc()['total_all'] ?? 0;

// 3️⃣ Total Patients
$total_patients_result = $conn->query("
  SELECT COUNT(*) AS total_patients FROM patients
");
$total_patients = $total_patients_result->fetch_assoc()['total_patients'] ?? 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Medicine Assistance</title>
  <link rel="stylesheet" href="styles/med.css">

  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <style>
    .tab-panel {
      display: none !important;
    }
    .tab-panel.active {
      display: block !important;
    }
    .table-controls {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 15px;
        align-items: center;
    }
    .table-controls input[type="text"],
    .table-controls input[type="date"] {
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }
    .table-controls .btn-secondary {
        background: #9A3F3F;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>  

  <main class="content">
    <div class="header-section">
      <h2>Medicine Assistance</h2>
    </div>

    <div class="summary-cards">
      <div class="card">
        <h3>Today's Medicines Dispensed</h3>
        <p><?php echo $todays_dispensed ?: 0; ?></p>
      </div>
      <div class="card">
        <h3>Total Medicines Dispensed</h3>
        <p><?php echo $total_dispensed ?: 0; ?></p>
      </div>
      <div class="card">
        <h3>Total Patients</h3>
        <p><?php echo $total_patients ?: 0; ?></p>
      </div>
    </div>

    <div class="tabs">
      <button class="tab active" data-tab="dispense">Dispense Medicine</button>
      <button class="tab" data-tab="history">Distribution History</button>
      <button class="tab" data-tab="addedHistory">Stock-In History</button>
    </div>

    <section class="tab-content">

      <!-- DISPENSE MEDICINE -->
      <div class="tab-panel active" id="dispense">
        <h3>Dispense Medicine</h3>
        <form action="give_medicine.php" method="POST">
          <div class="form-group">
            <label for="patient">Select Patient *</label>
            <select id="patient" name="patient_id" required>
              <option value="">Select Patient</option>
              <?php
              $patients = $conn->query("SELECT patient_id, fullname FROM patients ORDER BY fullname ASC");
              while ($p = $patients->fetch_assoc()) {
                  echo "<option value='{$p['patient_id']}'>{$p['fullname']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="medicine">Select Medicine *</label>
            <select id="medicine" name="med_id" required>
              <option value="">Select medicine</option>
              <?php
              $meds = $conn->query("SELECT med_id, med_name, quantity FROM medicines ORDER BY med_name ASC");
              while ($m = $meds->fetch_assoc()) {
                  echo "<option value='{$m['med_id']}'>{$m['med_name']} (Stock: {$m['quantity']})</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label for="quantity">Quantity *</label>
            <input type="number" id="quantity" name="quantity_given" placeholder="Enter quantity" required>
          </div>
          <div class="form-group">
            <label for="doctor">Prescriber/Doctor *</label>
            <input type="text" id="doctor" name="given_by" placeholder="Dr. Name" required>
          </div>
          <button type="submit" class="btn btn-primary">Dispense Medicine</button>
        </form>
      </div>

     <!-- DISTRIBUTION HISTORY -->
<div class="tab-panel" id="history">
    <h3>Distribution History</h3>
    <div class="table-controls">
        <input type="text" id="searchHistory" placeholder="Search by patient, medicine, or given by">
        <label>From:</label>
        <input type="date" id="dateFromHistory">
        <label>To:</label>
        <input type="date" id="dateToHistory">
        <button class="btn-secondary" id="exportHistory">📤 Export Logs</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Patient</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Given By</th>
            </tr>
        </thead>
        <tbody id="historyTableBody">
            <?php
            $history = $conn->query("
              SELECT ma.date_given, p.fullname, m.med_name, ma.quantity_given, ma.given_by
              FROM medicine_assistance ma
              JOIN patients p ON ma.patient_id = p.patient_id
              JOIN medicines m ON ma.med_id = m.med_id
              ORDER BY ma.date_given DESC
            ");
            if ($history->num_rows > 0) {
                while ($row = $history->fetch_assoc()) {
                    echo "<tr>
                      <td>{$row['date_given']}</td>
                      <td>{$row['fullname']}</td>
                      <td>{$row['med_name']}</td>
                      <td>{$row['quantity_given']}</td>
                      <td>{$row['given_by']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>No records yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- STOCK-IN HISTORY -->
<div class="tab-panel" id="addedHistory">
    <h3>Stock-In History</h3>
    <div class="table-controls">
        <input type="text" id="searchInventory" placeholder="Search by medicine">
        <input type="text" id="userInventory" placeholder="Search by user">
        <label>From:</label>
        <input type="date" id="dateFromInventory">
        <label>To:</label>
        <input type="date" id="dateToInventory">
        <button class="btn-secondary" id="exportInventory">📤 Export Logs</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date Added</th>
                <th>Medicine Name</th>
                <th>Quantity Added</th>
                <th>Added By</th>
            </tr>
        </thead>
        <tbody id="inventoryTableBody">
            <?php
            $addedHistory = $conn->query("
              SELECT 
                  at.timestamp AS date_added, 
                  m.med_name, 
                  at.details,
                  u.username AS added_by
              FROM 
                  audit_trail at
              JOIN
                  users u ON at.user_id = u.user_id
              JOIN
                  medicines m ON at.details LIKE CONCAT('%', m.med_name, '%')
              WHERE 
                  at.action = 'Add Inventory' OR at.action = 'Edit Inventory'
              ORDER BY at.timestamp DESC
            ");
            if ($addedHistory && $addedHistory->num_rows > 0) {
                while ($row = $addedHistory->fetch_assoc()) {
                    $details = $row['details'];
                    $quantity_match = '';
                    if (preg_match('/(\d+)\s+stock/', $details, $matches)) {
                        $quantity_match = $matches[1];
                    } elseif (preg_match('/\((?P<quantity>\d+)\s+(Tablet|Capsule|Syrup|Drop|Vial|Ampoule|Bottle)/', $details, $matches)) {
                        $quantity_match = $matches['quantity'];
                    }
                    echo "<tr>
                      <td>{$row['date_added']}</td>
                      <td>{$row['med_name']}</td>
                      <td>" . ($quantity_match ?: 'N/A') . "</td>
                      <td>{$row['added_by']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>No stock addition records found in audit trail.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

    </section>
  </main>

  <script>
    // --- Tab Switcher ---
    const tabs = document.querySelectorAll(".tab");
    const panels = document.querySelectorAll(".tab-panel");
    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        panels.forEach(panel => panel.classList.remove("active"));
        document.getElementById(tab.dataset.tab).classList.add("active");
      });
    });

    // --- Select2 ---
    $(document).ready(function() {
      $('#patient').select2({ placeholder: "Search patient...", allowClear: true, width: '100%' });
      $('#medicine').select2({ placeholder: "Search medicine...", allowClear: true, width: '100%' });
    });

    // --- Export CSV ---
    function exportTableToCSV(tableBodyId, filename) {
        const tableBody = document.getElementById(tableBodyId);
        if (!tableBody) return;
        const headers = Array.from(tableBody.closest('table').querySelectorAll('th')).map(th => th.textContent.trim());
        const rows = [headers.join(',')];
        Array.from(tableBody.querySelectorAll('tr')).forEach(row => {
            if(row.style.display !== 'none'){
                const cols = Array.from(row.cells).map(cell => cell.textContent.trim().replace(/,/g,''));
                rows.push(cols.join(','));
            }
        });
        const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename + '.csv';
        a.click();
    }

    // --- Filter Function ---
    function setupFilters(searchId, dateFromId, dateToId, tableBodyId, exportId, extraSearchId=null, extraColIndex=null){
        const searchInput = document.getElementById(searchId);
        const dateFrom = document.getElementById(dateFromId);
        const dateTo = document.getElementById(dateToId);
        const tableBody = document.getElementById(tableBodyId);
        const exportBtn = document.getElementById(exportId);
        const extraSearch = extraSearchId ? document.getElementById(extraSearchId) : null;

        function filterRows(){
            const sVal = searchInput ? searchInput.value.toLowerCase() : '';
            const fromDate = dateFrom.value ? new Date(dateFrom.value) : null;
            const toDate = dateTo.value ? new Date(dateTo.value) : null;
            const extraVal = extraSearch && extraColIndex !== null ? extraSearch.value.toLowerCase() : null;

            Array.from(tableBody.querySelectorAll('tr')).forEach(row=>{
                const rowDate = new Date(row.cells[0].textContent.split(' ')[0]);
                let show = true;

                if(sVal && !(row.cells[1].textContent.toLowerCase().includes(sVal) ||
                             row.cells[2].textContent.toLowerCase().includes(sVal) ||
                             (row.cells[4] ? row.cells[4].textContent.toLowerCase().includes(sVal) : false))) show = false;

                if(extraVal && extraColIndex!==null && !row.cells[extraColIndex].textContent.toLowerCase().includes(extraVal)) show = false;

                if(fromDate && rowDate < fromDate) show = false;
                if(toDate){
                    const limit = new Date(toDate);
                    limit.setDate(limit.getDate()+1);
                    if(rowDate >= limit) show = false;
                }
                row.style.display = show ? '' : 'none';
            });
        }

        [searchInput, dateFrom, dateTo, extraSearch].forEach(el=>{ if(el) el.addEventListener('input', filterRows); });
        if(exportBtn) exportBtn.addEventListener('click', ()=>exportTableToCSV(tableBodyId, searchId+'_logs'));
    }

    // --- Initialize Filters ---
    setupFilters('searchHistory','dateFromHistory','dateToHistory','historyTableBody','exportHistory');
    setupFilters('searchInventory','dateFromInventory','dateToInventory','inventoryTableBody','exportInventory','userInventory',3);
</script>

</body>
</html>
