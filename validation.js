// File overview: Provides shared client-side validation helpers for forms.
// Shared input normalization helper used across all validators.
function trimValue(value) {
    return String(value || "").trim();
}

// Allows only letters (A-Z, a-z) and spaces.
function isAlphaSpace(value) {
    value = trimValue(value);
    if (value === "") return false;
    for (let i = 0; i < value.length; i++) {
        const ch = value[i];
        const code = ch.charCodeAt(0);
        const isUpper = code >= 65 && code <= 90;
        const isLower = code >= 97 && code <= 122;
        if (ch !== " " && !isUpper && !isLower) return false;
    }
    return true;
}

// Allows only letters and digits with no spaces/symbols.
function isAlnumSimple(value) {
    value = trimValue(value);
    if (value === "") return false;
    for (let i = 0; i < value.length; i++) {
        const code = value.charCodeAt(i);
        const isDigit = code >= 48 && code <= 57;
        const isUpper = code >= 65 && code <= 90;
        const isLower = code >= 97 && code <= 122;
        if (!isDigit && !isUpper && !isLower) return false;
    }
    return true;
}

// Allows only numeric digits 0-9.
function isDigitsOnly(value) {
    value = trimValue(value);
    if (value === "") return false;
    for (let i = 0; i < value.length; i++) {
        const code = value.charCodeAt(i);
        if (code < 48 || code > 57) return false;
    }
    return true;
}

// Allows letters, digits, and hyphens, with at least one alphanumeric.
function isShaNumber(value) {
    value = trimValue(value);
    if (value === "") return false;
    let hasAlnum = false;
    for (let i = 0; i < value.length; i++) {
        const ch = value[i];
        const code = value.charCodeAt(i);
        const isDigit = code >= 48 && code <= 57;
        const isUpper = code >= 65 && code <= 90;
        const isLower = code >= 97 && code <= 122;
        if (isDigit || isUpper || isLower) {
            hasAlnum = true;
            continue;
        }
        if (ch === "-") continue;
        return false;
    }
    return hasAlnum;
}

// Validates digit-only input with a length range.
function isDigitsLengthBetween(value, min, max) {
    if (!isDigitsOnly(value)) return false;
    return value.length >= min && value.length <= max;
}

// Basic phone rule: either + followed by 8-15 digits, or 7-15 digits.
function isPhoneNumber(value) {
    value = trimValue(value);
    if (value === "") return false;
    if (value[0] === "+") {
        const digits = value.slice(1);
        return isDigitsLengthBetween(digits, 8, 15);
    }
    return isDigitsLengthBetween(value, 7, 15);
}

// Lightweight email validation for common formats.
function isEmailBasic(value) {
    value = trimValue(value);
    if (value === "") return false;
    if (value.indexOf(" ") !== -1) return false;
    const atPos = value.indexOf("@");
    if (atPos <= 0) return false;
    if (value.indexOf("@", atPos + 1) !== -1) return false;
    const domain = value.slice(atPos + 1);
    if (domain.length < 3) return false;
    const dotPos = domain.indexOf(".");
    if (dotPos <= 0 || dotPos === domain.length - 1) return false;
    return true;
}

// Validates calendar date format DD-MM-YYYY and real month/day limits.
function isDateDmy(value) {
    value = trimValue(value);
    if (value.length !== 10) return false;
    if (value[2] !== "-" || value[5] !== "-") return false;
    const day = value.slice(0, 2);
    const month = value.slice(3, 5);
    const year = value.slice(6, 10);
    if (!isDigitsOnly(day) || !isDigitsOnly(month) || !isDigitsOnly(year)) return false;

    const d = parseInt(day, 10);
    const m = parseInt(month, 10);
    const y = parseInt(year, 10);
    if (m < 1 || m > 12) return false;
    if (d < 1 || d > 31) return false;

    const daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];
    const isLeap = (y % 4 === 0 && (y % 100 !== 0 || y % 400 === 0));
    if (isLeap) daysInMonth[1] = 29;
    if (d > daysInMonth[m - 1]) return false;
    return true;
}

