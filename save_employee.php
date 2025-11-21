<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "barangay_health_center";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<script>alert('Connection failed: " . addslashes($conn->connect_error) . "'); window.history.back();</script>");
}
$conn->set_charset("utf8");

// Ensure the request is POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
    exit();
}

// Get form values
$fullname = trim($_POST['fullname'] ?? '');
$position = trim($_POST['position'] ?? '');
$email    = trim($_POST['email'] ?? '');
$role     = trim(strtolower($_POST['role'] ?? ''));

// Validate input
if (empty($fullname) || empty($position) || empty($email) || !in_array($role, ['staff', 'admin'])) {
    echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit();
}

// Check for duplicate email
$check_sql = "SELECT id FROM employee_accounts WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows > 0) {
    echo "<script>alert('Email already exists.'); window.history.back();</script>";
    $check_stmt->close();
    exit();
}
$check_stmt->close();

/* --- Secure Unique Employee ID Generator --- */
function generateSecureEmployeeId($conn) {
    $prefix = "EMP";
    $unique = false;
    $employee_id = "";

    while (!$unique) {
        $year = date("Y");
        $randomNum = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $randomLetters = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 2);
        $employee_id = "$prefix$year$randomNum$randomLetters";

        $check = $conn->prepare("SELECT id FROM employee_accounts WHERE employee_id = ?");
        $check->bind_param("s", $employee_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $unique = true;
        }

        $check->close();
    }

    return $employee_id;
}

$employee_id = generateSecureEmployeeId($conn);
$status = "active";

// Insert into employee_accounts
$sql = "INSERT INTO employee_accounts (fullname, position, email, role, status, employee_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $fullname, $position, $email, $role, $status, $employee_id);

if (!$stmt->execute()) {
    echo "<script>alert('Error creating employee: " . addslashes($conn->error) . "'); window.history.back();</script>";
    $stmt->close();
    exit();
}
$stmt->close();

// ✅ Log the creation before redirecting
if (isset($_SESSION['user_id'])) {
    $action = "Create Employee";
    $details = "Added new employee (Name: $fullname)";
    $status_log = "Success";

    // ✅ Corrected SQL and bind parameters
    $log_sql = "INSERT INTO audit_trail (user_id, action, details, status, timestamp)
                VALUES (?, ?, ?, ?, NOW())";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("isss", $_SESSION['user_id'], $action, $details, $status_log);
    $log_stmt->execute();
    $log_stmt->close();
}

// ✅ Show popup alert and redirect
echo "<script>
    alert('✅ Employee successfully created! Employee ID: " . addslashes($employee_id) . "');
    window.location.href = 'employee_created.php?id=" . urlencode($employee_id) . "';
</script>";
exit();
?>
