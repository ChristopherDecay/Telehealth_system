<?php
// File overview: Handles admin reports functionality.
session_start();
require "../db.php";
require "../functions.php";

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

ensureLoginLogsTable($pdo);
ensureCaregiverMessagesTable($pdo);
ensureLabTestMessagesTable($pdo);
ensureLabTestResultDateColumn($pdo);
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$labRatings = getEntityRatingsMap($pdo, 'Lab');

function formatMinutes($minutes) {
    if ($minutes === null) return "-";
    $minutes = (int)$minutes;
    if ($minutes < 60) return $minutes . "m";
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return $hours . "h " . $mins . "m";
}

function formatDateOrDash($value) {
    if (!$value) return '-';
    return date('d-m-Y', strtotime($value));
}

function outputCsv($filename, $headers, $rows) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    foreach ($rows as $row) {
        $line = [];
        foreach ($headers as $h) {
            $line[] = $row[$h] ?? '';
        }
        fputcsv($out, $line);
    }
    fclose($out);
    exit;
}

$today = date('Y-m-d');
$defaultStart = date('Y-m-d', strtotime('-29 days'));
$startDate = $_GET['start'] ?? $defaultStart;
$endDate = $_GET['end'] ?? $today;
if (!isDateYmd($startDate)) $startDate = $defaultStart;
if (!isDateYmd($endDate)) $endDate = $today;
$startDateTime = $startDate . ' 00:00:00';
$endDateTime = $endDate . ' 23:59:59';

/* Login activity */
$loginSummary = $pdo->query(
    "SELECT COUNT(*) AS TotalLogins, COUNT(DISTINCT UserID) AS UniqueUsers
     FROM login_logs
     WHERE LoginAt >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"
)->fetch(PDO::FETCH_ASSOC);

$activeSessions = (int)$pdo->query(
    "SELECT COUNT(*) FROM login_logs WHERE LogoutAt IS NULL"
)->fetchColumn();

