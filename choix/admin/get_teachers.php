<?php
require 'db_connect.php';

// Set JSON response header
header("Content-Type: application/json");

// Debugging: Log request data
error_log("Received Request: " . json_encode($_REQUEST));

// Validate academic year ID
if (!isset($_REQUEST['id_annee']) || !is_numeric($_REQUEST['id_annee'])) {
    echo json_encode(["error" => "Missing or invalid academic year ID."]);
    exit();
}

$id_annee = (int) $_REQUEST['id_annee'];
$conn = getDbConnection();

if (!$conn) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// Check if the academic year exists
$checkYearSql = "SELECT COUNT(*) as count FROM annees WHERE id_annee = ?";
$checkStmt = $conn->prepare($checkYearSql);
if (!$checkStmt) {
    echo json_encode(["error" => "SQL error checking academic year: " . $conn->error]);
    exit();
}
$checkStmt->bind_param("i", $id_annee);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count == 0) {
    echo json_encode(["error" => "Academic year not found."]);
    exit();
}

// Fetch teachers and group them by id_enseignant to avoid duplicates
$sql = "
    SELECT e.id_enseignant, e.firstName_enseignant, e.lastName_enseignant, 
           GROUP_CONCAT(DISTINCT pe.id_matiere) AS matieres, 
           GROUP_CONCAT(DISTINCT pe.id_matiere_commune) AS matieres_communes
    FROM profile_enseignant pe
    JOIN enseignants e ON pe.id_enseignant = e.id_enseignant
    WHERE pe.id_annee = ?
    GROUP BY e.id_enseignant";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["error" => "SQL query preparation failed: " . $conn->error]);
    exit();
}

$stmt->bind_param("i", $id_annee);
$stmt->execute();
$result = $stmt->get_result();

$teachers = [];
while ($row = $result->fetch_assoc()) {
    $row['matieres'] = $row['matieres'] ? explode(',', $row['matieres']) : [];
    $row['matieres_communes'] = $row['matieres_communes'] ? explode(',', $row['matieres_communes']) : [];
    $teachers[] = $row;
}

// Debugging logs
error_log("Teachers Found: " . json_encode($teachers));
error_log("Teachers Exist: " . (!empty($teachers) ? "Yes" : "No"));

// ✅ Instead of returning an error, return an empty array when no teachers are found
echo json_encode($teachers);

// Cleanup
$stmt->close();
$conn->close();
?>
