<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $employee_id = $_GET['id'];

    // Mark the ID as viewed
    $conn->query("UPDATE employee_accounts SET is_id_viewed = 1 WHERE employee_id = '$employee_id'");

    // Fetch employee info
    $result = $conn->query("SELECT fullname, employee_id, email FROM employee_accounts WHERE employee_id = '$employee_id'");
    $employee = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Created</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            margin-top: 80px; 
            background-color: #f0e8deff;
        }
        .card { 
            background: #f0e3dcff; 
            padding: 30px; 
            border-radius: 12px; 
            border: 3px solid #4b2323ff;
            width: 400px; 
            margin: auto; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
        }
        h2 { 
            color: #642921; 
            margin-bottom: 20px;
        }
        .emp-id { 
            font-size: 2rem; 
            font-weight: bold; 
            color: #0e6613ff; 
        }
        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background-color: #642921;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .back-btn:hover {
            background-color: #814d3a;
            transform: translateY(-2px);
        }
        small {
            display: block;
            margin-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="card">
    <h2>Employee Account Created</h2>
    <p>Employee Name: <strong><?php echo htmlspecialchars($employee['fullname'] ?? 'N/A'); ?></strong></p>
    <p>Employee Email: <?php echo htmlspecialchars($employee['email'] ?? 'N/A'); ?></p>
    <p>Your Employee ID:</p>
    <p class="emp-id"><?php echo htmlspecialchars($employee['employee_id'] ?? 'N/A'); ?></p>
    <small>Please take note of your Employee ID — it will be needed to log in.</small>

    <!-- ✅ Back Button -->
    <a href="emp.php" class="back-btn">← Back to Employee List</a>
</div>
</body>
</html>
