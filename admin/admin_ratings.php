<?php
// File overview: Handles admin ratings functionality.
session_start();
require "../db.php";
require "../functions.php";

// Access control: Only allow admins
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Get filter parametersfrom query string
$filterType = $_GET['entity_type'] ?? '';
$filterID   = $_GET['entity_id'] ?? '';
$allowedEntityTypes = ['Doctor', 'Lab'];
if ($filterType && !in_array($filterType, $allowedEntityTypes, true)) {
    $filterType = '';
    $filterID = '';
}

// Fetch entity lists for dropdowns and build name maps for display.
$entityLists = [
    'Doctor' => $pdo->query("SELECT DoctorID, FName FROM doctors ORDER BY FName")->fetchAll(PDO::FETCH_ASSOC),
    'Lab' => $pdo->query("SELECT LabID, LabName FROM laboratories ORDER BY LabName")->fetchAll(PDO::FETCH_ASSOC)
];
$doctorRatings = getEntityRatingsMap($pdo, 'Doctor');
$labRatings = getEntityRatingsMap($pdo, 'Lab');
foreach ($entityLists['Doctor'] as &$doctorRow) {
    $doctorRow['FName'] .= " - Rating: " . formatEntityRatingLabel($doctorRatings[$doctorRow['DoctorID']] ?? null);
}
unset($doctorRow);
foreach ($entityLists['Lab'] as &$labRow) {
    $labRow['LabName'] .= " - Rating: " . formatEntityRatingLabel($labRatings[$labRow['LabID']] ?? null);
}
unset($labRow);

// Build a name lookup for display (avoid showing raw IDs).
$entityNameMap = [
    'Doctor' => [],
    'Lab' => []
];
foreach ($entityLists as $type => $rows) {
    foreach ($rows as $row) {
        $keys = array_keys($row);
        if (count($keys) >= 2) {
            $entityId = $row[$keys[0]];
            $entityNameMap[$type][$entityId] = $row[$keys[1]];
        }
    }
}

// fetch all ratings with optional filters
$sql = "SELECT r.RatingID, r.UserID, u.Uname, r.EntityType, r.EntityID, r.RatingValue, r.RatingDate
        FROM ratings r
        JOIN users u ON r.UserID = u.UserID
        WHERE r.EntityType IN ('Doctor', 'Lab')";

$params = [];

if ($filterType) {
    $sql .= " AND r.EntityType = :type";
    $params[':type'] = $filterType;
}

if ($filterID) {
    $sql .= " AND r.EntityID = :id";
    $params[':id'] = $filterID;
}

$sql .= " ORDER BY r.RatingDate DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$allRatings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch overall ratings (average and count) with same filters
$sqlAvg = "SELECT EntityType, EntityID, ROUND(AVG(RatingValue),1) AS AvgRating, COUNT(*) AS TotalRatings
           FROM ratings
           WHERE EntityType IN ('Doctor', 'Lab')";

if ($filterType) {
    $sqlAvg .= " AND EntityType = :type";
}

if ($filterID) {
    $sqlAvg .= " AND EntityID = :id";
}

$sqlAvg .= " GROUP BY EntityType, EntityID ORDER BY EntityType, AvgRating DESC";

$stmtAvg = $pdo->prepare($sqlAvg);
$stmtAvg->execute($params);
$overallRatings = $stmtAvg->fetchAll(PDO::FETCH_ASSOC);

?>

<?php
include "../header.php"; ?>

<main class="main-content">
<div class="dashboard-container">

    <h2>Ratings Overview</h2>

    <!-- FILTER FORM -->
    <form method="get" class="filter-form">
        <select name="entity_type" id="entity_type" onchange="updateEntityDropdown()">
            <option value="">All Types</option>
            <?php
foreach ($allowedEntityTypes as $type): ?>
                <option value="<?= $type ?>" <?= $filterType==$type?'selected':'' ?>><?= $type ?></option>
            <?php
endforeach; ?>
        </select>

        <select name="entity_id" id="entity_id">
            <option value="">All Entities</option>
            <?php
            if ($filterType && isset($entityLists[$filterType])) {
                foreach ($entityLists[$filterType] as $ent) {
                    $selected = ($filterID == $ent[array_keys($ent)[0]]) ? 'selected' : '';
                    $name = $ent[array_keys($ent)[1]];
                    echo "<option value='{$ent[array_keys($ent)[0]]}' $selected>$name</option>";
                }
            }
            ?>
        </select>

        <button type="submit" class="btn btn-view">Filter</button>
        <a href="admin_ratings.php" class="btn btn-view">Reset</a>
    </form>

    <!-- ALL RATINGS TABLE -->
    <h3>All Individual Ratings</h3>
    <table class="user-table" id="allRatingsTable">
        <thead>
            <tr>
                <th onclick="sortTable(0,'allRatingsTable')">Rating ID</th>
                <th onclick="sortTable(1,'allRatingsTable')">Rated By (User)</th>
                <th onclick="sortTable(2,'allRatingsTable')">Entity Type</th>
                <th onclick="sortTable(3,'allRatingsTable')">Entity</th>
                <th onclick="sortTable(4,'allRatingsTable')">Rating Value</th>
                <th onclick="sortTable(5,'allRatingsTable')">Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
