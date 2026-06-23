<?php
// Generate the next role-based user ID (e.g., PT001, DR010).
function generateID($pdo, $role) {

    $map = [
        "Patient"   => "PT",
        "Caregiver" => "CG",
        "Doctor"    => "DR",
        "Nurse"     => "NR",
        "Labtech"   => "LT",
        "Admin"     => "ADM"
    ];

    if (!isset($map[$role])) {
        throw new Exception("Unknown role: $role");
    }

    $prefix = $map[$role];

    $stmt = $pdo->prepare("
        SELECT UserID 
        FROM users 
        WHERE Role = :role 
          AND UserID LIKE :prefix
        ORDER BY UserID DESC 
        LIMIT 1
    ");

    $stmt->execute([
        ':role'   => $role,
        ':prefix' => $prefix . '%'
    ]);

    $lastID = $stmt->fetchColumn();

    $num = $lastID
        ? intval(substr($lastID, strlen($prefix))) + 1
        : 1;

    return $prefix . str_pad($num, 3, "0", STR_PAD_LEFT);
}

// Generate the next clinical session ID (e.g., SES000000000037).
function generateSessionID($pdo) {
    $stmt = $pdo->query(
        "SELECT SessionID
         FROM sessions
         WHERE SessionID LIKE 'SES%'
         ORDER BY SessionID DESC
         LIMIT 1"
    );
    $lastID = $stmt->fetchColumn();
    $num = $lastID ? (int)substr($lastID, 3) + 1 : 1;
    return 'SES' . str_pad((string)$num, 12, '0', STR_PAD_LEFT);
}

// Enforce password complexity rules and return true or an error message.
function isStrongPassword($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }

    $hasUpper = false;
    $hasLower = false;
    $hasNumber = false;
    $hasSpecial = false;

    $len = strlen($password);
    for ($i = 0; $i < $len; $i++) {
        $ch = $password[$i];
        $ord = ord($ch);

        if ($ord >= 65 && $ord <= 90) {
            $hasUpper = true;
        } elseif ($ord >= 97 && $ord <= 122) {
            $hasLower = true;
        } elseif ($ord >= 48 && $ord <= 57) {
            $hasNumber = true;
        } else {
            $hasSpecial = true;
        }
    }

    if (!$hasUpper)   return "Password must contain at least one uppercase letter";
    if (!$hasLower)   return "Password must contain at least one lowercase letter";
    if (!$hasNumber)  return "Password must contain at least one number";
    if (!$hasSpecial) return "Password must contain at least one special character";

    return true; // password is strong
}

// Validate that input contains only letters and spaces.
function isAlphaSpace($value) {
    $value = (string)$value;
    if ($value === '') return false;
    $len = strlen($value);
    for ($i = 0; $i < $len; $i++) {
        $ch = $value[$i];
        $ord = ord($ch);
        if ($ch !== ' ' && !($ord >= 65 && $ord <= 90) && !($ord >= 97 && $ord <= 122)) {
            return false;
        }
    }
    return true;
}

// Validate that input contains only letters and digits.
function isAlnumSimple($value) {
    $value = (string)$value;
    if ($value === '') return false;
    $len = strlen($value);
    for ($i = 0; $i < $len; $i++) {
        $ch = $value[$i];
        $ord = ord($ch);
        $isDigit = ($ord >= 48 && $ord <= 57);
        $isUpper = ($ord >= 65 && $ord <= 90);
        $isLower = ($ord >= 97 && $ord <= 122);
        if (!($isDigit || $isUpper || $isLower)) {
            return false;
        }
    }
    return true;
}

// Validate that input contains only numeric digits.
function isDigitsOnly($value) {
    $value = (string)$value;
    if ($value === '') return false;
    $len = strlen($value);
    for ($i = 0; $i < $len; $i++) {
        $ord = ord($value[$i]);
        if ($ord < 48 || $ord > 57) {
            return false;
        }
    }
    return true;
}

