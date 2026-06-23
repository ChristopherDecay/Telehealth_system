<?php
// File overview: Handles register functionality.
// Shared page header (session/nav/assets) for auth pages.
include "header.php";
require_once __DIR__ . "/functions.php";

$securityQuestions = getSecurityQuestionsMap();

// Build a lightweight math CAPTCHA challenge per page load.
$captchaA = random_int(1, 9);
$captchaB = random_int(1, 9);
$_SESSION['register_captcha_answer'] = (string)($captchaA + $captchaB);
?>

<!-- Registration page container and card layout. -->
<div class="auth-wrapper auth-wrapper--center" id="registerWrapper">
    <div class="auth-box auth-box--register">
        <h2>Create Account</h2>

        <!-- Show server-side error message passed via query string. -->
        <?php
if (isset($_GET['error'])): ?>
            <div class="error"><?php
echo $_GET['error']; ?></div>
        <?php
endif; ?>

        <!-- Account creation form with client-side validation before submit. -->
        <form method="post" action="process/register_process.php" onsubmit="return handleRegisterSubmit(this);" data-step-form>
            <div class="auth-step is-active" data-step="1">
                <label>Role</label>
                <select name="role">
                    <option value="">Select role</option>
                    <option>Patient</option>
                    <option>Caregiver</option>
                    <option>Doctor</option>
                    <option>Nurse</option>
                    <option>Labtech</option>
                    <option>Admin</option>
                </select>

                <label>Username</label>
                <input type="text" name="uname">

                <label>Password</label>
                <input type="password" name="pwd">

                <label>Confirm Password</label>
                <input type="password" name="pwd2">

                <div class="auth-actions">
                    <button type="button" class="btn btn-view" data-next-step>Next</button>
                </div>
            </div>

            <div class="auth-step" data-step="2">
                <button type="button" class="btn btn-view auth-back-btn" data-prev-step>Back</button>
                <div class="security-grid">
                    <?php foreach ($securityQuestions as $qKey => $qText): ?>
                        <div class="security-item">
                            <label>Security Question <?= strtoupper(substr($qKey, 1)) ?>: <?= htmlspecialchars($qText) ?></label>
                            <input type="text" name="security_answers[<?= htmlspecialchars($qKey) ?>]" autocomplete="off">
                        </div>
                    <?php endforeach; ?>
                </div>

                <label>CAPTCHA: What is <?= $captchaA ?> + <?= $captchaB ?>?</label>
                <input type="text" name="captcha" autocomplete="off">

                <div class="auth-actions auth-actions--right">
                    <input type="submit" value="Register">
                </div>
            </div>
        </form>
    </div>
</div>

<?php
// Shared page footer markup/scripts.
include "footer.php";
?>

<script>
    (function () {
        const form = document.querySelector("[data-step-form]");
        if (!form) return;

        const steps = Array.from(form.querySelectorAll(".auth-step"));
        const nextBtn = form.querySelector("[data-next-step]");
        const prevBtn = form.querySelector("[data-prev-step]");

        function showStep(stepNumber) {
            steps.forEach((step) => {
                step.classList.toggle("is-active", step.dataset.step === String(stepNumber));
            });
            const wrapper = document.getElementById("registerWrapper");
            if (wrapper) {
                wrapper.classList.toggle("auth-wrapper--center", String(stepNumber) === "1");
                wrapper.classList.toggle("auth-wrapper--top", String(stepNumber) !== "1");
            }
            const firstInput = form.querySelector(`.auth-step.is-active input, .auth-step.is-active select`);
            if (firstInput) firstInput.focus();
        }

        function getValue(fieldName) {
            const el = form.querySelector(`[name="${fieldName}"]`);
            return el ? (window.trimValue ? trimValue(el.value) : String(el.value || "").trim()) : "";
        }

        function validateStepOne() {
            const errors = [];
            if (getValue("role") === "") errors.push("Role is required.");
            if (getValue("uname") === "") errors.push("Username is required.");
            if (getValue("pwd") === "") errors.push("Password is required.");
            if (getValue("pwd2") === "") errors.push("Confirm your password.");
            if (getValue("pwd") !== "" && getValue("pwd2") !== "" && getValue("pwd") !== getValue("pwd2")) {
                errors.push("Passwords do not match.");
            }
            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }
            return true;
        }

        if (nextBtn) {
            nextBtn.addEventListener("click", function () {
                if (validateStepOne()) showStep(2);
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener("click", function () {
                showStep(1);
            });
        }

        window.handleRegisterSubmit = function (currentForm) {
            const activeStep = currentForm.querySelector(".auth-step.is-active");
            const stepNumber = activeStep ? activeStep.dataset.step : "1";
            if (stepNumber === "1") {
                if (validateStepOne()) {
                    showStep(2);
                }
                return false;
            }
            if (typeof validateRegisterForm === "function") {
                return validateRegisterForm(currentForm);
            }
            return true;
        };
    })();
</script>
