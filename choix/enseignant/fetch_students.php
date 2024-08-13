<?php
require 'db_connect.php';

ob_start(); // Start output buffering

$conn = getDbConnection();
$id_matiere = $_POST['id_matiere'] ?? null;
$id_matiere_commune = $_POST['id_matiere_commune'] ?? null;
$id_annee = $_POST['id_annee'] ?? null;

if ($id_matiere) {
    $stmt = $conn->prepare("
        SELECT e.id_etudiant, e.firstName_etudiant, e.lastName_etudiant, 
               n.cc_note, n.normal_note, 
               (n.cc_note * 0.3 + n.normal_note * 0.7) AS note_final,  -- Calculate note_final here
               IF(n.id_etudiant IS NOT NULL, 1, 0) AS duplicate 
        FROM matieres_etudiants me
        JOIN etudiants e ON me.id_etudiant = e.id_etudiant
        LEFT JOIN notes n ON e.id_etudiant = n.id_etudiant 
            AND n.id_matiere = ? 
            AND n.id_annee = ?
        WHERE me.id_matiere = ? AND me.id_annee = ?
    ");
    $stmt->bind_param("iiii", $id_matiere, $id_annee, $id_matiere, $id_annee);
} else {
    $stmt = $conn->prepare("
        SELECT e.id_etudiant, e.firstName_etudiant, e.lastName_etudiant, 
               n.cc_note, n.normal_note, 
               (n.cc_note * 0.3 + n.normal_note * 0.7) AS note_final,  -- Calculate note_final here
               IF(n.id_etudiant IS NOT NULL, 1, 0) AS duplicate 
        FROM matieres_communes_etudiants mce
        JOIN etudiants e ON mce.id_etudiant = e.id_etudiant
        LEFT JOIN notes n ON e.id_etudiant = n.id_etudiant 
            AND n.id_matiere_commune = ? 
            AND n.id_annee = ?
        WHERE mce.id_matiere_commune = ? AND mce.id_annee = ?
    ");
    $stmt->bind_param("iiii", $id_matiere_commune, $id_annee, $id_matiere_commune, $id_annee);
}

$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

ob_clean(); // Clean the output buffer
echo json_encode($students);

$stmt->close();
$conn->close();

ob_end_flush(); // End and flush the output buffer
 // End and flush the output buffer
?>
