<?php
include 'db_connect.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Safely get session values with defaults
$role = $_SESSION['role'] ?? 'Guest';
$username = $_SESSION['username'] ?? 'Guest';

// Fetch inventory from DB
$sql = "
SELECT m.*, MAX(ma.date_given) AS last_dispensed
FROM medicines m
LEFT JOIN medicine_assistance ma ON m.med_id = ma.med_id
GROUP BY m.med_id
ORDER BY last_dispensed DESC, m.med_id DESC
";
$result = $conn->query($sql);



// Total number of items
$totalItems = $conn->query("SELECT COUNT(*) AS total FROM medicines")->fetch_assoc()['total'];

//// Low Stock (≤10 and NOT expiring soon)
// Low Stock (≤10, regardless of expiry)
$lowStock = $conn->query("
    SELECT COUNT(*) AS total
    FROM medicines
    WHERE quantity <= 10
")->fetch_assoc()['total'];

// Expiring Soon (within 30 days, but not already counted as low stock)
$expiringSoon = $conn->query("
    SELECT COUNT(*) AS total
    FROM medicines
    WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
      AND expiry_date >= CURDATE()
      AND quantity > 10
")->fetch_assoc()['total'];

if (isset($_GET['message'])) {
    echo "<script>alert('" . htmlspecialchars($_GET['message']) . "');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Management</title>
  <link rel="stylesheet" href="styles/inv.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Modal background */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background: rgba(214, 190, 190, 0);
    }

    /* Modal content */
    .modal-content {
      background: #fff;
      margin: 5% auto;
      padding: 20px;
      border-radius: 10px;
      width: 600px;
      max-width: 95%;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.2);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #ddd;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }

    .modal-header h2 {
      font-size: 18px;
      margin: 0;
    }

    .close {
      cursor: pointer;
      font-size: 22px;
      font-weight: bold;
      border: none;
      background: none;
    }

    .modal-body {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }

    .modal-body label {
      font-weight: 600;
      margin-top: 10px;
      display: block;
    }

    .modal-body input,
    .modal-body select,
    .modal-body textarea {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .modal-body textarea {
      grid-column: span 2;
    }

    .modal-footer {
      display: flex;
      justify-content: flex-end;
      margin-top: 20px;
    }

    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn-primary {
      padding: 10px 20px;
      font-size: 15px;
      font-weight: 600;
      background: #642921ff;
      color: #ffffffff;
    }
    .btn.cancel {
      background: #f3f3f3;
      margin-right: 10px;
    }
    .btn.green {
      background: #723917ff;
      color: #fff;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
  </header>

  <!-- Page Header -->
  <div class="page-header">
  <h2>Inventory Management</h2>
  <div class="header-buttons">
    <button id="notifBtn" class="btn-notif">
      🔔 Alerts
      <span class="notif-count">
        <?= $lowStock + $expiringSoon; ?>
      </span>
    </button>
    <button class="btn-primary" id="openModalBtn">+ Add Inventory</button>
  </div>
</div>
    <!-- Summary Cards -->
    <!-- Summary Cards -->

<section class="summary-cards">
  <div class="card">
    <h3>Total Items</h3>
    <p id="total-items"><?= $totalItems; ?></p>
  </div>

  <div class="card">
    <h3>Low Stock Alerts</h3>
    <p id="low-stock"><?= $lowStock; ?></p> </div>

  <div class="card">
    <h3>Expiring Soon</h3>
    <p id="expiring-soon"><?= $expiringSoon; ?></p> </div>
</section>
<div id="notifDropdown" class="notif-dropdown">
  <h4>⚠️ Alerts</h4>
  <div class="notif-section">
    <h5>Low Stock (≤10)</h5>
    <ul>
      <?php
      $lowStockItems = $conn->query("
        SELECT med_name, quantity FROM medicines
        WHERE quantity <= 10
        ORDER BY quantity ASC
      ");
      if ($lowStockItems->num_rows > 0) {
        while ($item = $lowStockItems->fetch_assoc()) {
          echo "<li>🩺 " . htmlspecialchars($item['med_name']) . " - " . $item['quantity'] . " left</li>";
        }
      } else {
        echo "<li>No low stock medicines.</li>";
      }
      ?>
    </ul>
  </div>

  <div class="notif-section">
    <h5>Expiring Soon (within 30 days)</h5>
    <ul>
      <?php
      $expiringItems = $conn->query("
        SELECT med_name, expiry_date FROM medicines
        WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
          AND expiry_date >= CURDATE()
        ORDER BY expiry_date ASC
      ");
      if ($expiringItems->num_rows > 0) {
        while ($item = $expiringItems->fetch_assoc()) {
          echo "<li>💊 " . htmlspecialchars($item['med_name']) . " - Expires on " . $item['expiry_date'] . "</li>";
        }
      } else {
        echo "<li>No expiring medicines soon.</li>";
      }
      ?>
    </ul>
  </div>
</div>


    <section class="forecast-charts-container">
  <!-- General Inventory Forecast -->
  <div class="inventory-forecast">
    <h3>Inventory Forecast</h3>
    <canvas id="forecastChart"></canvas>
  </div>

  <!-- Specific Medicine Forecast -->
  <div class="inventory-forecast">
    <h3>Medicine Forecast</h3>
    <select id="medicineSelect">
      <option value="">-- Select Medicine --</option>
      <?php
        $meds = $conn->query("SELECT med_name FROM medicines ORDER BY med_name ASC");
        while ($m = $meds->fetch_assoc()) {
          echo "<option value='".htmlspecialchars($m['med_name'])."'>".htmlspecialchars($m['med_name'])."</option>";
        }
      ?>
    </select>
    <canvas id="specificForecastChart"></canvas>
  </div>
</section>


    
   

   <!-- Inventory Table -->
<section class="table-container">
<h3>Inventory List</h3>   

<div class="table-controls">
      <input type="text" placeholder="Search medicines...">
      <select>
  <option>All Categories</option>
  <option>Antibiotic</option>
  <option>Analgesic</option>
  <option>Vitamin</option>
  <option>Antiseptic</option>
  <option>Vaccine</option>
</select>

<select>
  <option>All Stock</option>
  <option value="low">Low</option>
  <option value="sufficient">Sufficient</option>
</select>


<select id="expirationFilter">
  <option value="all">All Expiration</option>
  <option value="expired">Expired</option>
  <option value="soon">Expiring Soon</option>
  <option value="safe">Safe</option>
</select>


      <button class="btn-secondary">📤 Export</button>
    </div>
  <table>
    <thead>
      <tr>
        <th>Medicine</th>
        <th>Category</th>
         <th>Unit</th>
        <th>Stock Level</th>
        <th>Batch Number</th>
        <th>Expiration</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['med_name']); ?></td>
            <td><?= htmlspecialchars($row['category']); ?></td>
              <td><?= $row['unit']; ?></td>
            <td><?= $row['quantity']; ?></td>
            <td><?= $row['batch_number']; ?></td>
            <td><?= $row['expiry_date']; ?></td>
            <td class="actions">
  <button class="btn-action edit" data-id="<?= $row['med_id']; ?>">
    ✏️ Edit
  </button>
  <a href="delete_inventory.php?id=<?= $row['med_id']; ?>" 
     class="btn-action delete"
     onclick="return confirm('Are you sure you want to delete this item?');">
    🗑️ Delete
  </a>
</td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5">No inventory items found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>

  <!-- Add Inventory Modal -->
  <div id="inventoryModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add Inventory Item</h2>
        <button class="close" id="closeModal">&times;</button>
      </div>
      <form action="save_inventory.php" method="POST">
        <div class="modal-body">
          <div>
            <label>Medicine Name *</label>
            <input type="text" name="medicine_name" placeholder="e.g., Paracetamol 500mg" required>
          </div>
          <div>
            <label>Category *</label>
            <select name="category" required>
              <option value="">Select category</option>
              <option value="antibiotic">Antibiotic</option>
              <option value="analgesic">Analgesic</option>
              <option value="vitamin">Vitamin</option>
              <option value="antiseptic">Antiseptic</option>
              <option value="other">Vaccine</option>
            </select>
          </div>
          <div>
<div>
  <label>Unit *</label>
  <select name="unit" required>
    <option value="">Select unit</option>
    <option value="Tablet">Tablet</option>
    <option value="Capsule">Capsule</option>
    <option value="Syrup">Syrup (bottle)</option>
    <option value="Drop">Drop (bottle)</option>
    <option value="Vial">Vial</option>
    <option value="Ampoule">Ampoule</option>
    <option value="Bottle">Bottle (ml)</option>
  
  </select>
</div>
          
            <label>Initial Quantity *</label>
            <input type="number" name="quantity" required>
          </div>
         
          <div>
            <label>Batch Number *</label>
           <input type="text" name="batch_number" placeholder="Batch number" required>
          </div>
          <div>
            <label>Expiration Date *</label>
            <input type="date" name="expiration_date" required>
          </div>
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn cancel" id="cancelBtn">Cancel</button>
          <button type="submit" class="btn ">Add Item</button>
        </div>
      </form>
    </div>
  </div>


  <!-- Edit Inventory Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Inventory Item</h2>
      <button class="close" id="closeEditModal">&times;</button>
    </div>
    <form id="editForm" method="POST" action="update_inventory.php">
      <input type="hidden" name="med_id" id="edit_med_id">
      <div class="modal-body">
        <div>
          <label>Medicine Name *</label>
          <input type="text" name="medicine_name" id="edit_med_name" required>
        </div>
        <div>
          <label>Category *</label>
          <select name="category" id="edit_category" required>
            <option value="">Select category</option>
            <option value="antibiotic">Antibiotic</option>
            <option value="analgesic">Analgesic</option>
            <option value="vitamin">Vitamin</option>
            <option value="antiseptic">Antiseptic</option>
            <option value="other">Vaccine</option>
          </select>
        </div>
        <div>
          <label>Unit *</label>
          <select name="unit" id="edit_unit" required>
            <option value="">Select unit</option>
            <option value="Tablet">Tablet</option>
            <option value="Capsule">Capsule</option>
            <option value="Syrup">Syrup (bottle)</option>
            <option value="Drop">Drop (bottle)</option>
            <option value="Vial">Vial</option>
            <option value="Ampoule">Ampoule</option>
            <option value="Bottle">Bottle (ml)</option>
          </select>
        </div>
        <div>
          <label>Current Quantity</label>
          <input type="number" id="edit_quantity_current" readonly>
        </div>
        <div>
          <label>Add Quantity *</label>
          <input type="number" name="quantity_add" value="0" min="0" required>
        </div>
        <div>
          <label>Batch Number *</label>
          <input type="text" name="batch_number" id="edit_batch" required>
        </div>
        <div>
          <label>Expiration Date *</label>
          <input type="date" name="expiry_date" id="edit_expiry" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancel" id="cancelEditBtn">Cancel</button>
        <button type="submit" class="btn green">Update Item</button>
      </div>
    </form>
  </div>
</div>

<script>
  const editModal = document.getElementById("editModal");
  const closeEditModal = document.getElementById("closeEditModal");
  const cancelEditBtn = document.getElementById("cancelEditBtn");

 document.querySelectorAll('.btn-action.edit').forEach(button => {
  button.addEventListener('click', function(e) {
    e.preventDefault();
    const tr = this.closest('tr');

    // ✅ Use the correct med_id from the button’s data attribute
    document.getElementById('edit_med_id').value = this.dataset.id;

    // ✅ Fill the modal with correct row data
    document.getElementById('edit_med_name').value = tr.children[0].textContent.trim();
    document.getElementById('edit_category').value = tr.children[1].textContent.trim().toLowerCase();
    document.getElementById('edit_unit').value = tr.children[2].textContent.trim();
    document.getElementById('edit_quantity_current').value = tr.children[3].textContent.trim();
    document.getElementById('edit_batch').value = tr.children[4].textContent.trim();
    document.getElementById('edit_expiry').value = tr.children[5].textContent.trim();

    editModal.style.display = "block";
  });
});


  closeEditModal.onclick = cancelEditBtn.onclick = function() {
    editModal.style.display = "none";
  }

  window.onclick = function(event) {
    if (event.target == editModal) editModal.style.display = "none";
  }
</script>

  <script>
    const modal = document.getElementById("inventoryModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeModal = document.getElementById("closeModal");
    const cancelBtn = document.getElementById("cancelBtn");

    openModalBtn.onclick = function() {
      modal.style.display = "block";
    }

    closeModal.onclick = function() {
      modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>

<script>
  // Search Function
  const searchInput = document.querySelector('.table-controls input');
  const tableRows = document.querySelectorAll('tbody tr');

  searchInput.addEventListener('keyup', function () {
    const query = this.value.toLowerCase();

    tableRows.forEach(row => {
      const name = row.children[0].textContent.toLowerCase();
      row.style.display = name.includes(query) ? '' : 'none';
    });
  });
</script>

<script>
  const categoryFilter = document.querySelectorAll('.table-controls select')[0];
  const stockFilter = document.querySelectorAll('.table-controls select')[1];
  const expirationFilter = document.getElementById('expirationFilter'); // ✅ ADDED

  categoryFilter.addEventListener('change', filterTable);
  stockFilter.addEventListener('change', filterTable);
  expirationFilter.addEventListener('change', filterTable); // ✅ ADDED

  function filterTable() {
    const category = categoryFilter.value.toLowerCase();
    const stock = stockFilter.value.toLowerCase();
    const expiration = expirationFilter.value.toLowerCase(); // ✅ ADDED

    const now = new Date();
    const future = new Date();
    future.setDate(now.getDate() + 30); // ✅ 30 days from now

    tableRows.forEach(row => {
      const medCategory = row.children[1].textContent.toLowerCase();
      const stockLevel = parseInt(row.children[3].textContent);
      const expiryDate = new Date(row.children[5].textContent); // ✅ Expiration column
      let show = true;

      // Category filter
      if (category !== 'all categories' && medCategory !== category) show = false;

      // Stock filter
      if (stock !== 'all stock') {
        if (stock === 'low' && stockLevel > 10) show = false;
        if (stock === 'sufficient' && stockLevel <= 10) show = false;
      }

      // ✅ Expiration filter
    // Expiration filter
if (expiration !== 'all') {
  const isExpired = expiryDate < now;
  const isSoon = expiryDate >= now && expiryDate <= future;
  if (expiration === 'expired' && !isExpired) show = false;
  if (expiration === 'soon' && !isSoon) show = false;
  if (expiration === 'safe' && (isSoon || isExpired)) show = false;
}


      row.style.display = show ? '' : 'none';
    });
  }

</script>




<script>
  const exportBtn = document.querySelector('.btn-secondary');

  exportBtn.addEventListener('click', () => {
    const rows = [['Medicine', 'Category', 'Unit', 'Stock Level', 'Batch Number', 'Expiration']];

document.querySelectorAll('tbody tr').forEach(row => {
  if (row.style.display !== 'none') {
    const cols = [
      row.children[0].textContent.trim(), // Medicine
      row.children[1].textContent.trim(), // Category
      row.children[2].textContent.trim(), // Unit
      row.children[3].textContent.trim(), // Stock Level
      row.children[4].textContent.trim(), // Batch Number
      row.children[5].textContent.trim()  // Expiration Date ✅
    ];
    rows.push(cols);
  }
});

    let csvContent = rows.map(e => e.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'inventory.csv';
    a.click();
  });
</script>


<!-- ========== FORECAST CHART ========== -->
<canvas id="forecastChart" width="600" height="300"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Promise.all([
  fetch('forecast_api.php').then(r => r.json()), // ⬅️ CHANGED HERE
  fetch('dispensed_results.php').then(r => r.json())
])
.then(([forecastData, dispensedData]) => {
  const allLabels = Array.from(new Set([
    ...Object.keys(forecastData),
    ...Object.keys(dispensedData)
  ]));

  const predictedValues = allLabels.map(med => forecastData[med] || 0);
  const dispensedValues = allLabels.map(med => dispensedData[med] || 0);

  const ctx = document.getElementById('forecastChart').getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: allLabels,
      datasets: [
  {
    label: 'Dispensed (Actual)',
    data: dispensedValues,
    backgroundColor: '#852a1eff',
    borderRadius: 6
  },
  {
    label: 'Predicted (Next Month)',
    data: predictedValues,
    backgroundColor: '#CF7F71',
    borderRadius: 6
  }
]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'top' },
        title: {
          display: true,
          text: 'Medicine Dispensed vs Forecasted Demand',
          font: { size: 16 }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Quantity' }
        },
        x: {
          title: { display: true, text: 'Medicine' },
          ticks: { autoSkip: false, maxRotation: 45, minRotation: 45 }
        }
      }
    }
  });
})
.catch(err => console.error("⚠️ Error loading data:", err));
</script>

<script>
const medicineSelect = document.getElementById('medicineSelect');
let specificChart = null;

medicineSelect.addEventListener('change', function() {
  const medName = this.value;
  if (!medName) return;

  Promise.all([
  fetch('forecast_api.php').then(r => r.json()), // ⬅️ CHANGED HERE
  fetch('dispensed_results.php').then(r => r.json())
])
  .then(([forecastData, dispensedData]) => {
    const predicted = forecastData[medName] ? [forecastData[medName]] : [0];
    const dispensed = dispensedData[medName] ? [dispensedData[medName]] : [0];

    const ctx = document.getElementById('specificForecastChart').getContext('2d');

    // Destroy previous chart if exists
    if (specificChart) specificChart.destroy();

    specificChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [medName],
        datasets: [
          {
            label: 'Dispensed (Actual)',
            data: dispensed,
            backgroundColor: '#852a1eff',
            borderRadius: 6
          },
          {
            label: 'Predicted (Next Month)',
            data: predicted,
            backgroundColor: '#CF7F71',
            borderRadius: 6
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: {
            display: true,
            text: 'Forecast vs Actual for ' + medName,
            font: { size: 16 }
          }
        },
        scales: {
          y: { beginAtZero: true, title: { display: true, text: 'Quantity' } },
          x: { title: { display: true, text: 'Medicine' } }
        }
      }
    });
  })
  .catch(err => console.error("⚠️ Error loading data:", err));
});
</script>


<script>
  const notifBtn = document.getElementById('notifBtn');
  const notifDropdown = document.getElementById('notifDropdown');

  notifBtn.addEventListener('click', function(e) {
    e.stopPropagation(); // Prevent window click from immediately hiding it
    notifDropdown.style.display = (notifDropdown.style.display === 'block') ? 'none' : 'block';
  });

  // Hide dropdown if user clicks outside
  window.addEventListener('click', function(e) {
    if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
      notifDropdown.style.display = 'none';
    }
  });

  tableRows.forEach(row => {
  const expiryDate = new Date(row.children[5].textContent);
  if (expiryDate < new Date()) {
    row.style.backgroundColor = '#f8d7da'; // light red
  } else {
    row.style.backgroundColor = '';
  }
});

</script>

</body>
</html>
