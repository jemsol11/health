<?php
// report_generate.php
include 'db_connect.php';

$type = $_POST['type'] ?? 'inventory';
$start = $_POST['start'] ?? '';
$end = $_POST['end'] ?? '';
$filter = trim($_POST['filter'] ?? '');

$start_q = $start ? $conn->real_escape_string($start) : '';
$end_q = $end ? $conn->real_escape_string($end) : '';
$filter_q = $filter ? $conn->real_escape_string($filter) : '';

$title = ucfirst($type) . " Report";
$data = [];

if ($type === 'inventory') {
    $sql = "SELECT med_name,category,unit,quantity,batch_number,expiry_date FROM medicines";
    if ($filter_q) $sql .= " WHERE med_name LIKE '%{$filter_q}%'";
    $res = $conn->query($sql);
    while ($r = $res->fetch_assoc()) $data[] = $r;
} elseif ($type === 'medicine') {
    $sql = "SELECT m.med_name, SUM(ma.quantity_given) AS total_dispensed, MIN(ma.date_given) AS first_date, MAX(ma.date_given) AS last_date FROM medicine_assistance ma JOIN medicines m ON ma.med_id=m.med_id";
    $where = [];
    if ($filter_q) $where[] = "m.med_name LIKE '%{$filter_q}%'";
    if ($start_q) $where[] = "DATE(ma.date_given) >= '$start_q'";
    if ($end_q) $where[] = "DATE(ma.date_given) <= '$end_q'";
    if ($where) $sql .= " WHERE " . implode(' AND ', $where);
    $sql .= " GROUP BY m.med_name ORDER BY total_dispensed DESC";
    $res = $conn->query($sql);
    while ($r = $res->fetch_assoc()) $data[] = $r;
} else { // patient
    $sql = "SELECT patient_id,fullname,age,sex,purok,date_registered FROM patients";
    if ($filter_q) $sql .= " WHERE fullname LIKE '%{$filter_q}%'";
    if ($start_q || $end_q) {
        $w = [];
        if ($start_q) $w[] = "DATE(date_registered) >= '$start_q'";
        if ($end_q) $w[] = "DATE(date_registered) <= '$end_q'";
        $sql .= ($filter_q ? " AND " : " WHERE ") . implode(' AND ', $w);
    }
    $res = $conn->query($sql);
    while ($r = $res->fetch_assoc()) $data[] = $r;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title) ?></title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;padding:20px;color:#222}
    h1{margin-bottom:4px}
    .meta{color:#666;font-size:13px;margin-bottom:12px}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #ddd;padding:8px;text-align:left}
    th{background:#f5f5f5}
    .small{font-size:12px;color:#666}
    @media print {
      .no-print { display:none; }
    }
  </style>
</head>
<body>
 <div class="no-print" style="margin-bottom:12px; display:flex; gap:10px;">
  <button 
    onclick="window.print()" 
    style="padding:8px 12px;background:#9A3F3F;color:#fff;border:none;border-radius:6px;cursor:pointer">
    🖨️ Print / Save PDF
  </button>

  <button 
    onclick="exitReport()" 
    style="padding:8px 12px;background:#555;color:#fff;border:none;border-radius:6px;cursor:pointer">
    ❌ Cancel / Exit
  </button>
</div>

  
  <?php if (empty($data)): ?>
    <div style="padding:30px;background:#fff;border-radius:8px;color:#666">No results found for the selected filters.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <?php
          // use keys from first row
          $keys = array_keys($data[0]);
          foreach ($keys as $k) echo "<th>".htmlspecialchars($k)."</th>";
          ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data as $row): ?>
          <tr>
            <?php foreach ($keys as $k): ?>
              <td><?= htmlspecialchars($row[$k]) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <script>
function exitReport() {
  if (window.history.length > 1) {
    // Go back to the previous page if possible
    window.history.back();
  } else {
    // Or redirect to your reports page
    window.location.href = 'rep.php';
  }
}
</script>

</body>
</html>
