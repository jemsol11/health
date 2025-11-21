<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "barangay_health_center";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form inputs
$role        = $_POST['role'] ?? '';
$username    = trim($_POST['username'] ?? '');
$password    = trim($_POST['password'] ?? '');
$employee_id = trim($_POST['id_number'] ?? '');

// --- ADMIN LOGIN ---
if ($role === "admin") {
    $sql = "SELECT * FROM users WHERE role='admin' AND (username=? OR email=?) LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'admin';

            // Log success (admin)
            $action = "Login";
            $details = "Admin logged in successfully.";
            $status = "Success";
            
            $log_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                        VALUES (?, NULL, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("isss", $user['user_id'], $action, $details, $status);
            $log_stmt->execute();
            $log_stmt->close();

            header("Location: admd.php");
            exit();
        } else {
            // Log failed password attempt
            $action = "Login";
            $details = "Failed login: Incorrect password for username/email: $username";
            $status = "Failed";
            
            $log_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                        VALUES (NULL, NULL, ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("sss", $action, $details, $status);
            $log_stmt->execute();
            $log_stmt->close();
            
            echo "<script>alert('Incorrect password.'); window.location.href='log.php';</script>";
            exit();
        }
    } else {
        // Log failed login attempt
        $action = "Login";
        $details = "Failed login: Username/Email not found: $username";
        $status = "Failed";
        
        $log_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                    VALUES (NULL, NULL, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("sss", $action, $details, $status);
        $log_stmt->execute();
        $log_stmt->close();
        
        echo "<script>alert('Admin account not found.'); window.location.href='log.php';</script>";
        exit();
    }

// --- STAFF LOGIN ---
} elseif ($role === "staff") {
    
    // Authenticate against employee_accounts (using email and employee_id)
    $sql = "SELECT * FROM employee_accounts WHERE email=? AND employee_id=? AND status='active' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();

        // Set session variables for staff
        $_SESSION['employee_id'] = $staff['employee_id']; // ← THIS IS THE KEY
        $_SESSION['username'] = $staff['fullname'];
        $_SESSION['role'] = 'staff';
        
        // Log the login action
        $action = "Login";
        $details = "Staff logged in successfully.";
        $status = "Success";

        $log_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                    VALUES (NULL, ?, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("ssss", $staff['employee_id'], $action, $details, $status);
        $log_stmt->execute();
        $log_stmt->close();

        header("Location: staffd.php");
        exit();

    } else {
        // Log failed staff login
        $action = "Login";
        $details = "Failed login attempt for username/email: $username";
        $status = "Failed";
        
        $log_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                    VALUES (NULL, NULL, ?, ?, ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("sss", $action, $details, $status);
        $log_stmt->execute();
        $log_stmt->close();
        
        echo "<script>alert('Invalid Staff Email or ID Number.'); window.location.href='log.php';</script>";
        exit();
    }

} else {
    echo "<script>alert('Invalid role selected.'); window.location.href='log.php';</script>";
    exit();
}

$conn->close();
?>