if ($allRatings): ?>
            <?php
foreach ($allRatings as $r): ?>
                <tr>
                    <td><?= $r['RatingID'] ?></td>
                    <td><?= htmlspecialchars($r['Uname']) ?></td>
                    <td><?= $r['EntityType'] ?></td>
                    <td>
                        <?php
                        $link = "#";
                        switch ($r['EntityType']) {
                            case 'Doctor': $link = "admin_view_users.php?id=".$r['EntityID']; break;
                            case 'Lab': $link = "admin_manage_labs.php#lab-".$r['EntityID']; break;
                        }
                        $entityName = $entityNameMap[$r['EntityType']][$r['EntityID']] ?? 'Unknown';
                        ?>
                        <a href="<?= $link ?>"><?= htmlspecialchars($entityName) ?></a>
                    </td>
                    <td><?= $r['RatingValue'] ?></td>
                    <td><?= $r['RatingDate'] ?></td>
                </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="6">No ratings found.</td></tr>
        <?php
endif; ?>
        </tbody>
    </table>

    <br>

    <!-- OVERALL RATINGS TABLE -->
    <h3>Overall Ratings per Entity</h3>
    <table class="user-table" id="overallRatingsTable">
        <thead>
            <tr>
                <th onclick="sortTable(0,'overallRatingsTable')">Entity Type</th>
                <th onclick="sortTable(1,'overallRatingsTable')">Entity</th>
                <th onclick="sortTable(2,'overallRatingsTable')">Average Rating</th>
                <th onclick="sortTable(3,'overallRatingsTable')">Total Ratings</th>
            </tr>
        </thead>
        <tbody>
        <?php
if ($overallRatings): ?>
            <?php
foreach ($overallRatings as $o): ?>
                <tr>
                    <td><?= $o['EntityType'] ?></td>
                    <td>
                        <?php
                        $link = "#";
                        switch ($o['EntityType']) {
                            case 'Doctor': $link = "admin_view_users.php?id=".$o['EntityID']; break;
                            case 'Lab': $link = "admin_manage_labs.php#lab-".$o['EntityID']; break;
                        }
                        $entityName = $entityNameMap[$o['EntityType']][$o['EntityID']] ?? 'Unknown';
                        ?>
                        <a href="<?= $link ?>"><?= htmlspecialchars($entityName) ?></a>
                    </td>
                    <td><?= $o['AvgRating'] ?></td>
                    <td><?= $o['TotalRatings'] ?></td>
                </tr>
            <?php
endforeach; ?>
        <?php
else: ?>
            <tr><td colspan="4">No ratings found.</td></tr>
        <?php
endif; ?>
        </tbody>
    </table>

   

</div>
</main>

<script>

// DYNAMIC ENTITY DROPDOWN

const entityLists = <?php
echo json_encode($entityLists); ?>;

function updateEntityDropdown() {
    const typeSelect = document.getElementById('entity_type');
    const entitySelect = document.getElementById('entity_id');
    const type = typeSelect.value;

    entitySelect.innerHTML = '<option value="">All Entities</option>';

    if (type && entityLists[type]) {
        entityLists[type].forEach(ent => {
            const id = ent[Object.keys(ent)[0]];
            const name = ent[Object.keys(ent)[1]];
            entitySelect.innerHTML += `<option value="${id}">${name}</option>`;
        });
    }
}


// SIMPLE TABLE SORTING

function sortTable(n, tableId) {
    const table = document.getElementById(tableId);
    let rows = Array.from(table.rows).slice(1);
    let asc = table.asc = !table.asc;

    rows.sort((a, b) => {
        let x = a.cells[n].innerText.toLowerCase();
        let y = b.cells[n].innerText.toLowerCase();
        return x === y ? 0 : (x > y ? 1 : -1) * (asc ? 1 : -1);
    });

    rows.forEach(row => table.tBodies[0].appendChild(row));
}
</script>

<?php
include "../footer.php"; ?>