// Validates DOB in DD-MM-YYYY, disallowing future dates and extreme ages.
function isRealisticDobDmy(value, maxAgeYears = 120) {
    value = trimValue(value);
    if (!isDateDmy(value)) return false;

    const day = parseInt(value.slice(0, 2), 10);
    const month = parseInt(value.slice(3, 5), 10);
    const year = parseInt(value.slice(6, 10), 10);
    const dob = new Date(year, month - 1, day);

    if (dob.getFullYear() !== year || dob.getMonth() !== month - 1 || dob.getDate() !== day) {
        return false;
    }

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (dob > today) return false;

    const minDate = new Date(today);
    minDate.setFullYear(today.getFullYear() - maxAgeYears);
    if (dob < minDate) return false;

    return true;
}

// Checks whether DOB in DD-MM-YYYY meets a minimum age threshold.
function isAtLeastAgeDmy(value, minAgeYears) {
    value = trimValue(value);
    if (!isDateDmy(value)) return false;
    const day = parseInt(value.slice(0, 2), 10);
    const month = parseInt(value.slice(3, 5), 10);
    const year = parseInt(value.slice(6, 10), 10);
    const dob = new Date(year, month - 1, day);
    if (dob.getFullYear() !== year || dob.getMonth() !== month - 1 || dob.getDate() !== day) {
        return false;
    }
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    let age = today.getFullYear() - dob.getFullYear();
    const m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    return age >= minAgeYears;
}

// Validates calendar date format YYYY-MM-DD and real month/day limits.
function isDateYmd(value) {
    value = trimValue(value);
    if (value.length !== 10) return false;
    if (value[4] !== "-" || value[7] !== "-") return false;
    const year = value.slice(0, 4);
    const month = value.slice(5, 7);
    const day = value.slice(8, 10);
    if (!isDigitsOnly(day) || !isDigitsOnly(month) || !isDigitsOnly(year)) return false;

    const d = parseInt(day, 10);
    const m = parseInt(month, 10);
    const y = parseInt(year, 10);
    if (m < 1 || m > 12) return false;
    if (d < 1 || d > 31) return false;

    const daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];
    const isLeap = (y % 4 === 0 && (y % 100 !== 0 || y % 400 === 0));
    if (isLeap) daysInMonth[1] = 29;
    if (d > daysInMonth[m - 1]) return false;
    return true;
}

// Validates 24-hour time format HH:MM.
function isTimeHm(value) {
    value = trimValue(value);
    if (value.length !== 5) return false;
    if (value[2] !== ":") return false;
    const hour = value.slice(0, 2);
    const minute = value.slice(3, 5);
    if (!isDigitsOnly(hour) || !isDigitsOnly(minute)) return false;
    const h = parseInt(hour, 10);
    const m = parseInt(minute, 10);
    return h >= 0 && h <= 23 && m >= 0 && m <= 59;
}

// Displays collected validation errors and returns form validity status.
function showErrors(errors) {
    if (errors.length > 0) {
        alert(errors.join("\n"));
        return false;
    }
    return true;
}

// Login form validation rules.
function validateLoginForm(form) {
    const errors = [];
    const role = trimValue(form.role.value);
    const uname = trimValue(form.uname.value);
    const pwd = trimValue(form.pwd.value);
    if (role === "") errors.push("Role is required.");
    if (uname === "") errors.push("Username is required.");
    if (pwd === "") errors.push("Password is required.");
    return showErrors(errors);
}

// Registration form validation rules.
function validateRegisterForm(form) {
    const errors = [];
    const role = trimValue(form.role.value);
    const uname = trimValue(form.uname.value);
    const pwd = trimValue(form.pwd.value);
    const pwd2 = trimValue(form.pwd2.value);
    const captcha = trimValue(form.captcha.value);
    if (role === "") errors.push("Role is required.");
    if (uname === "") errors.push("Username is required.");
    if (pwd === "") errors.push("Password is required.");
    if (pwd2 === "") errors.push("Confirm your password.");
    for (let i = 1; i <= 5; i++) {
        const el = form.querySelector(`input[name="security_answers[q${i}]"]`);
        if (!el || trimValue(el.value) === "") {
            errors.push("Please answer all 5 security questions.");
            break;
        }
    }
    if (captcha === "") errors.push("CAPTCHA answer is required.");
    if (pwd !== "" && pwd2 !== "" && pwd !== pwd2) errors.push("Passwords do not match.");
    return showErrors(errors);
}

