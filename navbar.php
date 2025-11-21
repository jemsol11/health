<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']); // get current filename
?>
<header class="navbar">
  <div class="logo">Bagong Silang Health Center 🩺</div>
  <nav>
    <ul>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <li><a href="admd.php" class="<?= ($current_page == 'admd.php') ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="emp.php" class="<?= ($current_page == 'emp.php') ? 'active' : '' ?>">Accounts</a></li>
        <li><a href="pat.php" class="<?= ($current_page == 'pat.php') ? 'active' : '' ?>">Patients</a></li>
        <li><a href="inv.php" class="<?= ($current_page == 'inv.php') ? 'active' : '' ?>">Inventory</a></li>
        <li><a href="med.php" class="<?= ($current_page == 'med.php') ? 'active' : '' ?>">Medicine Assistance</a></li>
        <li><a href="rep.php" class="<?= ($current_page == 'rep.php') ? 'active' : '' ?>">Reports</a></li>
        <li><a href="aud.php" class="<?= ($current_page == 'aud.php') ? 'active' : '' ?>">Audit Trail</a></li>
      <?php elseif ($_SESSION['role'] === 'staff'): ?>
        <li><a href="staffd.php" class="<?= ($current_page == 'staffd.php') ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="pat.php" class="<?= ($current_page == 'pat.php') ? 'active' : '' ?>">Patients</a></li>
        <li><a href="med.php" class="<?= ($current_page == 'med.php') ? 'active' : '' ?>">Medicine Assistance</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <div class="user-info">
    <div class="avatar"><?php echo strtoupper(substr($_SESSION['role'], 0, 1)); ?></div>
    <span><?php echo ucfirst($_SESSION['role']); ?> - <?php echo $_SESSION['username']; ?></span>
    <form action="logout.php" method="post" style="display:inline;">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>
</header>