// Validate SHA number: letters, digits, and hyphens only, with at least one alphanumeric.
function isShaNumber($value) {
    $value = trim((string)$value);
    if ($value === '') return false;
    $len = strlen($value);
    $hasAlnum = false;
    for ($i = 0; $i < $len; $i++) {
        $ch = $value[$i];
        $ord = ord($ch);
        $isDigit = ($ord >= 48 && $ord <= 57);
        $isUpper = ($ord >= 65 && $ord <= 90);
        $isLower = ($ord >= 97 && $ord <= 122);
        if ($isDigit || $isUpper || $isLower) {
            $hasAlnum = true;
            continue;
        }
        if ($ch === '-') {
            continue;
        }
        return false;
    }
    return $hasAlnum;
}

// Validate digit-only input length within a min/max range.
function isDigitsLengthBetween($value, $min, $max) {
    if (!isDigitsOnly($value)) return false;
    $len = strlen((string)$value);
    return $len >= $min && $len <= $max;
}

// Validate phone format: +8-15 digits or 7-15 digits.
function isPhoneNumber($value) {
    $value = trim((string)$value);
    if ($value === '') return false;

    if ($value[0] === '+') {
        $digits = substr($value, 1);
        return isDigitsLengthBetween($digits, 8, 15);
    }

    return isDigitsLengthBetween($value, 7, 15);
}

// Perform lightweight email format validation.
function isEmailBasic($value) {
    $value = trim((string)$value);
    if ($value === '') return false;
    if (strpos($value, ' ') !== false) return false;

    $atPos = strpos($value, '@');
    if ($atPos === false) return false;
    if ($atPos === 0) return false;
    if ($atPos !== strrpos($value, '@')) return false;

    $domain = substr($value, $atPos + 1);
    if ($domain === '') return false;
    $dotPos = strpos($domain, '.');
    if ($dotPos === false) return false;
    if ($dotPos === 0) return false;
    if ($dotPos === strlen($domain) - 1) return false;

    return true;
}

// Validate DD-MM-YYYY format including real calendar dates.
function isDateDmy($value) {
    $value = trim((string)$value);
    if (strlen($value) !== 10) return false;
    if ($value[2] !== '-' || $value[5] !== '-') return false;

    $day = substr($value, 0, 2);
    $month = substr($value, 3, 2);
    $year = substr($value, 6, 4);

    if (!isDigitsOnly($day) || !isDigitsOnly($month) || !isDigitsOnly($year)) return false;

    $d = (int)$day;
    $m = (int)$month;
    $y = (int)$year;
    if ($m < 1 || $m > 12) return false;
    if ($d < 1 || $d > 31) return false;

    $daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];
    $isLeap = ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0));
    if ($isLeap) $daysInMonth[1] = 29;
    if ($d > $daysInMonth[$m - 1]) return false;

    return true;
}

// Validate DOB is real, not future, and not older than max age.
function isRealisticDobDmy($value, $maxAgeYears = 120) {
    $value = trim((string)$value);
    if (!isDateDmy($value)) return false;
    $dob = DateTime::createFromFormat('d-m-Y', $value);
    if ($dob === false) return false;

    $today = new DateTime('today');
    if ($dob > $today) return false;

    $minDate = (clone $today)->modify('-' . (int)$maxAgeYears . ' years');
    if ($dob < $minDate) return false;

    return true;
}

// Check whether a DD-MM-YYYY DOB meets a minimum age.
function isAtLeastAgeDmy($value, $minAgeYears) {
    $value = trim((string)$value);
    if (!isDateDmy($value)) return false;
    $dob = DateTime::createFromFormat('d-m-Y', $value);
    if ($dob === false) return false;
    $today = new DateTime('today');
    $age = $today->diff($dob)->y;
    return $age >= (int)$minAgeYears;
}

// Convert date from DD-MM-YYYY to YYYY-MM-DD.
function dmyToYmd($value) {
    $value = trim((string)$value);
    if (!isDateDmy($value)) return null;
    $dt = DateTime::createFromFormat('d-m-Y', $value);
    if ($dt === false) return null;
    return $dt->format('Y-m-d');
}

// Validate YYYY-MM-DD format including real calendar dates.
function isDateYmd($value) {
    $value = trim((string)$value);
    if (strlen($value) !== 10) return false;
    if ($value[4] !== '-' || $value[7] !== '-') return false;

    $year = substr($value, 0, 4);
    $month = substr($value, 5, 2);
    $day = substr($value, 8, 2);

    if (!isDigitsOnly($day) || !isDigitsOnly($month) || !isDigitsOnly($year)) return false;

    $d = (int)$day;
    $m = (int)$month;
    $y = (int)$year;
    if ($m < 1 || $m > 12) return false;
    if ($d < 1 || $d > 31) return false;

    $daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];
    $isLeap = ($y % 4 === 0 && ($y % 100 !== 0 || $y % 400 === 0));
    if ($isLeap) $daysInMonth[1] = 29;
    if ($d > $daysInMonth[$m - 1]) return false;

    return true;
}

