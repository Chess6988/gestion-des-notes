<?php
session_start();

if (!isset($_SESSION['firstName_enseignant']) || !isset($_SESSION['lastName_enseignant'])) {
    header("Location: signin_enseignant.php");
    exit();
}

require 'db_connect.php';

function saveProfileEnseignant($conn, $id_enseignant, $id_annee, $id_matiere = null, $id_matiere_commune = null) {
    $sql = "INSERT INTO profile_enseignant (id_enseignant, id_annee, id_matiere, id_matiere_commune) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("iiii", $id_enseignant, $id_annee, $id_matiere, $id_matiere_commune);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
}

function linkEnseignantAnnee($conn, $id_enseignant, $id_annee) {
    $sql = "INSERT INTO enseignants_annees (id_enseignant, id_annee) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ii", $id_enseignant, $id_annee);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_enseignant = $_SESSION['id_enseignant'];
    $id_annee = $_SESSION['id_annee'];

    $conn = getDbConnection();

    // Link enseignant with the selected annee
    linkEnseignantAnnee($conn, $id_enseignant, $id_annee);

    // Insert selected matieres and/or matieres_communes into profile_enseignant
    if (!empty($_POST['matieres'])) {
        foreach ($_POST['matieres'] as $id_matiere) {
            saveProfileEnseignant($conn, $id_enseignant, $id_annee, $id_matiere, null);
        }
    }

    if (!empty($_POST['matieres_communes'])) {
        foreach ($_POST['matieres_communes'] as $id_matiere_commune) {
            saveProfileEnseignant($conn, $id_enseignant, $id_annee, null, $id_matiere_commune);
        }
    }

    $conn->close();

    // Set success message in session
    $_SESSION['success_message'] = "Subjects registered successfully!";
    header("Location: wait_for_validation.php");
    exit();
}
?>
