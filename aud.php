<?php
session_start();

// Database connection
$host = "localhost";
$user = "root"; // default for XAMPP
$pass = "";     // default no password
$db   = "barangay_health_center";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter parameters
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filter_user = isset($_GET['filter_user']) ? $_GET['filter_user'] : '';
$filter_action = isset($_GET['filter_action']) ? $_GET['filter_action'] : '';

// Build WHERE clause
$where_conditions = [];
if (!empty($date_from)) {
    $where_conditions[] = "DATE(a.timestamp) >= '" . $conn->real_escape_string($date_from) . "'";
}
if (!empty($date_to)) {
    $where_conditions[] = "DATE(a.timestamp) <= '" . $conn->real_escape_string($date_to) . "'";
}
if (!empty($filter_user)) {
    if ($filter_user === 'Staff') {
        $where_conditions[] = "a.employee_id IS NOT NULL";
    } else {
        $where_conditions[] = "(u.username = '" . $conn->real_escape_string($filter_user) . "' OR e.fullname = '" . $conn->real_escape_string($filter_user) . "')";
    }
}
if (!empty($filter_action)) {
    $where_conditions[] = "a.action = '" . $conn->real_escape_string($filter_action) . "'";
}

$where_sql = '';
if (count($where_conditions) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $where_conditions);
}

// --- FETCH LOGS ---
// COALESCE ensures we show admin username or staff fullname/email
$sql = "
SELECT 
    a.id,
    a.timestamp,
    a.action,
    a.details,
    a.status,
    COALESCE(u.username, e.fullname, e.email, 'Unknown User') AS username,
    CASE 
        WHEN u.user_id IS NOT NULL THEN 'admin'
        WHEN e.employee_id IS NOT NULL THEN 'staff'
        ELSE 'unknown'
    END AS role
FROM audit_trail a
LEFT JOIN users u ON a.user_id = u.user_id
LEFT JOIN employee_accounts e ON a.employee_id = e.employee_id
$where_sql
ORDER BY a.timestamp DESC
";

$result = $conn->query($sql);

// --- SUMMARY COUNTS ---
// Include both users and employees for active counts
$count_base = "
FROM audit_trail a
LEFT JOIN users u ON a.user_id = u.user_id
LEFT JOIN employee_accounts e ON a.employee_id = e.employee_id
$where_sql
";

$and_clause = !empty($where_sql) ? " AND " : " WHERE ";

$total_events = $conn->query("SELECT COUNT(*) AS total $count_base")->fetch_assoc()['total'];
$successful   = $conn->query("SELECT COUNT(*) AS success $count_base $and_clause a.status='Success'")->fetch_assoc()['success'];
$failed       = $conn->query("SELECT COUNT(*) AS fail $count_base $and_clause a.status='Failed'")->fetch_assoc()['fail'];
$active_users = $conn->query("SELECT COUNT(DISTINCT COALESCE(a.user_id, a.employee_id)) AS active $count_base $and_clause a.status='Success'")->fetch_assoc()['active'];

// --- DROPDOWN DATA ---
$users_result = $conn->query("SELECT DISTINCT username FROM users WHERE username IS NOT NULL ORDER BY username");
$staff_result = $conn->query("SELECT DISTINCT fullname FROM employee_accounts WHERE fullname IS NOT NULL ORDER BY fullname");
$actions_result = $conn->query("SELECT DISTINCT action FROM audit_trail WHERE action IS NOT NULL ORDER BY action");

