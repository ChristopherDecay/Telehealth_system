<?php
// Handles forgot-password lookup and reset via security questions.
include "header.php";

$error = $_GET['error'] ?? "";
$info = $_GET['info'] ?? "";
$flow = $_SESSION['forgot_password_flow'] ?? null;
$hasChallenge = is_array($flow)
    && isset($flow['question1_text'], $flow['question2_text'], $flow['user_id'], $flow['role']);
?>

<div class="auth-wrapper auth-wrapper--center">
    <div class="auth-box">
        <h2>Forgot Password</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="success"><?= htmlspecialchars($info) ?></div>
        <?php endif; ?>

        <?php if (!$hasChallenge): ?>
            <form method="post" action="process/forgot_password_process.php" onsubmit="return validateForgotPasswordLookupForm(this);">
                <input type="hidden" name="action" value="lookup">

                <label for="role">Role</label>
                <select name="role" id="role">
                    <option value="">Select role</option>
                    <option>Patient</option>
                    <option>Caregiver</option>
                    <option>Doctor</option>
                    <option>Nurse</option>
                    <option>Labtech</option>
                    <option>Admin</option>
                </select>

                <label for="uname">Username</label>
                <input type="text" name="uname" id="uname">

                <input type="submit" value="Continue">
            </form>
        <?php else: ?>
            <form method="post" action="process/forgot_password_process.php" onsubmit="return validateForgotPasswordResetForm(this);">
                <input type="hidden" name="action" value="reset">

                <label><?= htmlspecialchars($flow['question1_text']) ?></label>
                <input type="text" name="answer1" autocomplete="off">

                <label><?= htmlspecialchars($flow['question2_text']) ?></label>
                <input type="text" name="answer2" autocomplete="off">

                <label>New Password</label>
                <input type="password" name="pwd">

                <label>Confirm New Password</label>
                <input type="password" name="pwd2">

                <input type="submit" value="Reset Password">
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>