// Forgot/reset password form validation.
function validateForgotPasswordLookupForm(form) {
    const errors = [];
    const role = trimValue(form.role.value);
    const uname = trimValue(form.uname.value);
    if (role === "") errors.push("Role is required.");
    if (uname === "") errors.push("Username is required.");
    return showErrors(errors);
}

function validateForgotPasswordResetForm(form) {
    const errors = [];
    const a1 = trimValue(form.answer1.value);
    const a2 = trimValue(form.answer2.value);
    const pwd = trimValue(form.pwd.value);
    const pwd2 = trimValue(form.pwd2.value);
    if (a1 === "" || a2 === "") errors.push("Please answer both security questions.");
    if (pwd === "") errors.push("Password is required.");
    if (pwd2 === "") errors.push("Confirm your password.");
    if (pwd !== "" && pwd2 !== "" && pwd !== pwd2) errors.push("Passwords do not match.");
    return showErrors(errors);
}

// Profile form validation with role-specific rules.
function validateProfileForm(form) {
    const errors = [];
    const fullname = trimValue(form.fullname.value);
    const dob = trimValue(form.dob.value);
    const gender = form.querySelector("input[name='gender']:checked");
    const idpp = trimValue(form.idpp.value);
    const phoneno = trimValue(form.phoneno.value);
    const email = trimValue(form.email.value);
    const role = trimValue(form.role.value);

    if (fullname === "") errors.push("Full name is required.");
    else if (!isAlphaSpace(fullname)) errors.push("Full name can only contain letters and spaces.");

    if (dob === "") errors.push("Date of birth is required.");
    else if (!isRealisticDobDmy(dob)) {
        errors.push("DOB must be a realistic date in DD-MM-YYYY format.");
    } else if (role === "Caregiver" && !isAtLeastAgeDmy(dob, 18)) {
        errors.push("Caregivers must be at least 18 years old.");
    } else if (role === "Nurse" && !isAtLeastAgeDmy(dob, 21)) {
        errors.push("Nurses must be at least 21 years old.");
    } else if (role === "Labtech" && !isAtLeastAgeDmy(dob, 21)) {
        errors.push("Labtechs must be at least 21 years old.");
    } else if (role === "Doctor" && !isAtLeastAgeDmy(dob, 25)) {
        errors.push("Doctors must be at least 25 years old.");
    }

    if (!gender) errors.push("Please select your gender.");

    if (idpp === "") errors.push("ID Number / Passport is required.");
    else if (!isAlnumSimple(idpp)) errors.push("ID / Passport can only contain letters and numbers.");

    if (phoneno === "") errors.push("Phone number is required.");
    else if (!isPhoneNumber(phoneno)) errors.push("Phone number must be either + and 8-15 digits or 7-15 digits.");

    if (email === "") errors.push("Email is required.");
    else if (!isEmailBasic(email)) errors.push("Invalid email format.");

    if (role === "Patient") {
        const nokname = trimValue(form.nokname.value);
        const nokphone = trimValue(form.nokphoneno.value);
        const shano = trimValue(form.shano.value);
        if (nokname === "") errors.push("Next of Kin Name is required.");
        if (nokphone === "" || !isPhoneNumber(nokphone)) errors.push("Valid Next of Kin Phone is required.");
        if (shano === "") errors.push("SHA Number is required.");
        else if (!isShaNumber(shano)) errors.push("SHA Number can only contain letters, numbers, and hyphens.");
    }

    if (role === "Caregiver") {
        const workhrs = trimValue(form.workhrs.value);
        if (workhrs === "") errors.push("Work hours are required.");
    }

    if (role === "Doctor" || role === "Nurse") {
        const hospital = trimValue(form.hospitalnm.value);
        if (hospital === "") errors.push("Please select a hospital.");
        if (role === "Doctor") {
            const specialization = trimValue(form.specialization.value);
            if (specialization === "") errors.push("Specialization is required.");
        }
    }

    if (role === "Doctor") {
        const kmpdc = trimValue(form.kmpdc.value);
        const experience = trimValue(form.experience.value);
        if (kmpdc === "") errors.push("KMPDC License is required.");
        if (experience === "" || !isDigitsOnly(experience)) errors.push("Experience must be a number.");
    }

    if (role === "Nurse") {
        const nck = trimValue(form.nck.value);
        if (nck === "") errors.push("NCK License is required.");
    }

    if (role === "Labtech") {
        const lab = trimValue(form.labnm.value);
        const kmlttb = trimValue(form.kmlttb.value);
        if (lab === "") errors.push("Please select a lab.");
        if (kmlttb === "") errors.push("KMLTTB License is required.");
    }

    return showErrors(errors);
}