// Convert date from YYYY-MM-DD to DD-MM-YYYY.
function ymdToDmy($value) {
    $value = trim((string)$value);
    if (!isDateYmd($value)) return null;
    $dt = DateTime::createFromFormat('Y-m-d', $value);
    if ($dt === false) return null;
    return $dt->format('d-m-Y');
}

// Validate 24-hour time in HH:MM format.
function isTimeHm($value) {
    $value = trim((string)$value);
    if (strlen($value) !== 5) return false;
    if ($value[2] !== ':') return false;

    $hour = substr($value, 0, 2);
    $min = substr($value, 3, 2);
    if (!isDigitsOnly($hour) || !isDigitsOnly($min)) return false;

    $h = (int)$hour;
    $m = (int)$min;
    if ($h < 0 || $h > 23) return false;
    if ($m < 0 || $m > 59) return false;

    return true;
}

// Validate appointment duration range and step interval.
function isValidDurationMinutes($value, $min = 20, $max = 120, $step = 10) {
    $value = trim((string)$value);
    if ($value === '' || !isDigitsOnly($value)) return false;
    $minutes = (int)$value;
    if ($minutes < $min || $minutes > $max) return false;
    if ($minutes % $step !== 0) return false;
    return true;
}

// Validate appointment start/end are within regular hospital timings.
function isWithinHospitalHours($appointmentDateTime, $durationMinutes, $openTime = '08:00', $closeTime = '17:00') {
    $start = new DateTime((string)$appointmentDateTime);
    $minutes = (int)$durationMinutes;
    if ($minutes <= 0) return false;

    $end = (clone $start)->modify('+' . $minutes . ' minutes');
    $date = $start->format('Y-m-d');
    $open = new DateTime($date . ' ' . $openTime . ':00');
    $close = new DateTime($date . ' ' . $closeTime . ':00');

    if ($end->format('Y-m-d') !== $date) return false;
    return $start >= $open && $end <= $close;
}

// Validate appointment start is at least the given lead time from now.
function isAtLeastLeadTimeFromNow($appointmentDateTime, $minLeadMinutes = 60) {
    $start = new DateTime((string)$appointmentDateTime);
    $threshold = new DateTime();
    $threshold->modify('+' . (int)$minLeadMinutes . ' minutes');
    return $start >= $threshold;
}

// Notification helpers.
// Insert a new unread notification for a user.
function addNotification($pdo, $userID, $role, $title, $message, $link = null) {
    if ($userID === null || $userID === '') return;
    $stmt = $pdo->prepare(
        "INSERT INTO notifications
         (UserID, Role, Title, Message, Link, IsRead)
         VALUES (:uid, :role, :title, :msg, :link, 0)"
    );
    $stmt->execute([
        ':uid' => $userID,
        ':role' => $role,
        ':title' => $title,
        ':msg' => $message,
        ':link' => $link
    ]);
}

// File/input utility helpers.
// Extract and normalize a file extension from a filename.
function getFileExtension($filename) {
    $filename = (string)$filename;
    $pos = strrpos($filename, '.');
    if ($pos === false) return '';
    return strtolower(substr($filename, $pos + 1));
}

// Check if a file extension is in the allowed list.
function isAllowedExtension($filename, $allowed) {
    $ext = getFileExtension($filename);
    if ($ext === '') return false;
    return in_array($ext, $allowed, true);
}

// Check whether a trimmed value is non-empty.
function isNonEmpty($value) {
    return trim((string)$value) !== '';
}

