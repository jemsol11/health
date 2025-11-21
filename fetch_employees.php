<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "barangay_health_center";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$search = $_GET['search'] ?? '';
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

$sql = "SELECT * FROM employee_accounts WHERE 1=1";

if (!empty($search)) {
  $sql .= " AND (fullname LIKE '%$search%' OR email LIKE '%$search%' OR employee_id LIKE '%$search%')";
}
if (!empty($role)) {
  $sql .= " AND role = '$role'";
}
if (!empty($status)) {
  $sql .= " AND status = '$status'";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
  
  }
} else {
  echo "<tr><td colspan='6' style='text-align:center;'>No employees found</td></tr>";
}
$conn->close();
?>