// Auto-capitalization helpers for names and sentences.
function capitalizeWords(value) {
    let result = "";
    let shouldCapitalize = true;

    for (let i = 0; i < value.length; i++) {
        const ch = value[i];
        const code = ch.charCodeAt(0);
        const isLower = code >= 97 && code <= 122;
        const isUpper = code >= 65 && code <= 90;
        const isLetter = isLower || isUpper;

        if (isLetter && shouldCapitalize) {
            result += isLower ? String.fromCharCode(code - 32) : ch;
            shouldCapitalize = false;
        } else {
            result += ch;
            if (isLetter) shouldCapitalize = false;
        }

        if (ch === " " || ch === "-" || ch === "'") {
            shouldCapitalize = true;
        }
    }

    return result;
}

function capitalizeSentences(value) {
    let result = "";
    let shouldCapitalize = true;

    for (let i = 0; i < value.length; i++) {
        const ch = value[i];
        const code = ch.charCodeAt(0);
        const isLower = code >= 97 && code <= 122;
        const isUpper = code >= 65 && code <= 90;
        const isLetter = isLower || isUpper;

        if (isLetter && shouldCapitalize) {
            result += isLower ? String.fromCharCode(code - 32) : ch;
            shouldCapitalize = false;
        } else {
            result += ch;
            if (isLetter) shouldCapitalize = false;
        }

        if (ch === "." || ch === "!" || ch === "?") {
            shouldCapitalize = true;
        }
    }

    return result;
}

