<?php
// give_medicine.php
session_start();
include 'db_connect.php';
include 'run_forecast.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = intval($_POST['patient_id']);
    $med_id     = intval($_POST['med_id']); // selects a specific batch/item
    $qty        = intval($_POST['quantity_given']);
    $given_by   = trim($_POST['given_by'] ?? 'unknown');

    if ($qty <= 0) {
        die("Invalid quantity.");
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Lock the inventory row to avoid race conditions
        $sel = $conn->prepare("SELECT quantity, med_name, batch_number FROM medicines WHERE med_id = ? FOR UPDATE");
        $sel->bind_param("i", $med_id);
        $sel->execute();
        $res = $sel->get_result();

        if (!$row = $res->fetch_assoc()) {
            $conn->rollback();
            die("Medicine not found.");
        }

        if ($row['quantity'] < $qty) {
            $conn->rollback();
            die("Not enough stock. Available: " . intval($row['quantity']));
        }

        // Insert into medicine_assistance
        $ins = $conn->prepare("
            INSERT INTO medicine_assistance (patient_id, med_id, quantity_given, date_given, given_by)
            VALUES (?, ?, ?, NOW(), ?)
        ");
        $ins->bind_param("iiis", $patient_id, $med_id, $qty, $given_by);
        $ins->execute();

        // Deduct stock
        $upd = $conn->prepare("UPDATE medicines SET quantity = quantity - ? WHERE med_id = ?");
        $upd->bind_param("ii", $qty, $med_id);
        $upd->execute();

        // ✅ Audit Trail (fixed)
        $user_id_for_log = NULL;
        $employee_id_for_log = NULL;

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff') {
            $employee_id_for_log = $_SESSION['employee_id'];
        } else if (isset($_SESSION['user_id'])) {
            $user_id_for_log = $_SESSION['user_id'];
        }

        if ($user_id_for_log !== NULL || $employee_id_for_log !== NULL) {
            $action = "Dispense Medicine";
            $details = "Dispensed {$qty}x {$row['med_name']} (Batch: {$row['batch_number']}) to Patient ID: {$patient_id}";
            $status = "Success";

            $audit_sql = "INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                          VALUES (?, ?, ?, ?, ?)";
            $audit_stmt = $conn->prepare($audit_sql);
            $audit_stmt->bind_param("issss", $user_id_for_log, $employee_id_for_log, $action, $details, $status);
            $audit_stmt->execute();
            $audit_stmt->close();
        }

        // Commit transaction
        $conn->commit();

        header("Location: med.php?success=medicine_given");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
