<?php
require 'db_connect.php';
header("Content-Type: application/json");

// Validate teacher ID and academic year
if (!isset($_GET['id_enseignant']) || !isset($_GET['id_annee']) || !is_numeric($_GET['id_enseignant']) || !is_numeric($_GET['id_annee'])) {
    echo json_encode(["error" => "Invalid parameters."]);
    exit();
}

$id_enseignant = (int) $_GET['id_enseignant'];
$id_annee = (int) $_GET['id_annee'];

$conn = getDbConnection();
if (!$conn) {
    echo json_encode(["error" => "Database connection failed."]);
    exit();
}

// 🟢 Original Code Intact: Checking If the Academic Year Exists
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

// 🟢 Fetch Matières Assigned to the Teacher (Updated Query)
$sqlMatieres = "
    SELECT DISTINCT m.nom_matiere, m.courseCode 
    FROM profile_enseignant pe
    JOIN matieres m ON pe.id_matiere = m.id_matiere
    WHERE pe.id_enseignant = ? AND pe.id_annee = ?
";
$stmt1 = $conn->prepare($sqlMatieres);
if (!$stmt1) {
    echo json_encode(["error" => "SQL error fetching matieres: " . $conn->error]);
    exit();
}
$stmt1->bind_param("ii", $id_enseignant, $id_annee);
$stmt1->execute();
$result1 = $stmt1->get_result();
$matieres = $result1->fetch_all(MYSQLI_ASSOC);
$stmt1->close();

// 🟢 Fetch Matières Communes Assigned to the Teacher (Updated Query)
$sqlMatieresCommunes = "
    SELECT DISTINCT mc.nom_matiere_commune AS nom_matiere, mc.courseCode
    FROM profile_enseignant pe
    JOIN matieres_communes mc ON pe.id_matiere_commune = mc.id_matiere_commune
    WHERE pe.id_enseignant = ? AND pe.id_annee = ?
";
$stmt2 = $conn->prepare($sqlMatieresCommunes);
if (!$stmt2) {
    echo json_encode(["error" => "SQL error fetching matieres communes: " . $conn->error]);
    exit();
}
$stmt2->bind_param("ii", $id_enseignant, $id_annee);
$stmt2->execute();
$result2 = $stmt2->get_result();
$matieresCommunes = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();

$conn->close();

// ✅ Original Code Structure is Maintained
echo json_encode([
    "matieres" => $matieres,
    "matieresCommunes" => $matieresCommunes
]);
?>