function attachAutoCapitalize() {
    const wordFields = document.querySelectorAll('[data-capitalize="words"]');
    const sentenceFields = document.querySelectorAll('[data-capitalize="sentences"]');

    wordFields.forEach(function (el) {
        el.addEventListener('input', function () {
            const start = el.selectionStart;
            const end = el.selectionEnd;
            const newVal = capitalizeWords(el.value);
            if (newVal !== el.value) {
                el.value = newVal;
                if (typeof start === 'number' && typeof end === 'number') {
                    el.setSelectionRange(start, end);
                }
            }
        });
    });

    sentenceFields.forEach(function (el) {
        el.addEventListener('input', function () {
            const start = el.selectionStart;
            const end = el.selectionEnd;
            const newVal = capitalizeSentences(el.value);
            if (newVal !== el.value) {
                el.value = newVal;
                if (typeof start === 'number' && typeof end === 'number') {
                    el.setSelectionRange(start, end);
                }
            }
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachAutoCapitalize);
} else {
    attachAutoCapitalize();
}

// Appointment booking form validation rules.
function validateBookAppointmentForm(form) {
    const errors = [];
    const date = trimValue(form.appointment_date.value);
    const time = trimValue(form.appointment_time.value);
    const duration = trimValue(form.duration_minutes.value);
    const reason = trimValue(form.reason.value);
    if (date === "" || !isDateDmy(date)) {
        errors.push("Date must be in DD-MM-YYYY format.");
    } else {
        const day = parseInt(date.slice(0, 2), 10);
        const month = parseInt(date.slice(3, 5), 10);
        const year = parseInt(date.slice(6, 10), 10);
        const selectedDate = new Date(year, month - 1, day);
        selectedDate.setHours(0, 0, 0, 0);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (selectedDate < today) {
            errors.push("Appointment date cannot be in the past.");
        }
    }
    if (time === "" || !isTimeHm(time)) errors.push("Time must be in HH:MM format.");
    if (date !== "" && isDateDmy(date) && time !== "" && isTimeHm(time)) {
        const day = parseInt(date.slice(0, 2), 10);
        const month = parseInt(date.slice(3, 5), 10);
        const year = parseInt(date.slice(6, 10), 10);
        const hour = parseInt(time.slice(0, 2), 10);
        const minute = parseInt(time.slice(3, 5), 10);
        const start = new Date(year, month - 1, day, hour, minute, 0, 0);
        const threshold = new Date(Date.now() + 60 * 60000);
        if (start < threshold) {
            errors.push("Appointment must be at least 1 hour from now.");
        }
    }
    if (duration === "") {
        errors.push("Please select a duration.");
    } else if (!isDigitsOnly(duration)) {
        errors.push("Duration must be a number of minutes.");
    } else {
        const minutes = parseInt(duration, 10);
        if (minutes < 20 || minutes > 120 || minutes % 10 !== 0) {
            errors.push("Duration must be 20-120 minutes in 10-minute steps.");
        } else if (date !== "" && isDateDmy(date) && time !== "" && isTimeHm(time)) {
            const day = parseInt(date.slice(0, 2), 10);
            const month = parseInt(date.slice(3, 5), 10);
            const year = parseInt(date.slice(6, 10), 10);
            const hour = parseInt(time.slice(0, 2), 10);
            const minute = parseInt(time.slice(3, 5), 10);
            const start = new Date(year, month - 1, day, hour, minute, 0, 0);
            const end = new Date(start.getTime() + minutes * 60000);
            const open = new Date(year, month - 1, day, 8, 0, 0, 0);
            const close = new Date(year, month - 1, day, 17, 0, 0, 0);
            if (start < open || end > close || end.getDate() !== start.getDate()) {
                errors.push("Appointments are only allowed during hospital hours (08:00-17:00).");
            }
        }
    }
    if (reason === "") errors.push("Please provide a brief reason for your appointment.");
    return showErrors(errors);
}

// Generic rating form validation (1-5 range).
function validateRateForm(form) {
    const errors = [];
    const entity = trimValue(form.entity_id.value);
    const rating = trimValue(form.rating.value);
    if (entity === "") errors.push("Please select an item to rate.");
    if (rating === "") errors.push("Rating is required.");
    else {
        const num = parseFloat(rating);
        if (isNaN(num) || num < 1 || num > 5) errors.push("Rating must be between 1 and 5.");
    }
    return showErrors(errors);
}

// Generic document/result upload validation.
function validateUploadDocumentForm(form) {
    const errors = [];
    const fileInput = form.document || form.resultfile;
    if (!fileInput || !fileInput.value) errors.push("Please select a file to upload.");
    return showErrors(errors);
}

// Caregiver document upload validation.
function validateCaregiverUploadDocumentForm(form) {
    const errors = [];
    const patient = trimValue(form.patient_id.value);
    if (patient === "") errors.push("Please select a patient.");
    if (!form.document || !form.document.value) errors.push("Please choose a file.");
    return showErrors(errors);
}

// Admin hospital management form validation.
function validateHospitalForm(form) {
    const errors = [];
    if (trimValue(form.hospital_name.value) === "") errors.push("Hospital name is required.");
    if (trimValue(form.location.value) === "") errors.push("Location is required.");
    if (trimValue(form.license.value) === "") errors.push("License is required.");
    return showErrors(errors);
}

// Admin lab management form validation.
function validateLabForm(form) {
    const errors = [];
    if (trimValue(form.lab_name.value) === "") errors.push("Lab name is required.");
    if (trimValue(form.lab_location.value) === "") errors.push("Lab location is required.");
    return showErrors(errors);
}

// User feedback form validation.
function validateFeedbackForm(form) {
    const errors = [];
    const feedback = trimValue(form.feedback.value);
    if (feedback === "") errors.push("Feedback is required.");
    return showErrors(errors);
}

// Admin feedback reply validation.
function validateAdminReplyForm(form) {
    const errors = [];
    const reply = trimValue(form.reply.value);
    if (reply === "") errors.push("Reply is required.");
    return showErrors(errors);
}

// Lab result submission validation.
function validateLabResultForm(form) {
    const errors = [];
    const result = trimValue(form.result.value);
    if (result === "") errors.push("Result is required.");
    return showErrors(errors);
}

// Caregiver request validation by patient identifier.
function validateCaregiverRequestForm(form) {
    const errors = [];
    const val = trimValue(form.patient_identifier.value);
    if (val === "") errors.push("Patient Username is required.");
    return showErrors(errors);
}
