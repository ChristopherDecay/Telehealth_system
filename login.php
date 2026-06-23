<?php
// File overview: Handles user login form display and submission setup.
include "header.php";

// Read optional query parameters used to show errors and keep selected role.
$error = $_GET['error'] ?? "";
$oldRole = $_GET['role'] ?? "";
?>

<div class="auth-wrapper auth-wrapper--center">
    <div class="auth-box">
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="process/login_process.php" onsubmit="return validateLoginForm(this);">

            <label for="role">Role</label>
            <select name="role" id="role">
                <option value="">Select role</option>
                <?php
                $roles = ["Patient","Caregiver","Doctor","Nurse","Labtech","Admin"];
                foreach ($roles as $r) {
                    $selected = ($oldRole === $r) ? "selected" : "";
                    echo "<option value='$r' $selected>$r</option>";
                }
                ?>
            </select>

            <label for="uname">Username</label>
            <input type="text" name="uname" id="uname">

            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd">

            <input type="submit" value="Login">
        </form>

        <div class="auth-help-link">
            <a href="/Telehealth_system/forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
