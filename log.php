<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Health Center Login</title>
  <link rel="stylesheet" href="styles/log.css">
</head>
<body>
  <div class="login-container">
    <h2>Bagong Silang Patient Records Management System</h2>
    <form id="loginForm" action="login.php" method="POST">
      
      <!-- Role Selection -->
      <label for="role">Select Role:</label>
      <select id="role" name="role" required onchange="toggleFields()">
        <option value="" disabled selected>-- Select Role --</option>
        <option value="admin">Admin</option>
        <option value="staff">Staff</option>
      </select>

      <!-- Username / Email -->
      <div class="input-group">
        <label for="username">Username / Email:</label>
        <input type="text" id="username" name="username" required>
      </div>

      <!-- Password (Admin Only) -->
      <!-- Password (Admin Only) -->
<div class="input-group" id="passwordField" style="display:none; position: relative;">
  <label for="password">Password:</label>
  <input type="password" id="password" name="password">
  <span id="togglePassword" style="position: absolute; right: 10px; top: 35px; cursor: pointer;">👁️</span>
</div>

      <!-- ID Number (Staff Only) -->
      <div class="input-group" id="idNumberField" style="display:none;">
        <label for="id_number">ID Number:</label>
        <input type="text" id="id_number" name="id_number">
      </div>

      <!-- Submit Button -->
      <button type="submit" class="login-btn">Login</button>
    </form>
  </div>

  <script>
    function toggleFields() {
      const role = document.getElementById("role").value;
      const passwordField = document.getElementById("passwordField");
      const idNumberField = document.getElementById("idNumberField");

      if (role === "admin") {
        passwordField.style.display = "block";
        idNumberField.style.display = "none";
        document.getElementById("password").required = true;
        document.getElementById("id_number").required = false;
      } else if (role === "staff") {
        passwordField.style.display = "none";
        idNumberField.style.display = "block";
        document.getElementById("password").required = false;
        document.getElementById("id_number").required = true;
      }
    }
  </script>
<script>
  // Toggle password visibility
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", () => {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    togglePassword.textContent = "🙈"; // optional: change emoji when visible
  } else {
    passwordInput.type = "password";
    togglePassword.textContent = "👁️";
  }
});
</script>
</body>
</html>