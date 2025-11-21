<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input
    $patient_id  = intval($_POST['patient_id']);
    $doctor      = trim($_POST['doctor']);
    $date        = $_POST['date'];
    $complaint   = $_POST['complaint'];
    $diagnosis   = $_POST['diagnosis'];
    $treatment   = $_POST['treatment'];
    $bp          = $_POST['bp'];
    $temperature = $_POST['temperature'];
    $weight      = $_POST['weight'];
    $height      = $_POST['height'];
    $notes       = $_POST['notes'];

    // Multiple medicine arrays
    $med_ids     = $_POST['med_id'] ?? [];
    $quantities  = $_POST['quantity'] ?? [];

    // Build a readable list for display
    $medication_list = [];
    foreach ($med_ids as $i => $id) {
        $qty = intval($quantities[$i]);
        if ($id && $qty > 0) {
            $result = $conn->query("SELECT med_name FROM medicines WHERE med_id = $id");
            $row = $result->fetch_assoc();
            $med_name = $row['med_name'] ?? 'Unknown Medicine';
            $medication_list[] = "$med_name (Qty: $qty)";
        }
    }
    $medication_str = implode(", ", $medication_list);

    // Get user/employee for audit
    $user_id_for_log = $_SESSION['user_id'] ?? NULL;
    $employee_id_for_log = ($_SESSION['role'] ?? '') === 'staff' ? $_SESSION['employee_id'] : NULL;

    try {
        // Start transaction
        $conn->begin_transaction();

        // 1️⃣ Insert main medical record
        $sql = "INSERT INTO medical_records 
                (patient_id, doctor, date, complaint, diagnosis, treatment, medication, bp, temperature, weight, height, notes)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssssssss", 
            $patient_id, $doctor, $date, $complaint, $diagnosis, $treatment, 
            $medication_str, $bp, $temperature, $weight, $height, $notes
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert medical record: " . $stmt->error);
        }

        $record_id = $stmt->insert_id;
        $stmt->close();

        // 2️⃣ Loop through each medicine
        foreach ($med_ids as $i => $id) {
            $qty = intval($quantities[$i]);
            if (!$id || $qty <= 0) continue;

            // Check stock
            $check_stock = $conn->prepare("SELECT med_name, quantity FROM medicines WHERE med_id = ? FOR UPDATE");
            $check_stock->bind_param("i", $id);
            $check_stock->execute();
            $res = $check_stock->get_result();
            if (!$res || $res->num_rows === 0) throw new Exception("Medicine not found (ID: $id)");
            $row = $res->fetch_assoc();
            $check_stock->close();

            if ($row['quantity'] < $qty) {
                throw new Exception("Not enough stock for {$row['med_name']} (Available: {$row['quantity']})");
            }

            // Deduct stock
            $update_stock = $conn->prepare("UPDATE medicines SET quantity = quantity - ? WHERE med_id = ?");
            $update_stock->bind_param("ii", $qty, $id);
            if (!$update_stock->execute()) {
                throw new Exception("Failed to update stock for {$row['med_name']}");
            }
            $update_stock->close();

            // Insert into medicine_assistance linked to this record
            $insert_assistance = $conn->prepare("
                INSERT INTO medicine_assistance (record_id, patient_id, med_id, quantity_given, date_given, given_by)
                VALUES (?, ?, ?, ?, NOW(), ?)
            ");
            $insert_assistance->bind_param("iiiis", $record_id, $patient_id, $id, $qty, $doctor);
            if (!$insert_assistance->execute()) {
                throw new Exception("Failed to insert into medicine_assistance for {$row['med_name']}");
            }
            $insert_assistance->close();

            // Audit each dispense
            $action = "Dispense Medicine";
            $details = "Dispensed {$qty}x {$row['med_name']} to Patient ID: {$patient_id} (Record ID: {$record_id})";
            $status = "Success";

            $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                                     VALUES (?, ?, ?, ?, ?)");
            $audit->bind_param("issss", $user_id_for_log, $employee_id_for_log, $action, $details, $status);
            $audit->execute();
            $audit->close();
        }

        // 3️⃣ Audit record creation
        $action = "Add Medical Record";
        $details = "Added medical record for Patient ID: $patient_id (Doctor: $doctor, Diagnosis: $diagnosis)";
        $status = "Success";
        $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                                 VALUES (?, ?, ?, ?, ?)");
        $audit->bind_param("issss", $user_id_for_log, $employee_id_for_log, $action, $details, $status);
        $audit->execute();
        $audit->close();

        // ✅ Commit everything
        $conn->commit();

        header("Location: pat.php?success=record_added");
        exit;

    } catch (Exception $e) {
        // ❌ Rollback all changes if anything fails
        $conn->rollback();

        // Audit failure
        $action = "Add Medical Record";
        $details = "Failed to add record for Patient ID: $patient_id ({$e->getMessage()})";
        $status = "Failed";
        $audit = $conn->prepare("INSERT INTO audit_trail (user_id, employee_id, action, details, status)
                                 VALUES (?, ?, ?, ?, ?)");
        $audit->bind_param("issss", $user_id_for_log, $employee_id_for_log, $action, $details, $status);
        $audit->execute();
        $audit->close();

        echo "<script>alert('localhost says: Error: {$e->getMessage()}'); window.history.back();</script>";
    }

    $conn->close();
}
?>
