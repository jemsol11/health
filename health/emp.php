<?php
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "barangay_health_center";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch all employees from employee_accounts
$sql = "SELECT id, employee_id, fullname, position, email, role, status FROM employee_accounts ORDER BY id DESC";
$result = $conn->query($sql);

$sql_admins = "SELECT user_id, fullname, email, role, username FROM users WHERE role = 'admin'";
$result_admins = $conn->query($sql_admins);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Accounts</title>
  <link rel="stylesheet" href="styles/emp.css">
  <style>
    /* Modal background */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; 
      width: 100%; height: 100%; overflow: auto; background: rgba(0, 0, 0, 0.5);}
    .modal-content { background: #fff; margin: 5% auto; padding: 20px; border-radius: 10px; 
      width: 500px; max-width: 95%; box-shadow: 0px 4px 20px rgba(0,0,0,0.2);}
    .modal-header { display: flex; justify-content: space-between; align-items: center;
      border-bottom: 2px solid #3d1a1aff; padding-bottom: 10px; margin-bottom: 15px;}
    .modal-header h2 { font-size: 18px; margin: 0; }
    .close { cursor: pointer; font-size: 22px; font-weight: bold; border: none; background: none; }
    .modal-body label { display: block; margin-top: 12px; font-weight: 600; }
    .modal-body input, .modal-body select { width: 100%; padding: 8px; margin-top: 5px;
      border: 1px solid #ccc; border-radius: 6px;}
    .modal-footer { display: flex; justify-content: flex-end; margin-top: 20px; }
    .btn { padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; }
    .btn.green { background: #642921ff; color: #fff; }
    .btn.cancel { background: #f3f3f3; margin-right: 10px; }
    .badge.green { background: #1f9b53ff; color: #fff; padding: 10px 15px; border-radius: 5px; }
   
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <!-- Page Content -->
  <div class="header-section">
  <div>
    <h2>Employee Accounts</h2>
   
  </div>
  <div>
    <button class="btn green" id="openModalBtn">+ Create Employee</button>
    <button class="btn green" id="openAdminModalBtn">+ Create Admin</button>
  </div>
</div>

   
<!-- Admin Directory Table -->
<section class="table-container">
  <h3>Admin Accounts</h3>

  <div class="search-filter">
    <input type="text" id="adminSearchInput" placeholder="Search admins...">
  </div>

  <table id="adminTable">
    <thead>
      <tr>
        <th>Admin</th>
        <th>Email</th>
        <th>Username</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php if ($result_admins->num_rows > 0): ?>
        <?php while($admin = $result_admins->fetch_assoc()): ?>
          <tr>
            <td>
              <?php echo htmlspecialchars($admin['fullname']); ?><br>
             
            </td>

            <td><?php echo htmlspecialchars($admin['email']); ?></td>

          

            <td><?php echo htmlspecialchars($admin['username']); ?></td>

            <td>
              <div class="action-buttons">
                <button 
                  class="btn-edit"
                  data-id="<?php echo $admin['user_id']; ?>"
                  data-fullname="<?php echo htmlspecialchars($admin['fullname']); ?>"
                  data-email="<?php echo htmlspecialchars($admin['email']); ?>"
                  data-role="<?php echo htmlspecialchars($admin['role']); ?>"
                >✏️ Edit</button>

              <a href="delete_admin.php?id=<?php echo $admin['user_id']; ?>"
                   class="btn-delete"
                   onclick="return confirm('Are you sure you want to delete this admin? This action cannot be undone.');">🗑️ Delete</a>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No admin accounts found</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>


    <!-- Staff Directory Table -->
    <section class="table-container">
  <h3>Staff Accounts</h3>

  <div class="search-filter">
    <input type="text" id="searchInput" placeholder="Search employees...">
  </div>

  <table id="staffTable"> <!-- ✅ Added this ID -->
    <thead>
      <tr>
        <th>Employee</th>
        <th>Position</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <?php echo htmlspecialchars($row['fullname']); ?><br>
                
                </td>
                
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td>
                  <span class="badge <?php echo ($row['status'] == 'active') ? 'green' : 'red'; ?>">
                    <?php echo ucfirst($row['status']); ?>
                  </span>
                </td>
                
                <td>
         
        <div class="action-buttons">
  <button 
    class="btn-edit" 
    data-id="<?php echo $row['id']; ?>"
    data-fullname="<?php echo htmlspecialchars($row['fullname']); ?>"
    data-position="<?php echo htmlspecialchars($row['position']); ?>"
    data-email="<?php echo htmlspecialchars($row['email']); ?>"
    data-role="<?php echo htmlspecialchars($row['role']); ?>"
    data-status="<?php echo htmlspecialchars($row['status']); ?>"
  >✏️ Edit</button>

  <a href="delete_employee.php?id=<?php echo $row['id']; ?>" 
     class="btn-delete" 
     onclick="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">🗑️ Delete</a>
</div> 
                </td>
               
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
              <tr>
                <td colspan="6" style="text-align:center;">No employees found</td>
              </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>

  </main>

 
<!-- Create Admin Account Modal -->
<div id="adminModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Create Admin Account</h2>
      <button class="close" id="closeAdminModal">&times;</button>
    </div>

    <form action="save_admin.php" method="POST" onsubmit="return validateAdminPassword();">
      <div class="modal-body">
        <label>Full Name *</label>
        <input type="text" name="fullname" placeholder="Enter full name" required>

        <label>Email Address *</label>
        <input type="email" name="email" placeholder="admin@bagongsilang.gov.ph" required>

        <label>Username *</label>
        <input type="text" name="username" placeholder="Enter username" required>

     <div class="input-wrapper">
  <label for="admin_password">Password *</label>
  <div class="password-field">
    <input type="password" name="password" id="admin_password" placeholder="Enter password" required>
    <span class="toggle-eye" id="toggleAdminPassword">👁️</span>
  </div>
</div>

<div class="input-wrapper">
  <label for="admin_confirm_password">Confirm Password *</label>
  <div class="password-field">
    <input type="password" name="confirm_password" id="admin_confirm_password" placeholder="Re-enter password" required>
    <span class="toggle-eye" id="toggleAdminConfirmPassword">👁️</span>
  </div>
</div>
        <div id="password-requirements">
          <p><strong>Password must contain:</strong></p>
          <ul>
            <li>At least 8 characters</li>
            <li>One uppercase letter (A–Z)</li>
            <li>One lowercase letter (a–z)</li>
            <li>One number (0–9)</li>
            <li>One special character (!@#$%^&*)</li>
          </ul>
        </div>

        <input type="hidden" name="role" value="admin">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn cancel" id="cancelAdminBtn">Cancel</button>
        <button type="submit" class="btn green">Create Account</button>
      </div>
    </form>
  </div>
</div>

   <!-- Create Employee Modal -->
   <!-- Create Employee Modal -->
<div id="employeeModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Create Employee Account</h2>
      <button class="close" id="closeModal">&times;</button>
    </div>

    <form action="save_employee.php" method="POST">
      <div class="modal-body">
        <label>Full Name *</label>
        <input type="text" name="fullname" placeholder="Enter full name" required>

        <label>Position *</label>
        <input type="text" name="position" placeholder="e.g., Registered Nurse" required>

        <label>Email Address *</label>
        <input type="email" name="email" placeholder="employee@bagongsilang.gov.ph" required>

        <label>Role *</label>
        <select name="role" required>
        
          <option value="staff">Staff</option>
        </select>

        <!-- Auto-generated Employee ID - No input needed -->
        <label>Employee ID</label>
        <input type="text" name="employee_id" value="Auto-generated (e.g., EMP001)" readonly>
        <small>This will be automatically assigned upon creation.</small>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn cancel" id="cancelBtn">Cancel</button>
        <button type="submit" class="btn green">Create Account</button>
      </div>
    </form>
  </div>
</div>

   <!-- Edit Employee Modal -->
<div id="editEmployeeModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Employee Account</h2>
      <button class="close" id="closeEditModal">&times;</button>
    </div>
    <form action="update_employee.php" method="POST">
      <div class="modal-body">
        <input type="hidden" name="id" id="edit_id">

        <label>Full Name *</label>
        <input type="text" name="fullname" id="edit_fullname" required>

        <label>Position *</label>
        <input type="text" name="position" id="edit_position" required>

        <label>Email *</label>
        <input type="email" name="email" id="edit_email" required>

        <label>Role *</label>
        <select name="role" id="edit_role" required>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
        </select>

        <label>Status *</label>
        <select name="status" id="edit_status" required>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn cancel" id="cancelEditBtn">Cancel</button>
        <button type="submit" class="btn green">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Admin Modal -->
<div id="editAdminModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Admin Account</h2>
      <button class="close" id="closeEditAdminModal">&times;</button>
    </div>

    <form action="edit_admin.php" method="POST" onsubmit="return validateEditAdminPassword();">
      <div class="modal-body">
        <input type="hidden" name="user_id" id="edit_admin_id">

        <label>Full Name *</label>
        <input type="text" name="fullname" id="edit_admin_fullname" required>

        <label>Email Address *</label>
        <input type="email" name="email" id="edit_admin_email" required>

        <label>Username *</label>
        <input type="text" name="username" id="edit_admin_username" required>

        <label>New Password (Leave blank to keep current)</label>
        <input type="password" name="password" id="edit_admin_password" placeholder="Enter new password">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" id="edit_admin_confirm_password" placeholder="Re-enter new password">

        <input type="hidden" name="role" value="admin">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn cancel" id="cancelEditAdminBtn">Cancel</button>
        <button type="submit" class="btn green">Save Changes</button>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {

  // ===============================
  // STAFF SEARCH FILTER
  // ===============================
  const staffSearchInput = document.getElementById('searchInput');
  if (staffSearchInput) {
    const staffRows = document.querySelectorAll('#staffTable tbody tr');
    staffSearchInput.addEventListener('input', () => {
      const filter = staffSearchInput.value.toLowerCase();
      staffRows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
      });
    });
  }

  // ===============================
  // ADMIN SEARCH FILTER
  // ===============================
  const adminSearchInput = document.getElementById('adminSearchInput');
  if (adminSearchInput) {
    const adminRows = document.querySelectorAll('#adminTable tbody tr');
    adminSearchInput.addEventListener('input', () => {
      const filter = adminSearchInput.value.toLowerCase();
      adminRows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
      });
    });
  }

  // ===============================
  // EMPLOYEE ADD MODAL
  // ===============================
  const employeeModal = document.getElementById("employeeModal");
  const openEmployeeBtn = document.getElementById("openModalBtn");
  const closeEmployeeBtn = document.getElementById("closeModal");
  const cancelEmployeeBtn = document.getElementById("cancelBtn");

  openEmployeeBtn?.addEventListener('click', () => employeeModal.style.display = "block");
  closeEmployeeBtn?.addEventListener('click', () => employeeModal.style.display = "none");
  cancelEmployeeBtn?.addEventListener('click', () => employeeModal.style.display = "none");

  // ===============================
  // EMPLOYEE EDIT MODAL
  // ===============================
  const editEmployeeModal = document.getElementById('editEmployeeModal');
  const closeEditEmployeeModal = document.getElementById('closeEditModal');
  const cancelEditEmployeeBtn = document.getElementById('cancelEditBtn');

  document.querySelectorAll('#staffTable .btn-edit').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('edit_id').value = button.dataset.id;
      document.getElementById('edit_fullname').value = button.dataset.fullname;
      document.getElementById('edit_position').value = button.dataset.position;
      document.getElementById('edit_email').value = button.dataset.email;
      document.getElementById('edit_role').value = button.dataset.role;
      document.getElementById('edit_status').value = button.dataset.status;
      editEmployeeModal.style.display = 'block';
    });
  });

  closeEditEmployeeModal?.addEventListener('click', () => editEmployeeModal.style.display = "none");
  cancelEditEmployeeBtn?.addEventListener('click', () => editEmployeeModal.style.display = "none");

  // ===============================
  // ADMIN CREATE MODAL
  // ===============================
  const adminModal = document.getElementById("adminModal");
  const openAdminModalBtn = document.getElementById("openAdminModalBtn");
  const closeAdminModal = document.getElementById("closeAdminModal");
  const cancelAdminBtn = document.getElementById("cancelAdminBtn");

  openAdminModalBtn?.addEventListener("click", () => adminModal.style.display = "block");
  closeAdminModal?.addEventListener("click", () => adminModal.style.display = "none");
  cancelAdminBtn?.addEventListener("click", () => adminModal.style.display = "none");

  // ===============================
  // ADMIN EDIT MODAL
  // ===============================
  const editAdminModal = document.getElementById("editAdminModal");
  const closeEditAdminModal = document.getElementById("closeEditAdminModal");
  const cancelEditAdminBtn = document.getElementById("cancelEditAdminBtn");

  document.querySelectorAll("#adminTable .btn-edit").forEach(button => {
    button.addEventListener("click", () => {
      document.getElementById("edit_admin_id").value = button.dataset.id;
      document.getElementById("edit_admin_fullname").value = button.dataset.fullname;
      document.getElementById("edit_admin_email").value = button.dataset.email;
      document.getElementById("edit_admin_username").value = button.dataset.username || "";
      editAdminModal.style.display = "block";
    });
  });

  closeEditAdminModal?.addEventListener("click", () => editAdminModal.style.display = "none");
  cancelEditAdminBtn?.addEventListener("click", () => editAdminModal.style.display = "none");

  // ===============================
  // SHARED CLICK-OUTSIDE TO CLOSE MODALS
  // ===============================
  window.addEventListener("click", (event) => {
    const modals = [employeeModal, editEmployeeModal, adminModal, editAdminModal];
    modals.forEach(m => {
      if (event.target === m) m.style.display = "none";
    });
  });

    // ===============================
  const togglePassword = document.getElementById('toggleAdminPassword');
  const passwordInput = document.getElementById('admin_password');

  const toggleConfirm = document.getElementById('toggleAdminConfirmPassword');
  const confirmInput = document.getElementById('admin_confirm_password');

  function setupToggle(eyeIcon, inputField) {
    eyeIcon.addEventListener('click', () => {
      const isPassword = inputField.type === 'password';
      inputField.type = isPassword ? 'text' : 'password';
      eyeIcon.textContent = isPassword ? '🙈' : '👁️';
    });
  }

  if (togglePassword && passwordInput) setupToggle(togglePassword, passwordInput);
  if (toggleConfirm && confirmInput) setupToggle(toggleConfirm, confirmInput);
});

// ===============================
// PASSWORD VALIDATION FUNCTION
// ===============================
function validateAdminPassword() {
  const password = document.getElementById("admin_password").value;
  const confirm = document.getElementById("admin_confirm_password").value;

  const minLength = /.{8,}/;
  const upper = /[A-Z]/;
  const lower = /[a-z]/;
  const number = /[0-9]/;
  const special = /[!@#$%^&*]/;

  if (!minLength.test(password) || !upper.test(password) || !lower.test(password) || !number.test(password) || !special.test(password)) {
    alert("❌ Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
    return false;
  }

  if (password !== confirm) {
    alert("❌ Passwords do not match. Please recheck.");
    return false;
  }

  return true;
}
</script>
