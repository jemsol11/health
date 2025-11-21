<?php
include 'db_connect.php';
header('Content-Type: application/json');

$sql = "SELECT m.med_name, SUM(ma.quantity_given) AS total_dispensed
        FROM medicine_assistance ma
        JOIN medicines m ON ma.med_id = m.med_id
        WHERE YEAR(ma.date_given) >= 2022
        GROUP BY m.med_name";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['med_name']] = (float)$row['total_dispensed'];
}
echo json_encode($data);
?>
