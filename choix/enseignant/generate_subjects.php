<?php
// Database connection
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_annee = $_POST['id_annee'];

    // Fetch matieres based on id_annee
    $stmt = $conn->prepare("
        SELECT DISTINCT m.id_matiere, m.nom_matiere
        FROM matieres m
        JOIN matieres_etudiants me ON m.id_matiere = me.id_matiere
        WHERE me.id_annee = ?
    ");
    $stmt->bind_param("i", $id_annee);
    $stmt->execute();
    $result = $stmt->get_result();
    $matieres = [];
    while ($row = $result->fetch_assoc()) {
        $matieres[] = $row;
    }

    // Fetch matieres_communes based on id_annee
    $stmt = $conn->prepare("
        SELECT DISTINCT mc.id_matiere_commune, mc.nom_matiere_commune
        FROM matieres_communes mc
        JOIN matieres_communes_etudiants mce ON mc.id_matiere_commune = mce.id_matiere_commune
        WHERE mce.id_annee = ?
    ");
    $stmt->bind_param("i", $id_annee);
    $stmt->execute();
    $result = $stmt->get_result();
    $matieres_communes = [];
    while ($row = $result->fetch_assoc()) {
        $matieres_communes[] = $row;
    }

    // Return the fetched data as JSON
    echo json_encode([
        'matieres' => $matieres,
        'matieres_communes' => $matieres_communes
    ]);
}
?> 