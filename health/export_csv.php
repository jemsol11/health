<?php
// export_csv.php
include 'db_connect.php';

$type = $_GET['type'] ?? 'inventory';
$allowed = ['inventory','medicine','patient'];
if (!in_array($type, $allowed)) {
    http_response_code(400);
    echo "Invalid type";
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$type.'_report.csv');

$out = fopen('php://output', 'w');

if ($type === 'inventory') {
    fputcsv($out, ['med_id','med_name','category','unit','quantity','batch_number','expiry_date']);
    $res = $conn->query("SELECT med_id,med_name,category,unit,quantity,batch_number,expiry_date FROM medicines ORDER BY med_name");
    if ($res) while ($r = $res->fetch_assoc()) fputcsv($out, $r);
} elseif ($type === 'medicine') {
    fputcsv($out, ['med_name','total_dispensed']);
    $res = $conn->query("SELECT m.med_name, SUM(ma.quantity_given) AS total_dispensed FROM medicine_assistance ma JOIN medicines m ON ma.med_id=m.med_id GROUP BY ma.med_id ORDER BY total_dispensed DESC");
    if ($res) while ($r = $res->fetch_assoc()) fputcsv($out, $r);
} elseif ($type === 'patient') {
    fputcsv($out, ['patient_id','fullname','age','sex','purok','date_registered']);
    $res = $conn->query("SELECT patient_id,fullname,age,sex,purok,date_registered FROM patients ORDER BY patient_id");
    if ($res) while ($r = $res->fetch_assoc()) fputcsv($out, $r);
}

fclose($out);
exit;
