<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admd.css">
</head>
<body>
  <!-- Navbar -->
  <header class="navbar">
    <div class="logo">Bagong Silang Health Center 🩺 </div>
    <nav>
      <ul>
        <li><a href="admd.php">Dashboard</a></li>

        <li><a href="pat.php">Patients</a></li>
       
        <li><a href="med.php">Medicine Assistance</a></li>
      
        
      </ul>
    </nav>
     <div class="user-info">
      <div class="avatar">S</div>
      <span>Staff</span>
      <form action="logout.php" method="post" style="display:inline;">
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>
  </header>

  <!-- Main Dashboard -->
  <main class="dashboard">
    <!-- Top summary cards -->
    <section class="summary-cards">
      <div class="card">Total Patients</div>
      <div class="card">Medicines in Stock</div>
      <div class="card">Low Stock Alerts</div>
      <div class="card">Today's Visits</div>
    </section>

    <!-- Charts Section -->
    <section class="charts">
      <div class="chart-box">Medicine Dispensing (Last 7 Days) [Bar Chart Placeholder]</div>
      <div class="chart-box">Inventory Forecast [Line Chart Placeholder]</div>
    </section>

    <!-- Recent Updates Section -->
    <section class="updates">
      <div class="update-box">Recent Medicine Dispensing [List Placeholder]</div>
      <div class="update-box">Recent Patient Updates [List Placeholder]</div>
    </section>

    <!-- Quick Actions -->
    <section class="quick-actions">
      <button class="btn green">Add Patient</button>
      <button class="btn blue">Add Inventory</button>
      <button class="btn purple">Dispense Medicine</button>
    </section>
  </main>
</body>
</html>
