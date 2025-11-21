<?php
session_start();
include "db_connect.php";

if (isset($_GET['success']) && $_GET['success'] === 'record_added') {
    echo "<script>alert('Medical record added successfully!');</script>";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_patient'])) {
    $fullname     = $_POST['fullname'];
    $dob          = $_POST['dob'];
    $age          = trim($_POST['age']);
    $sex          = $_POST['sex'];
    $blood_type   = $_POST['blood_type'];
    $purok        = $_POST['purok'];
    $contact      = $_POST['contact'];
    $civil_status = $_POST['civil_status'];
    $occupation   = $_POST['occupation'];
    $emergency    = $_POST['emergency'];

    // Insert patient
    $sql = "INSERT INTO patients (fullname, dob, age, sex, blood_type, purok, contact, civil_status, occupation, emergency_contact) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $fullname, $dob, $age, $sex, $blood_type, $purok, $contact, $civil_status, $occupation, $emergency);

    // ✅ Determine user_id or employee_id from session
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
        // ✅ Audit Trail (success) - NOW WITH employee_id
        $action = "Added new patient";
        $details = "Patient: " . $fullname;
        $status = "Success";

        $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status) VALUES (?, ?, ?, ?, ?)");
        $audit->bind_param("issss", $user_id, $employee_id, $action, $details, $status);
        $audit->execute();
        $audit->close();

        echo "<script>alert('Patient added successfully!'); window.location='pat.php';</script>";
    } else {
        // ✅ Audit Trail (failed) - NOW WITH employee_id
        $action = "Add patient failed";
        $details = "Attempted to add patient: " . $fullname;
        $status = "Failed";

        $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status) VALUES (?, ?, ?, ?, ?)");
        $audit->bind_param("issss", $user_id, $employee_id, $action, $details, $status);
        $audit->execute();
        $audit->close();

        echo "<script>alert('Error adding patient.');</script>";
    }
    $stmt->close();
}

