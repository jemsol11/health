<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $med_id = intval($_POST['med_id']); 
    $med_name = trim($_POST['medicine_name']);
    $category = trim($_POST['category']);
    $unit = trim($_POST['unit']);
    $quantity_add = intval($_POST['quantity_add']);
    $batch_number = trim($_POST['batch_number']);
    $expiry_date = $_POST['expiry_date'];
    // Assuming 'added_by_user' is passed in a hidden field from the form
    $added_by = $_POST['added_by_user'] ?? ($_SESSION['username'] ?? 'System');
    $user_id = $_SESSION['user_id'] ?? 0;
    
    // Check if med_id is valid
    if ($med_id <= 0) {
        die("Invalid medicine ID.");
    }

    // --- TRANSACTION START ---
    $conn->begin_transaction();

    try {
        // 1. Get current quantity safely by med_id
        $stmt = $conn->prepare("SELECT quantity FROM medicines WHERE med_id = ?");
        $stmt->bind_param("i", $med_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Medicine not found.");
        }
        $row = $result->fetch_assoc();
        $current = (int)$row['quantity'];
        $stmt->close();

        // 2. Compute new quantity
        $new_quantity = $current + $quantity_add;

        // 3. Update record in the medicines table
        $stmt = $conn->prepare("
            UPDATE medicines 
            SET 
                med_name = ?, 
                category = ?, 
                unit = ?, 
                quantity = ?, 
                batch_number = ?, 
                expiry_date = ?
            WHERE med_id = ?
        ");
        $stmt->bind_param("sssissi", 
            $med_name, 
            $category, 
            $unit, 
            $new_quantity, 
            $batch_number, 
            $expiry_date, 
            $med_id
        );
        $stmt->execute();
        $stmt->close();

        // 4. LOG stock addition into inventory_history (Only if quantity was added)
        if ($quantity_add > 0) {
            $log_stmt = $conn->prepare("INSERT INTO inventory_history 
                                        (med_id, quantity_added, added_by)
                                        VALUES (?, ?, ?)");
            $log_stmt->bind_param("iis", $med_id, $quantity_add, $added_by);
            $log_stmt->execute();
            $log_stmt->close();
        }

        // 5. Audit trail logging
        $action = "Edit Inventory";
        $details = "Edited medicine: $med_name, added $quantity_add stock";
        $timestamp = date('Y-m-d H:i:s');
        $status = "Success";

        $audit = $conn->prepare("
            INSERT INTO audit_trail (user_id, action, details, timestamp, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $audit->bind_param("issss", $user_id, $action, $details, $timestamp, $status);
        $audit->execute();
        $audit->close();
        
        // --- TRANSACTION COMMIT ---
        $conn->commit();

        // Redirect with success message
        header("Location: inv.php?update=success");
        exit();

    } catch (Exception $e) {
        $conn->rollback(); // Revert changes if anything failed
        die("Error updating record: " . $e->getMessage());
    }
}
?>