// Database ensure/migration helpers.
// Create login_logs table if it does not exist.
function ensureLoginLogsTable($pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS login_logs (
            LogID INT AUTO_INCREMENT PRIMARY KEY,
            UserID VARCHAR(20) NOT NULL,
            Role VARCHAR(20) NOT NULL,
            SessionID VARCHAR(128) NOT NULL,
            LoginAt DATETIME NOT NULL,
            LogoutAt DATETIME NULL,
            SessionMinutes INT NULL,
            IPAddress VARCHAR(45) NULL,
            INDEX idx_login_logs_user (UserID),
            INDEX idx_login_logs_session (SessionID),
            INDEX idx_login_logs_login (LoginAt)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}

// Create caregiver_messages table if it does not exist.
function ensureCaregiverMessagesTable($pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS caregiver_messages (
            MessageID INT AUTO_INCREMENT PRIMARY KEY,
            AppID INT NOT NULL,
            SenderRole VARCHAR(20) NOT NULL,
            SenderID VARCHAR(20) NOT NULL,
            Message TEXT NOT NULL,
            MsgDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_caregiver_msg_app (AppID),
            INDEX idx_caregiver_msg_sender (SenderID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}

// Create lab_test_messages table if it does not exist.
function ensureLabTestMessagesTable($pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS lab_test_messages (
            MessageID INT AUTO_INCREMENT PRIMARY KEY,
            LabTestID INT NOT NULL,
            SenderRole VARCHAR(20) NOT NULL,
            SenderID VARCHAR(20) NOT NULL,
            Message TEXT NOT NULL,
            MsgDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_labtest_msg_test (LabTestID),
            INDEX idx_labtest_msg_sender (SenderID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}

// Ensure the ResultDate column exists on lab_tests.
function ensureLabTestResultDateColumn($pdo) {
    $check = $pdo->query("SHOW COLUMNS FROM lab_tests LIKE 'ResultDate'")->fetch(PDO::FETCH_ASSOC);
    if (!$check) {
        $pdo->exec("ALTER TABLE lab_tests ADD COLUMN ResultDate DATETIME NULL");
    }
}

// Ensure lab-test patient-approval workflow columns exist.
function ensureLabTestApprovalColumns($pdo) {
    $check = $pdo->query("SHOW COLUMNS FROM lab_tests LIKE 'PatientApprovalStatus'")->fetch(PDO::FETCH_ASSOC);
    if (!$check) {
        $pdo->exec("ALTER TABLE lab_tests ADD COLUMN PatientApprovalStatus VARCHAR(20) NOT NULL DEFAULT 'Accepted'");
    }

    $check = $pdo->query("SHOW COLUMNS FROM lab_tests LIKE 'RequestedByDoctorID'")->fetch(PDO::FETCH_ASSOC);
    if (!$check) {
        $pdo->exec("ALTER TABLE lab_tests ADD COLUMN RequestedByDoctorID VARCHAR(20) NULL");
    }

    $check = $pdo->query("SHOW COLUMNS FROM lab_tests LIKE 'RequestNote'")->fetch(PDO::FETCH_ASSOC);
    if (!$check) {
        $pdo->exec("ALTER TABLE lab_tests ADD COLUMN RequestNote TEXT NULL");
    }
}

// Create chat_reads table if it does not exist.
function ensureChatReadsTable($pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS chat_reads (
            ChatType VARCHAR(20) NOT NULL,
            ChatID INT NOT NULL,
            UserID VARCHAR(20) NOT NULL,
            Role VARCHAR(20) NOT NULL,
            LastReadAt DATETIME NOT NULL,
            PRIMARY KEY (ChatType, ChatID, UserID),
            INDEX idx_chat_reads_time (LastReadAt)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}

// Chat read-tracking helpers.
// Insert or update a user read timestamp for a chat.
function upsertChatRead($pdo, $chatType, $chatID, $userID, $role) {
    $stmt = $pdo->prepare(
        "INSERT INTO chat_reads (ChatType, ChatID, UserID, Role, LastReadAt)
         VALUES (:ctype, :cid, :uid, :role, NOW())
         ON DUPLICATE KEY UPDATE LastReadAt = NOW()"
    );
    $stmt->execute([
        ':ctype' => $chatType,
        ':cid' => $chatID,
        ':uid' => $userID,
        ':role' => $role
    ]);
}

// Fetch last-read timestamps for users in a chat context.
function getChatReads($pdo, $chatType, $chatID, $userIDs) {
    if (empty($userIDs)) return [];
    $placeholders = implode(',', array_fill(0, count($userIDs), '?'));
    $sql = "SELECT UserID, LastReadAt
            FROM chat_reads
            WHERE ChatType = ?
              AND ChatID = ?
              AND UserID IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge([$chatType, $chatID], $userIDs));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $map = [];
    foreach ($rows as $r) {
        $map[$r['UserID']] = $r['LastReadAt'];
    }
    return $map;
}

// Chat authorization/context helpers.
// Build appointment chat context and enforce participant access rules.
function getAppointmentChatContext($pdo, $appointmentID, $userID, $role) {
    $appointmentID = trim((string)$appointmentID);
    if ($appointmentID === '') {
        return ['allowed' => false];
    }

    $stmt = $pdo->prepare(
        "SELECT a.AppID, a.PatientID, a.DoctorID, a.NurseID,
                p.FName AS PatientName,
                d.FName AS DoctorName,
                n.FName AS NurseName,
                up.Uname AS PatientUname,
                ud.Uname AS DoctorUname,
                un.Uname AS NurseUname
         FROM appointments a
         LEFT JOIN patients p ON a.PatientID = p.PatientID
         LEFT JOIN doctors d ON a.DoctorID = d.DoctorID
         LEFT JOIN nurses n ON a.NurseID = n.NurseID
         LEFT JOIN users up ON a.PatientID = up.UserID
         LEFT JOIN users ud ON a.DoctorID = ud.UserID
         LEFT JOIN users un ON a.NurseID = un.UserID
         WHERE a.AppID = :aid"
    );
    $stmt->execute([':aid' => $appointmentID]);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$appt) {
        return ['allowed' => false];
    }

    $allowed = false;
    $labInfo = null;

    if ($role === 'Patient' && $appt['PatientID'] === $userID) {
        $allowed = true;
    } elseif ($role === 'Doctor' && $appt['DoctorID'] === $userID) {
        $allowed = true;
    } elseif ($role === 'Nurse' && $appt['NurseID'] === $userID) {
        $allowed = true;
    } elseif ($role === 'Labtech') {
        $labStmt = $pdo->prepare(
            "SELECT l.LabID, lab.LabName
             FROM labtechs l
             JOIN laboratories lab ON l.LabID = lab.LabID
             WHERE l.LabTechID = :id"
        );
        $labStmt->execute([':id' => $userID]);
        $labInfo = $labStmt->fetch(PDO::FETCH_ASSOC);

        if ($labInfo) {
            $check = $pdo->prepare(
                "SELECT 1
                 FROM lab_tests lt
                 WHERE lt.PatientID = :pid
                   AND lt.LabID = :labid
                   AND (
                       SELECT a.AppID
                       FROM appointments a
                       WHERE a.PatientID = lt.PatientID
                         AND (lt.TestDate IS NULL OR a.AppointmentDate <= lt.TestDate)
                       ORDER BY a.AppointmentDate DESC
                       LIMIT 1
                   ) = :aid
                 LIMIT 1"
            );
            $check->execute([
                ':pid' => $appt['PatientID'],
                ':labid' => $labInfo['LabID'],
                ':aid' => $appt['AppID']
            ]);
            if ($check->fetchColumn()) {
                $allowed = true;
            }
        }
    }

    if (!$allowed) {
        return ['allowed' => false];
    }

    return [
        'allowed' => true,
        'appointment' => $appt,
        'participants' => [
            'Patient' => $appt['PatientName'] ?: ($appt['PatientUname'] ?: '-'),
            'Doctor' => $appt['DoctorName'] ?: ($appt['DoctorUname'] ?: '-'),
            'Nurse' => $appt['NurseName'] ?: ($appt['NurseUname'] ?: '-')
        ],
        'lab' => $labInfo
    ];
}

// Return user-friendly appointment status labels without changing DB values.
function getAppointmentStatusLabel($status, $doctorID = null, $doctorRejectedAt = null) {
    $status = (string)$status;
    switch ($status) {
        case 'Pending':
            if ($doctorID === null || $doctorID === '') {
                return !empty($doctorRejectedAt) ? 'Needs Reassignment' : 'Awaiting Doctor Assignment';
            }
            return 'Scheduled';
        case 'AwaitingPatientApproval':
            return 'Awaiting Patient Approval';
        case 'Confirmed':
            return 'Doctor Confirmed';
        case 'Completed':
            return 'Consultation Completed';
        case 'Cancelled':
            return 'Cancelled by Patient';
        case 'Rejected':
            return 'Rejected';
        default:
            return $status;
    }
}

// Build a safe in-app back URL from HTTP_REFERER, with a local fallback.
function getSafeBackUrl($fallbackRelative) {
    $fallback = (string)$fallbackRelative;
    $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
    if ($referer === '') {
        return $fallback;
    }

    $parts = parse_url($referer);
    if (!is_array($parts)) {
        return $fallback;
    }

    $refererHost = (string)($parts['host'] ?? '');
    $currentHost = (string)($_SERVER['HTTP_HOST'] ?? '');
    if ($refererHost !== '' && $currentHost !== '' && strcasecmp($refererHost, $currentHost) !== 0) {
        return $fallback;
    }

    $path = (string)($parts['path'] ?? '');
    if ($path === '') {
        return $fallback;
    }

    $currentPath = (string)parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if ($currentPath !== '' && $path === $currentPath) {
        return $fallback;
    }

    $query = (string)($parts['query'] ?? '');
    if ($query !== '') {
        return $path . '?' . $query;
    }

    return $path;
}

// Security question helpers.
// Return fixed registration/recovery security questions keyed by stable IDs.
function getSecurityQuestionsMap() {
    return [
        'q1' => 'What was the name of your first school?',
        'q2' => 'What is your mother\'s maiden name?',
        'q3' => 'What city were you born in?',
        'q4' => 'What is the name of your childhood best friend?',
        'q5' => 'What was your favourite colour?'
    ];
}

// Normalize answers so comparison is case-insensitive and whitespace-tolerant.
function normalizeSecurityAnswer($value) {
    $value = trim((string)$value);
    $normalized = '';
    $lastWasSpace = false;
    $len = strlen($value);

    for ($i = 0; $i < $len; $i++) {
        $ch = $value[$i];
        $isSpace = ($ch === ' ' || $ch === "\t" || $ch === "\n" || $ch === "\r");
        if ($isSpace) {
            if (!$lastWasSpace) {
                $normalized .= ' ';
                $lastWasSpace = true;
            }
        } else {
            $normalized .= $ch;
            $lastWasSpace = false;
        }
    }

    return strtolower(trim($normalized));
}

// Ensure table for per-user security questions exists.
function ensureUserSecurityQuestionsTable($pdo) {
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS user_security_questions (
            UserID VARCHAR(20) NOT NULL,
            QuestionKey VARCHAR(10) NOT NULL,
            AnswerHash VARCHAR(255) NOT NULL,
            CreatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UpdatedAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (UserID, QuestionKey),
            INDEX idx_user_security_q_user (UserID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8"
    );
}

// Ratings helpers.
// Return average ratings and counts keyed by entity ID for a rating type.
function getEntityRatingsMap($pdo, $ratingType) {
    $ratingType = trim((string)$ratingType);
    if ($ratingType === '') return [];

    $stmt = $pdo->prepare(
        "SELECT EntityID, AVG(RatingValue) AS AvgRating, COUNT(*) AS RatingCount
         FROM ratings
         WHERE EntityType = :type
         GROUP BY EntityID"
    );
    $stmt->execute([':type' => $ratingType]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $map = [];
    foreach ($rows as $row) {
        $entityID = (string)$row['EntityID'];
        if ($entityID === '') continue;
        $map[$entityID] = [
            'avg' => isset($row['AvgRating']) ? (float)$row['AvgRating'] : 0.0,
            'count' => isset($row['RatingCount']) ? (int)$row['RatingCount'] : 0
        ];
    }
    return $map;
}

// Format rating data into a compact label for dropdowns.
function formatEntityRatingLabel($ratingInfo) {
    if (!is_array($ratingInfo)) {
        return "----- No ratings yet";
    }
    $count = (int)($ratingInfo['count'] ?? 0);
    if ($count <= 0) {
        return "----- No ratings yet";
    }
    $avg = (float)($ratingInfo['avg'] ?? 0);
    $rounded = (int)round($avg);
    if ($rounded < 0) $rounded = 0;
    if ($rounded > 5) $rounded = 5;
    $stars = str_repeat('*', $rounded) . str_repeat('-', 5 - $rounded);
    return $stars . " " . number_format($avg, 1) . "/5 (" . $count . ")";
}
?>
