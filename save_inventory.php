<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $med_name    = trim($_POST['medicine_name']);
    $category    = trim($_POST['category']);
    $quantity    = intval($_POST['quantity']);
    $unit        = trim($_POST['unit']);
    $batch       = trim($_POST['batch_number']);
    $expiry      = $_POST['expiration_date'] ?: NULL;
    // Assuming 'added_by_user' is passed in a hidden field from the form
    $added_by    = $_POST['added_by_user'] ?? ($_SESSION['username'] ?? 'System');
    $notes       = trim($_POST['notes']) ?: NULL;
    $description = NULL;
    $user_id     = $_SESSION['user_id'] ?? 0; // Get user_id for audit trail

    if ($med_name === '' || $quantity < 0) {
        die("Invalid input.");
    }

    // --- TRANSACTION START ---
    $conn->begin_transaction();

    try {
        // 1. INSERT the new medicine into the main table
        $stmt = $conn->prepare("INSERT INTO medicines
            (med_name, description, category, unit, quantity, batch_number, expiry_date, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss",
            $med_name, $description, $category, $unit, $quantity, $batch, $expiry, $notes
        );
        $stmt->execute();
        $new_med_id = $conn->insert_id;
        $stmt->close();

        // 2. LOG the initial stock addition into inventory_history
        $log_stmt = $conn->prepare("INSERT INTO inventory_history 
                                     (med_id, quantity_added, added_by)
                                     VALUES (?, ?, ?)");
        $log_stmt->bind_param("iis", $new_med_id, $quantity, $added_by);
        $log_stmt->execute();
        $log_stmt->close();
        
        // 3. Audit trail logging
        $action    = "Add Inventory";
        $details   = "Added new medicine: $med_name ($quantity $unit)";
        $timestamp = date('Y-m-d H:i:s');
        $status    = "Success";

        $audit = $conn->prepare("INSERT INTO audit_trail (user_id, action, details, timestamp, status)
                                 VALUES (?, ?, ?, ?, ?)");
        $audit->bind_param("issss", $user_id, $action, $details, $timestamp, $status);
        $audit->execute();
        $audit->close();
        
        // --- TRANSACTION COMMIT ---
        $conn->commit();

        echo "<script>
            alert('Inventory added successfully!');
            window.location.href = 'inv.php';
        </script>";
        exit;

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback(); // Revert changes if anything failed
        echo "<script>
            alert('Error saving inventory: " . addslashes($exception->getMessage()) . "');
            window.location.href = 'inv.php';
        </script>";
        exit;
    }
}
?>