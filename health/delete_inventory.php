<?php
include 'db_connect.php';
session_start();

if (isset($_GET['id'])) {
    $med_id = intval($_GET['id']);

    // Get user info for audit trail
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Unknown';

    // Start transaction to ensure both deletes succeed or both fail
    $conn->begin_transaction();
    $success = false;

    try {
        // Get medicine details before starting delete process
        $get_med = $conn->prepare("SELECT med_name FROM medicines WHERE med_id = ?");
        $get_med->bind_param("i", $med_id);
        $get_med->execute();
        $result = $get_med->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $medicine_name = $row['med_name'];

            // 1. DELETE related records from the child table (inventory_history)
            $delete_history = $conn->prepare("DELETE FROM inventory_history WHERE med_id = ?");
            $delete_history->bind_param("i", $med_id);
            $delete_history->execute();
            $delete_history->close();

            // 2. DELETE the medicine from the parent table (medicines)
            $delete_medicine = $conn->prepare("DELETE FROM medicines WHERE med_id = ?");
            $delete_medicine->bind_param("i", $med_id);
            $delete_medicine->execute();
            $delete_medicine->close();

            $conn->commit(); // Commit transaction if both deletes succeed
            $success = true;

            // Log successful deletion
            $action = "Deleted inventory item";
            $details = "User '$username' deleted medicine: $medicine_name (ID: $med_id)";
            $status = "Success";

            $audit = $conn->prepare("
                INSERT INTO audit_trail (user_id, action, details, status) 
                VALUES (?, ?, ?, ?)
            ");
            $audit->bind_param("isss", $user_id, $action, $details, $status);
            $audit->execute();
            $audit->close();

        } else {
            echo "⚠️ Medicine not found.";
        }
    } catch (mysqli_sql_exception $e) {
        $conn->rollback(); // Rollback if any query failed
        $success = false;
        
        // Log failed deletion
        $action = "Delete inventory item";
        $details = "User '$username' failed to delete item (ID: $med_id) due to database error.";
        $status = "Failed";

        $audit = $conn->prepare("
            INSERT INTO audit_trail (user_id, action, details, status) 
            VALUES (?, ?, ?, ?)
        ");
        $audit->bind_param("isss", $user_id, $action, $details, $status);
        $audit->execute();
        
        echo "❌ Fatal error during deletion: " . $e->getMessage();
    }

    if ($success) {
        header("Location: inv.php?message=Item+deleted+successfully");
        exit;
    }
} else {
    echo "⚠️ Invalid request.";
}

$conn->close();
?>