// Fetch patients for display
$patients = $conn->query("SELECT * FROM patients ORDER BY patient_id DESC");
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Patients </title>
  <link rel="stylesheet" href="styles/pat.css">

  <style>
    /* === MODAL STYLING === */
    .modal {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal.hidden { display: none; }

    .modal-content {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      width: 700px;
      max-width: 95%;
      max-height: 90vh;
      overflow-y: auto;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .modal-content.large { width: 750px; }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    .modal-header h2 { margin: 0; }
    .modal-header .close {
      cursor: pointer;
      font-size: 20px;
    }

    .modal-form {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
    }
    .form-row.full { grid-column: span 2; }

    .modal-form label {
      display: block;
      font-weight: 600;
      margin-bottom: 5px;
    }
    .modal-form input, 
    .modal-form select, 
    .modal-form textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    textarea { resize: vertical; }

    .modal-footer {
      grid-column: span 2;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 15px;
    }

    .btn.primary {
      padding: 10px 20px;
      font-size: 15px;
      font-weight: 600;
      background: #692924ff;
      color: #ffffffff;
      border: none;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
    }
    .btn.outline {
       padding: 10px 20px;
      font-size: 15px;
      font-weight: 600;
      color: #ffffffff;
      background: #532020ff;
      color:white;
      border: 1px solid #ccc;
      padding: 8px 15px;
      border-radius: 6px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'navbar.php'; ?>  

  <!-- PAGE CONTENT -->
  <main class="content">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1>Patient Management</h1>
      
      </div>

      <div class="header-actions">
        <button id="addRecordBtn" class="btn outline">📋 Add Medical Record</button>
        <button id="addPatientBtn" class="btn primary">＋ Add Patient</button>
      </div>
    </div>

    <!-- Patient Registry -->
   <section class="table-card">
  <div class="table-header">
    <h3>Patient Registry</h3>
  </div>

  <!-- 🔍 Search and Filters -->
  <div class="table-controls" style="margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
    <input 
      type="text" 
      id="searchInput" 
      placeholder="Search by patient name..." 
      style="flex: 1; padding: 8px; border: 2px solid #1d1111ff; border-radius: 6px;"
    >

    <select id="purokFilter" style="padding: 8px; border: 2px solid #181111ff; border-radius: 6px;">
      <option value="all">All Puroks</option>
      <option value="Purok 1">Purok 1</option>
      <option value="Purok 2">Purok 2</option>
      <option value="Purok 3">Purok 3</option>
      <option value="Purok 4">Purok 4</option>
      <option value="Purok 5">Purok 5</option>
      <option value="Purok 6">Purok 6</option>
      <option value="Purok 7">Purok 7</option>
      <option value="Purok 8">Purok 8</option>
    </select>

    <select id="sexFilter" style="padding: 8px; border: 2px solid #110b0bff; border-radius: 6px;">
      <option value="all">All Sex</option>
      <option value="Male">Male</option>
      <option value="Female">Female</option>
    </select>

    <button id="clearFilters" class="btn outline">Reset Filters</button>
  </div>

  <table class="patient-table">
  <thead>
    <tr>
      <th>Patient ID</th>
      <th>Patient Name</th>
      <th>Age / Sex</th>
      <th>Purok</th>
      <th>Contact</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody id="patientTable">
    <?php if ($patients->num_rows > 0): ?>
      <?php while ($row = $patients->fetch_assoc()): ?>
        <tr>
           <td><?= htmlspecialchars($row['patient_id']); ?></td>
          <td><?= htmlspecialchars($row['fullname']); ?></td>
          <td><?= htmlspecialchars($row['age']) . " / " . htmlspecialchars($row['sex']); ?></td>
          <td><?= htmlspecialchars($row['purok']); ?></td>
          <td><?= htmlspecialchars($row['contact']); ?></td>
          <td class="action-icons">
            <a href="view_patient.php?id=<?= $row['patient_id']; ?>" class="view" title="View"> 🔍View</a>
            <a href="edit_patient.php?id=<?= $row['patient_id']; ?>" class="edit" title="Edit">✏️Edit</a>
            <a href="delete_patient.php?id=<?= $row['patient_id']; ?>" class="delete" title="Delete" onclick="return confirm('Delete this patient?');">🗑️Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="5" style="text-align:center;">No patients found.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

</section>

  </main>

  <!-- ================== MODALS ================== -->

  <!-- Add Medical Record Modal -->
  <div id="recordModal" class="modal hidden">
    <div class="modal-content large">
      <div class="modal-header">
        <h2>Add Medical Record</h2>
        <span class="close" onclick="closeModal('recordModal')">&times;</span>
      </div>
      <form action="add_medical_record.php" method="POST" class="modal-form">
        <div class="form-row">
          <label>Patient *</label>
         <input 
  type="text" 
  id="patientSearch" 
  placeholder="Search patient name..." 
 
>

<!-- 👥 Patient Dropdown -->
<select name="patient_id" id="patient_id" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:6px;">
  <option value="">Select patient</option>
  <?php
  $patientList = $conn->query("SELECT patient_id, fullname FROM patients ORDER BY fullname ASC");
  while ($p = $patientList->fetch_assoc()) {
    echo "<option value='{$p['patient_id']}'>{$p['fullname']}</option>";
  }
  ?>
</select>
        </div>

        <div class="form-row">
          <label>Date</label>
          <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="form-row full">
          <label>Chief Complaint *</label>
          <input type="text" name="complaint" required>
        </div>

        <div class="form-row">
          <label>Diagnosis</label>
          <input type="text" name="diagnosis">
        </div>
        <div class="form-row">
          <label>Treatment</label>
          <input type="text" name="treatment">
        </div>

        

        <div class="form-row">
          <label>Blood Pressure</label>
          <input type="text" name="bp">
        </div>
        <div class="form-row">
          <label>Temperature</label>
          <input type="text" name="temperature">
        </div>
        <div class="form-row">
          <label>Weight</label>
          <input type="text" name="weight">
        </div>
        <div class="form-row">
          <label>Height</label>
          <input type="text" name="height">
        </div>

        <div class="form-row full">
          <label>Notes</label>
          <textarea name="notes"></textarea>
        </div>
<div class="form-row">
  <label>Doctor</label>
  <input type="text" name="doctor" placeholder="Attending doctor">
</div>
<div class="form-row full">
  <label>Medicines Dispensed</label>
  <div id="medicine-container">
    <div class="medicine-row">
      <select name="med_id[]" required>
        <option value="">Select medicine</option>
        <?php
        $meds = $conn->query("
    SELECT m.med_id, m.med_name, m.quantity, MAX(ma.date_given) AS last_dispensed
    FROM medicines m
    LEFT JOIN medicine_assistance ma ON m.med_id = ma.med_id
    GROUP BY m.med_id
    ORDER BY last_dispensed DESC, m.med_name ASC
");
        while ($m = $meds->fetch_assoc()) {
            echo "<option value='{$m['med_id']}'>{$m['med_name']} (Stock: {$m['quantity']})</option>";
        }
        ?>
      </select>

      <input type="number" name="quantity[]" placeholder="Qty" min="1" required>
      <button type="button" class="remove-btn" onclick="removeMedicine(this)">✖</button>
    </div>
  </div>

  <button type="button" id="addMedicineBtn">+ Add Another Medicine</button>
</div>

<div class="modal-footer">
  <button type="button" class="btn outline" onclick="closeModal('recordModal')">Cancel</button>
  <button type="submit" class="btn primary">Add Record</button>
</div>

</form>
</div> <!-- end of modal-content -->
</div> <!-- end of recordModal -->

  <!-- Add Patient Modal -->
  <div id="patientModal" class="modal hidden">
    <div class="modal-content large">
      <div class="modal-header">
        <h2>Add New Patient</h2>
        <span class="close" onclick="closeModal('patientModal')">&times;</span>
      </div>
      <form action="pat.php" method="POST" class="modal-form">
        <div class="form-row">
          <label>Full Name *</label>
          <input type="text" name="fullname" required>
        </div>

        <div class="form-row">
  <label>Age</label>
  <input type="text" name="age" id="age" placeholder="e.g. 6 months or 3 years" readonly>
</div>

       <div class="form-row">
  <label>Date of Birth *</label>
  <input type="date" name="dob" id="dob" required>
</div>



        <div class="form-row">
          <label>Sex *</label>
          <select name="sex" required>
            <option value="">Select sex</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
          </select>
        </div>
        <div class="form-row">
          <label>Blood Type</label>
          <select name="blood_type">
            <option value="">Blood type</option>
            <option>A+</option><option>A-</option>
            <option>B+</option><option>B-</option>
            <option>O+</option><option>O-</option>
            <option>AB+</option><option>AB-</option>
             <option>Not yet tested</option>
          </select>
        </div>

        <div class="form-row">
          <label>Purok *</label>
          <select name="purok" required>
            <option value="">Select purok</option>
            <option>Purok 1</option><option>Purok 2</option><option>Purok 3</option>
            <option>Purok 4</option><option>Purok 5</option><option>Purok 6</option>
            <option>Purok 7</option><option>Purok 8</option>
          </select>
        </div>

        <div class="form-row">
          <label>Contact Number *</label>
          <input type="text" name="contact" required>
        </div>
        <div class="form-row">
          <label>Civil Status</label>
          <select name="civil_status">
            <option value="">Select status</option>
            <option>Single</option><option>Married</option>
            <option>Widowed</option><option>Separated</option>
          </select>
        </div>

        <div class="form-row">
          <label>Occupation</label>
          <input type="text" name="occupation">
        </div>
        <div class="form-row">
          <label>Emergency Contact(Name/Contact No.)</label>
          <input type="text" name="emergency">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn outline" onclick="closeModal('patientModal')">Cancel</button>
          <button type="submit" name="add_patient" class="btn primary">Add Patient</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ================== SCRIPT ================== -->
  <script>
    document.getElementById("addRecordBtn").onclick = () => {
      document.getElementById("recordModal").classList.remove("hidden");
    };
    document.getElementById("addPatientBtn").onclick = () => {
      document.getElementById("patientModal").classList.remove("hidden");
    };
    function closeModal(id) {
      document.getElementById(id).classList.add("hidden");
    }
  </script>

  <script>
  const searchInput = document.getElementById('patientSearch');
  const patientSelect = document.getElementById('patient_id');

  searchInput.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    for (let i = 0; i < patientSelect.options.length; i++) {
      const option = patientSelect.options[i];
      const text = option.text.toLowerCase();
      // Show only matching options
      option.style.display = text.includes(searchTerm) ? '' : 'none';
    }
  });
</script>



<script>
// === PATIENT TABLE SEARCH & FILTER ===
const searchInputMain = document.getElementById('searchInput');
const purokFilter = document.getElementById('purokFilter');
const sexFilter = document.getElementById('sexFilter');
const clearFilters = document.getElementById('clearFilters');
const patientTable = document.getElementById('patientTable');

function filterPatients() {
  const searchTerm = searchInputMain.value.toLowerCase();
  const purokValue = purokFilter.value;
  const sexValue = sexFilter.value;

  const rows = patientTable.querySelectorAll('tr');
  rows.forEach(row => {
    // ✅ Match correct columns
    const id = row.cells[0]?.innerText.toLowerCase() || '';
    const name = row.cells[1]?.innerText.toLowerCase() || '';
    const ageSex = row.cells[2]?.innerText.toLowerCase() || '';
    const purok = row.cells[3]?.innerText || '';

    // ✅ Apply logic
    const matchesSearch = name.includes(searchTerm) || id.includes(searchTerm);
    const matchesPurok = (purokValue === 'all' || purok === purokValue);
    const matchesSex = (sexValue === 'all' || ageSex.includes(sexValue.toLowerCase()));

    // ✅ Show or hide
    row.style.display = (matchesSearch && matchesPurok && matchesSex) ? '' : 'none';
  });
}

// Event listeners
searchInputMain.addEventListener('keyup', filterPatients);
purokFilter.addEventListener('change', filterPatients);
sexFilter.addEventListener('change', filterPatients);

clearFilters.addEventListener('click', () => {
  searchInputMain.value = '';
  purokFilter.value = 'all';
  sexFilter.value = 'all';
  filterPatients();
});
</script>


<script>
document.getElementById('addMedicineBtn').addEventListener('click', function() {
  const container = document.getElementById('medicine-container');
  const newRow = document.createElement('div');
  newRow.classList.add('medicine-row');
  newRow.innerHTML = `
    <select name="med_id[]" required>
      <option value="">Select medicine</option>
      <?php
      $meds = $conn->query("SELECT med_id, med_name, quantity FROM medicines ORDER BY med_name ASC");
      while ($m = $meds->fetch_assoc()) {
          echo "<option value='{$m['med_id']}'>{$m['med_name']} (Stock: {$m['quantity']})</option>";
      }
      ?>
    </select>
    <input type="number" name="quantity[]" placeholder="Qty" min="1" required>
    <button type="button" class="remove-btn" onclick="removeMedicine(this)">✖</button>
  `;
  container.appendChild(newRow);
});

function removeMedicine(btn) {
  btn.parentElement.remove();
}
</script>
<script>
document.getElementById('dob').addEventListener('change', function() {
  const dob = new Date(this.value);
  const today = new Date();

  // If invalid date
  if (isNaN(dob)) return;

  let years = today.getFullYear() - dob.getFullYear();
  let months = today.getMonth() - dob.getMonth();
  let days = today.getDate() - dob.getDate();

  // Adjust if current month/day is before the birth month/day
  if (days < 0) {
    months--;
  }
  if (months < 0) {
    years--;
    months += 12;
  }

  let ageText = '';

  // 👶 If under 1 year, show months
  if (years === 0) {
    if (months === 0) {
      ageText = 'Less than a month';
    } else {
      ageText = `${months} month${months > 1 ? 's' : ''}`;
    }
  } 
  // 🧒 If 1 year or older
  else {
    ageText = `${years} year${years > 1 ? 's' : ''}`;
    if (months > 0) {
      ageText += ` and ${months} month${months > 1 ? 's' : ''}`;
    }
  }

  document.getElementById('age').value = ageText;
});
</script>


</body>
</html>