// Merge admin and staff users into one array
$user_list = [];
if ($users_result) {
    while ($row = $users_result->fetch_assoc()) {
        $user_list[] = $row['username'];
    }
}
if ($staff_result) {
    while ($row = $staff_result->fetch_assoc()) {
        if (!in_array($row['fullname'], $user_list)) {
            $user_list[] = $row['fullname'];
        }
    }
}
sort($user_list);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Audit Trail - Barangay Silang Health Management System</title>
  <link rel="stylesheet" href="styles/aud.css">
  <style>
    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .filter-row {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
      align-items: flex-end;
    }
    .filter-group {
      flex: 1;
      min-width: 200px;
    }
    .filter-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: #333;
    }
    .filter-group input,
    .filter-group select {
      width: 100%;
      padding: 8px 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }
    .filter-buttons {
      display: flex;
      gap: 10px;
      align-items: flex-end;
    }
    .filter-btn, .clear-btn {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
    }
    .filter-btn {
      background: #4CAF50;
      color: white;
    }
    .filter-btn:hover {
      background: #45a049;
    }
    .clear-btn {
      background: #f44336;
      color: white;
    }
    .clear-btn:hover {
      background: #da190b;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">Bagong Silang Health Center 🩺</div>
    <nav>
      <ul>
        <li><a href="admd.php">Dashboard</a></li>
        <li><a href="emp.php">Accounts</a></li>
        <li><a href="pat.php">Patients</a></li>
        <li><a href="inv.php">Inventory</a></li>
        <li><a href="med.php">Medicine Assistance</a></li>
        <li><a href="rep.php">Reports</a></li>
        <li><a href="aud.php" class="active">Audit Trail</a></li>
      </ul>
    </nav>
    <div class="user-info">
      <div class="avatar"><?php echo strtoupper(substr($_SESSION['role'], 0, 1)); ?></div>
      <span><?php echo ucfirst($_SESSION['role']); ?> - <?php echo $_SESSION['username']; ?></span>
      <form method="post" action="logout.php">
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>
  </header>

  <!-- Main Content -->
  <main class="content">
    <h2>Audit Trail</h2>
  
    <!-- Summary Cards -->
    <div class="summary-cards">
      <div class="card"><h3>Total Events</h3><p class="value"><?php echo $total_events; ?></p></div>
      <div class="card"><h3>Successful</h3><p class="value"><?php echo $successful; ?></p></div>
      <div class="card"><h3>Failed</h3><p class="value"><?php echo $failed; ?></p></div>
      <div class="card"><h3>Active Users</h3><p class="value"><?php echo $active_users; ?></p></div>
    </div>

    <!-- Logs -->
    <section class="logs">
      <div class="logs-header">
        <h3>Activity Logs</h3>
        <button class="export-btn" onclick="exportLogs()">📤Export Logs</button>
      </div>

      <!-- Filter Section -->
      <div class="filter-section">
        <form method="GET" action="aud.php" id="filterForm">
          <div class="filter-row">
            <div class="filter-group">
              <label>Date From:</label>
              <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="filter-group">
              <label>Date To:</label>
              <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            <div class="filter-group">
              <label>User:</label>
              <select name="filter_user">
                <option value="">All Users</option>
                <?php foreach ($user_list as $user): ?>
                  <option value="<?php echo htmlspecialchars($user); ?>" <?php echo ($filter_user == $user) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="filter-group">
              <label>Action:</label>
              <select name="filter_action">
                <option value="">All Actions</option>
                <?php 
                if ($actions_result && $actions_result->num_rows > 0) {
                  while ($action_row = $actions_result->fetch_assoc()): 
                ?>
                  <option value="<?php echo htmlspecialchars($action_row['action']); ?>" 
                    <?php echo ($filter_action == $action_row['action']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($action_row['action']); ?>
                  </option>
                <?php endwhile; } ?>
              </select>
            </div>
            <div class="filter-buttons">
              <button type="submit" class="filter-btn">🔍 Filter</button>
              <button type="button" class="clear-btn" onclick="clearFilters()">✖ Clear</button>
            </div>
          </div>
        </form>
      </div>

      <input type="text" placeholder="Search by user, action, or details..." class="search-box" onkeyup="searchLogs(this.value)">

      <table class="logs-table" id="logsTable">
        <thead>
          <tr>
            <th>Timestamp</th>
            <th>User</th>
            <th>Role</th>
            <th>Action</th>
            <th>Details</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['timestamp']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td><?php echo htmlspecialchars($row['action']); ?></td>
                <td><?php echo htmlspecialchars($row['details']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6">No activity logs found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <script>
    function searchLogs(query) {
      let filter = query.toLowerCase();
      let rows = document.querySelectorAll("#logsTable tbody tr");
      rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    }

    function clearFilters() {
      window.location.href = 'aud.php';
    }

    function exportLogs() {
      const params = new URLSearchParams(window.location.search);
      window.location.href = 'export_logs.php?' + params.toString();
    }
  </script>
</body>
</html>
<?php $conn->close(); ?>
