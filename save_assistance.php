<?php
ob_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $med_id = $_POST['med_id'];
    $quantity_given = $_POST['quantity_given'];
    $date_given = $_POST['date_given'];
    $doctor = $_POST['doctor'];
    $instructions = $_POST['instructions'];

    $stmt = $conn->prepare("INSERT INTO medicine_assistance 
        (patient_id, med_id, quantity_given, date_given, given_by, instructions) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $patient_id, $med_id, $quantity_given, $date_given, $doctor, $instructions);

    if ($stmt->execute()) {
        $update = $conn->prepare("UPDATE medicines SET quantity = quantity - ? WHERE med_id = ?");
        $update->bind_param("ii", $quantity_given, $med_id);
        $update->execute();

        echo "<script>
            alert('Medicine dispensed successfully!');
            window.location.href = 'med.php';
        </script>";
    } else {
        echo "<script>
            alert('Error dispensing medicine.');
            window.location.href = 'med.php';
        </script>";
    }
}
ob_end_flush();
?>