$dailyLogins = $pdo->query(
    "SELECT DATE(LoginAt) AS LogDate,
            COUNT(*) AS TotalLogins,
            COUNT(DISTINCT UserID) AS UniqueUsers
     FROM login_logs
     WHERE LoginAt >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
     GROUP BY DATE(LoginAt)
     ORDER BY LogDate DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$recentLogins = $pdo->query(
    "SELECT l.LoginAt, l.LogoutAt, l.SessionMinutes, l.Role,
            u.Uname, l.IPAddress
     FROM login_logs l
     LEFT JOIN users u ON u.UserID = l.UserID
     ORDER BY l.LoginAt DESC
     LIMIT 20"
)->fetchAll(PDO::FETCH_ASSOC);

/* Time usage */
$usageByRole = $pdo->query(
    "SELECT Role,
            SUM(SessionMinutes) AS TotalMinutes,
            AVG(SessionMinutes) AS AvgMinutes,
            COUNT(*) AS Sessions
     FROM login_logs
     WHERE LogoutAt IS NOT NULL
       AND LoginAt >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
     GROUP BY Role
     ORDER BY TotalMinutes DESC"
)->fetchAll(PDO::FETCH_ASSOC);

$usageByUser = $pdo->query(
    "SELECT l.UserID, u.Uname, l.Role,
            SUM(l.SessionMinutes) AS TotalMinutes,
            COUNT(*) AS Sessions
     FROM login_logs l
     LEFT JOIN users u ON u.UserID = l.UserID
     WHERE l.LogoutAt IS NOT NULL
       AND l.LoginAt >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
     GROUP BY l.UserID, u.Uname, l.Role
     ORDER BY TotalMinutes DESC
     LIMIT 10"
)->fetchAll(PDO::FETCH_ASSOC);

/* Specialist recommendation summary */
$topSpecialists = $pdo->query(
    "SELECT d.DoctorID, d.FName, d.Specialization,
            COUNT(a.AppID) AS TotalReferrals
     FROM appointments a
     JOIN doctors d ON d.DoctorID = a.DoctorID
     WHERE d.Specialization NOT IN ('General Practitioner','Family Medicine')
     GROUP BY d.DoctorID, d.FName, d.Specialization
     ORDER BY TotalReferrals DESC, d.DoctorID ASC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$mostRecommended = $topSpecialists[0] ?? null;

/* Top rated entities */
$topRatedDoctors = $pdo->query(
    "SELECT d.DoctorID, d.FName, d.Specialization,
            ROUND(AVG(r.RatingValue),1) AS AvgRating,
            COUNT(*) AS TotalRatings
     FROM ratings r
     JOIN doctors d ON r.EntityID = d.DoctorID
     WHERE r.EntityType = 'Doctor'
     GROUP BY d.DoctorID, d.FName, d.Specialization
     ORDER BY AvgRating DESC, TotalRatings DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$topRatedLabs = $pdo->query(
    "SELECT l.LabID, l.LabName,
            ROUND(AVG(r.RatingValue),1) AS AvgRating,
            COUNT(*) AS TotalRatings
     FROM ratings r
     JOIN laboratories l ON r.EntityID = l.LabID
     WHERE r.EntityType = 'Lab'
     GROUP BY l.LabID, l.LabName
     ORDER BY AvgRating DESC, TotalRatings DESC
     LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

// fetch lab metrics (total tests, completed, pending, last test date, avg turnaround) for each lab in date range
$stmtLabMetrics = $pdo->prepare(
    "SELECT lab.LabID, lab.LabName,
            COUNT(lt.LabTestID) AS TotalTests,
            SUM(CASE WHEN lt.Status = 'Completed' THEN 1 ELSE 0 END) AS Completed,
            SUM(CASE WHEN lt.Status <> 'Completed' OR lt.Status IS NULL THEN 1 ELSE 0 END) AS Pending,
            MAX(lt.TestDate) AS LastTestDate,
            AVG(
                CASE
                    WHEN lt.ResultDate IS NOT NULL AND lt.TestDate IS NOT NULL
                    THEN TIMESTAMPDIFF(HOUR, lt.TestDate, lt.ResultDate)
                    ELSE NULL
                END
            ) AS AvgTurnaroundHours
     FROM laboratories lab
     LEFT JOIN lab_tests lt
       ON lt.LabID = lab.LabID
      AND lt.TestDate BETWEEN :start AND :end
     GROUP BY lab.LabID, lab.LabName
     ORDER BY TotalTests DESC, lab.LabName ASC"
);
$stmtLabMetrics->execute([
    ':start' => $startDateTime,
    ':end' => $endDateTime
]);
$labMetrics = $stmtLabMetrics->fetchAll(PDO::FETCH_ASSOC);

// fetch chat messages count by role and daily totals in date range
$chatUnionSql = "
    SELECT SenderRole, MsgDate FROM messages
    UNION ALL
    SELECT SenderRole, MsgDate FROM caregiver_messages
    UNION ALL
    SELECT SenderRole, MsgDate FROM lab_test_messages
";

$stmtChatByRole = $pdo->prepare(
    "SELECT SenderRole, COUNT(*) AS TotalMessages
     FROM ($chatUnionSql) t
     WHERE t.MsgDate BETWEEN :start AND :end
     GROUP BY SenderRole
     ORDER BY TotalMessages DESC"
);
$stmtChatByRole->execute([
    ':start' => $startDateTime,
    ':end' => $endDateTime
]);
$chatByRole = $stmtChatByRole->fetchAll(PDO::FETCH_ASSOC);

$stmtChatDaily = $pdo->prepare(
    "SELECT DATE(t.MsgDate) AS ChatDate, COUNT(*) AS TotalMessages
     FROM ($chatUnionSql) t
     WHERE t.MsgDate BETWEEN :start AND :end
     GROUP BY DATE(t.MsgDate)
     ORDER BY ChatDate DESC"
);
$stmtChatDaily->execute([
    ':start' => $startDateTime,
    ':end' => $endDateTime
]);
$chatDaily = $stmtChatDaily->fetchAll(PDO::FETCH_ASSOC);

// fetch appointment counts by status, daily totals, and unassigned (no doctor/nurse) in date range
$stmtApptStatus = $pdo->prepare(
    "SELECT Status, COUNT(*) AS TotalAppointments
     FROM appointments
     WHERE AppointmentDate BETWEEN :start AND :end
     GROUP BY Status
     ORDER BY TotalAppointments DESC"
);
$stmtApptStatus->execute([
    ':start' => $startDateTime,
    ':end' => $endDateTime
]);
$apptStatus = $stmtApptStatus->fetchAll(PDO::FETCH_ASSOC);

$stmtApptDaily = $pdo->prepare(
    "SELECT DATE(AppointmentDate) AS ApptDate, COUNT(*) AS TotalAppointments
     FROM appointments
     WHERE AppointmentDate BETWEEN :start AND :end
     GROUP BY DATE(AppointmentDate)
     ORDER BY ApptDate DESC"
);
$stmtApptDaily->execute([
    ':start' => $startDateTime,
    ':end' => $endDateTime
]);
$apptDaily = $stmtApptDaily->fetchAll(PDO::FETCH_ASSOC);

$stmtApptAssignments = $pdo->prepare(
    "SELECT
        COUNT(*) AS TotalAppointments,
        SUM(CASE WHEN DoctorID IS NULL THEN 1 ELSE 0 END) AS NoDoctor,
        SUM(CASE WHEN NurseID IS NULL THEN 1 ELSE 0 END) AS NoNurse
     FROM appointments
     WHERE AppointmentDate BETWEEN :start AND :end"
);
$stmtApptAssignments->execute([
    ':start' => $startDateTime,
    ':end' => $endDateTime
]);
$apptAssignments = $stmtApptAssignments->fetch(PDO::FETCH_ASSOC) ?: [
    'TotalAppointments' => 0,
    'NoDoctor' => 0,
    'NoNurse' => 0
];

// Handle CSV export requests
$export = $_GET['export'] ?? '';
if ($export === 'lab_metrics') {
    $rows = [];
    foreach ($labMetrics as $r) {
        $rows[] = [
            'LabID' => $r['LabID'],
            'LabName' => $r['LabName'],
            'TotalTests' => $r['TotalTests'],
            'Completed' => $r['Completed'],
            'Pending' => $r['Pending'],
            'LastTestDate' => $r['LastTestDate'],
            'AvgTurnaroundHours' => $r['AvgTurnaroundHours']
        ];
    }
    outputCsv("lab_metrics_{$startDate}_{$endDate}.csv", ['LabID','LabName','TotalTests','Completed','Pending','LastTestDate','AvgTurnaroundHours'], $rows);
}
if ($export === 'chat_daily') {
    $rows = [];
    foreach ($chatDaily as $r) {
        $rows[] = [
            'ChatDate' => $r['ChatDate'],
            'TotalMessages' => $r['TotalMessages']
        ];
    }
    outputCsv("chat_daily_{$startDate}_{$endDate}.csv", ['ChatDate','TotalMessages'], $rows);
}
if ($export === 'chat_by_role') {
    $rows = [];
    foreach ($chatByRole as $r) {
        $rows[] = [
            'SenderRole' => $r['SenderRole'],
            'TotalMessages' => $r['TotalMessages']
        ];
    }
    outputCsv("chat_by_role_{$startDate}_{$endDate}.csv", ['SenderRole','TotalMessages'], $rows);
}
if ($export === 'appt_status') {
    $rows = [];
    foreach ($apptStatus as $r) {
        $rows[] = [
            'Status' => $r['Status'],
            'TotalAppointments' => $r['TotalAppointments']
        ];
    }
    outputCsv("appointments_status_{$startDate}_{$endDate}.csv", ['Status','TotalAppointments'], $rows);
}
if ($export === 'appt_daily') {
    $rows = [];
    foreach ($apptDaily as $r) {
        $rows[] = [
            'ApptDate' => $r['ApptDate'],
            'TotalAppointments' => $r['TotalAppointments']
        ];
    }
    outputCsv("appointments_daily_{$startDate}_{$endDate}.csv", ['ApptDate','TotalAppointments'], $rows);
}
?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="profile-container">

<h2>System Reports</h2>
<p>Login summary covers the last 7 days. Time usage covers the last 30 days.</p>

<h3>Report Filters</h3>
<form method="get" class="filter-form">
    <label>
        Start date (YYYY-MM-DD)
        <input type="text" name="start" value="<?= htmlspecialchars($startDate) ?>" placeholder="YYYY-MM-DD">
    </label>
    <label>
        End date (YYYY-MM-DD)
        <input type="text" name="end" value="<?= htmlspecialchars($endDate) ?>" placeholder="YYYY-MM-DD">
    </label>
    <button type="submit" class="btn btn-view">Apply</button>
</form>

<h3>Login Reports</h3>
<p>
    Total logins (7 days): <strong><?= (int)($loginSummary['TotalLogins'] ?? 0) ?></strong> |
    Unique users: <strong><?= (int)($loginSummary['UniqueUsers'] ?? 0) ?></strong> |
    Active sessions: <strong><?= $activeSessions ?></strong>
</p>

<table class="user-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Total Logins</th>
            <th>Unique Users</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($dailyLogins): ?>
        <?php
foreach ($dailyLogins as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['LogDate']) ?></td>
                <td><?= (int)$row['TotalLogins'] ?></td>
                <td><?= (int)$row['UniqueUsers'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="3">No login data found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Recent Logins</h3>
<table class="user-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Role</th>
            <th>Login Time</th>
            <th>Logout Time</th>
            <th>Session</th>
            <th>IP</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($recentLogins): ?>
        <?php
foreach ($recentLogins as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Uname'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($row['Role']) ?></td>
                <td><?= htmlspecialchars($row['LoginAt']) ?></td>
                <td><?= htmlspecialchars($row['LogoutAt'] ?? '-') ?></td>
                <td><?= formatMinutes($row['SessionMinutes']) ?></td>
                <td><?= htmlspecialchars($row['IPAddress'] ?? '-') ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="6">No login activity found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Time Usage Reports</h3>
<table class="user-table">
    <thead>
        <tr>
            <th>Role</th>
            <th>Total Time</th>
            <th>Average Session</th>
            <th>Sessions</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($usageByRole): ?>
        <?php
foreach ($usageByRole as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Role']) ?></td>
                <td><?= formatMinutes($row['TotalMinutes']) ?></td>
                <td><?= formatMinutes($row['AvgMinutes']) ?></td>
                <td><?= (int)$row['Sessions'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="4">No usage data found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Top Users by Time (30 days)</h3>
<table class="user-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Role</th>
            <th>Total Time</th>
            <th>Sessions</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($usageByUser): ?>
        <?php
foreach ($usageByUser as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Uname'] ?? 'Unknown User') ?></td>
                <td><?= htmlspecialchars($row['Role']) ?></td>
                <td><?= formatMinutes($row['TotalMinutes']) ?></td>
                <td><?= (int)$row['Sessions'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="4">No usage data found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Most Recommended Specialist</h3>
<?php
if ($mostRecommended): ?>
    <p>
        <strong><?= htmlspecialchars($mostRecommended['FName']) ?></strong>
        (<?= htmlspecialchars($mostRecommended['Specialization']) ?>) -
        <?= (int)$mostRecommended['TotalReferrals'] ?> referrals -
        <?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$mostRecommended['DoctorID']] ?? null)) ?>
    </p>
<?php
else: ?>
    <p>No specialist referral data found.</p>
<?php
endif; ?>

<table class="user-table">
    <thead>
        <tr>
            <th>Doctor</th>
            <th>Specialization</th>
            <th>Referrals</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($topSpecialists): ?>
        <?php
foreach ($topSpecialists as $row): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($row['FName']) ?><br>
                    <small><?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$row['DoctorID']] ?? null)) ?></small>
                </td>
                <td><?= htmlspecialchars($row['Specialization']) ?></td>
                <td><?= (int)$row['TotalReferrals'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="3">No specialist referral data found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Top 5 Highest Rated Doctors</h3>
<table class="user-table">
    <thead>
        <tr>
            <th>Doctor</th>
            <th>Specialization</th>
            <th>Average Rating</th>
            <th>Total Ratings</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($topRatedDoctors): ?>
        <?php
foreach ($topRatedDoctors as $row): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($row['FName']) ?><br>
                    <small><?= htmlspecialchars(formatEntityRatingLabel($doctorRatings[$row['DoctorID']] ?? null)) ?></small>
                </td>
                <td><?= htmlspecialchars($row['Specialization']) ?></td>
                <td><?= htmlspecialchars(formatEntityRatingLabel(['avg' => $row['AvgRating'], 'count' => $row['TotalRatings']])) ?></td>
                <td><?= (int)$row['TotalRatings'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="4">No doctor ratings found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Top 5 Highest Rated Laboratories</h3>
<table class="user-table">
    <thead>
        <tr>
            <th>Laboratory</th>
            <th>Average Rating</th>
            <th>Total Ratings</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($topRatedLabs): ?>
        <?php
foreach ($topRatedLabs as $row): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($row['LabName']) ?><br>
                    <small><?= htmlspecialchars(formatEntityRatingLabel($labRatings[$row['LabID']] ?? null)) ?></small>
                </td>
                <td><?= htmlspecialchars(formatEntityRatingLabel(['avg' => $row['AvgRating'], 'count' => $row['TotalRatings']])) ?></td>
                <td><?= (int)$row['TotalRatings'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="3">No laboratory ratings found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Lab Metrics (<?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?>)</h3>
<div class="section-spacing-sm">
    <a class="btn btn-view" href="admin_reports.php?start=<?= htmlspecialchars($startDate) ?>&end=<?= htmlspecialchars($endDate) ?>&export=lab_metrics">Export CSV</a>
</div>
<table class="user-table">
    <thead>
        <tr>
            <th>Lab</th>
            <th>Total Tests</th>
            <th>Completed</th>
            <th>Pending</th>
            <th>Last Test Date</th>
            <th>Avg Turnaround (hrs)</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($labMetrics): ?>
        <?php
foreach ($labMetrics as $row): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($row['LabName']) ?><br>
                    <small><?= htmlspecialchars(formatEntityRatingLabel($labRatings[$row['LabID']] ?? null)) ?></small>
                </td>
                <td><?= (int)$row['TotalTests'] ?></td>
                <td><?= (int)$row['Completed'] ?></td>
                <td><?= (int)$row['Pending'] ?></td>
                <td><?= htmlspecialchars(formatDateOrDash($row['LastTestDate'])) ?></td>
                <td><?= $row['AvgTurnaroundHours'] !== null ? number_format((float)$row['AvgTurnaroundHours'], 1) : '-' ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="6">No lab metrics found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Chat Activity (<?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?>)</h3>
<div class="section-spacing-sm">
    <a class="btn btn-view" href="admin_reports.php?start=<?= htmlspecialchars($startDate) ?>&end=<?= htmlspecialchars($endDate) ?>&export=chat_daily">Export Daily CSV</a>
    <a class="btn btn-view" href="admin_reports.php?start=<?= htmlspecialchars($startDate) ?>&end=<?= htmlspecialchars($endDate) ?>&export=chat_by_role">Export By Role CSV</a>
</div>

<table class="user-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Total Messages</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($chatDaily): ?>
        <?php
foreach ($chatDaily as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['ChatDate']) ?></td>
                <td><?= (int)$row['TotalMessages'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="2">No chat activity found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<table class="user-table">
    <thead>
        <tr>
            <th>Role</th>
            <th>Total Messages</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($chatByRole): ?>
        <?php
foreach ($chatByRole as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['SenderRole']) ?></td>
                <td><?= (int)$row['TotalMessages'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="2">No chat activity found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<h3>Appointment Stats (<?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?>)</h3>
<div class="section-spacing-sm">
    <a class="btn btn-view" href="admin_reports.php?start=<?= htmlspecialchars($startDate) ?>&end=<?= htmlspecialchars($endDate) ?>&export=appt_status">Export Status CSV</a>
    <a class="btn btn-view" href="admin_reports.php?start=<?= htmlspecialchars($startDate) ?>&end=<?= htmlspecialchars($endDate) ?>&export=appt_daily">Export Daily CSV</a>
</div>

<p class="dashboard-subtitle">
    Total: <strong><?= (int)$apptAssignments['TotalAppointments'] ?></strong> |
    No Doctor Assigned: <strong><?= (int)$apptAssignments['NoDoctor'] ?></strong> |
    No Nurse Assigned: <strong><?= (int)$apptAssignments['NoNurse'] ?></strong>
</p>

<table class="user-table">
    <thead>
        <tr>
            <th>Status</th>
            <th>Total Appointments</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($apptStatus): ?>
        <?php
foreach ($apptStatus as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['Status']) ?></td>
                <td><?= (int)$row['TotalAppointments'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="2">No appointment data found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

<table class="user-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Total Appointments</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($apptDaily): ?>
        <?php
foreach ($apptDaily as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['ApptDate']) ?></td>
                <td><?= (int)$row['TotalAppointments'] ?></td>
            </tr>
        <?php
endforeach; ?>
    <?php
else: ?>
        <tr><td colspan="2">No appointment data found.</td></tr>
    <?php
endif; ?>
    </tbody>
</table>

</div>
</main>

<?php
include "../footer.php"; ?>

