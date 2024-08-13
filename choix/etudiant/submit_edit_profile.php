<?php
session_start();
require_once 'db_connect.php';

// Debugging session variable
error_log("Session userId_etudiant at start: " . ($_SESSION['userId_etudiant'] ?? 'not set'));

// Verify if the student's ID is set in the session
if (!isset($_SESSION['userId_etudiant'])) {
    echo "Student ID is not set in session. Please create an account first.";
    exit();
}

$id_etudiant = $_SESSION['userId_etudiant'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_filiere = $_POST['filiere'];
    $id_niveau = $_POST['niveau'];
    $id_semestre = $_POST['semestre'];
    $id_annee = $_POST['annee'];
    $id_matieres = isset($_POST['matieres']) ? $_POST['matieres'] : [];
    $id_matieres_communes = isset($_POST['matieres_communes']) ? $_POST['matieres_communes'] : [];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update etudiants table
        $stmt = $conn->prepare("UPDATE etudiants SET id_filiere = ?, id_niveau = ? WHERE id_etudiant = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("iii", $id_filiere, $id_niveau, $id_etudiant);
        $stmt->execute();
        $stmt->close();

        // Delete old profile details
        $stmt = $conn->prepare("DELETE FROM profile_etudiant WHERE id_etudiant = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_etudiant);
        $stmt->execute();
        $stmt->close();

        // Insert into profile_etudiant
        $stmt_profile = $conn->prepare("INSERT INTO profile_etudiant (id_etudiant, id_filiere, id_semestre, id_niveau, id_matiere, id_matiere_commune, id_annee) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_profile) {
            throw new Exception($conn->error);
        }
        foreach ($id_matieres as $id_matiere) {
            foreach ($id_matieres_communes as $id_matiere_commune) {
                $stmt_profile->bind_param("iiiiiii", $id_etudiant, $id_filiere, $id_semestre, $id_niveau, $id_matiere, $id_matiere_commune, $id_annee);
                $stmt_profile->execute();
            }
        }
        $stmt_profile->close();

        // Update etudiants_annees
        $stmt = $conn->prepare("DELETE FROM etudiants_annees WHERE id_etudiant = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_etudiant);
        $stmt->execute();
        $stmt->close();

        $stmt_annee = $conn->prepare("INSERT INTO etudiants_annees (id_etudiant, id_annee) VALUES (?, ?)");
        if (!$stmt_annee) {
            throw new Exception($conn->error);
        }
        $stmt_annee->bind_param("ii", $id_etudiant, $id_annee);
        $stmt_annee->execute();
        $stmt_annee->close();

        // Update etudiants_semestres
        $stmt = $conn->prepare("DELETE FROM etudiants_semestres WHERE id_etudiant = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_etudiant);
        $stmt->execute();
        $stmt->close();

        $stmt_semestre = $conn->prepare("INSERT INTO etudiants_semestres (id_etudiant, id_semestre) VALUES (?, ?)");
        if (!$stmt_semestre) {
            throw new Exception($conn->error);
        }
        $stmt_semestre->bind_param("ii", $id_etudiant, $id_semestre);
        $stmt_semestre->execute();
        $stmt_semestre->close();

        // Update matieres_etudiants
        $stmt = $conn->prepare("DELETE FROM matieres_etudiants WHERE id_etudiant = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_etudiant);
        $stmt->execute();
        $stmt->close();

        $stmt_matiere = $conn->prepare("INSERT INTO matieres_etudiants (id_matiere, id_etudiant, id_annee) VALUES (?, ?, ?)");
        if (!$stmt_matiere) {
            throw new Exception($conn->error);
        }
        foreach ($id_matieres as $id_matiere) {
            $stmt_matiere->bind_param("iii", $id_matiere, $id_etudiant, $id_annee);
            $stmt_matiere->execute();
        }
        $stmt_matiere->close();

        // Update matieres_communes_etudiants
        $stmt = $conn->prepare("DELETE FROM matieres_communes_etudiants WHERE id_etudiant = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_etudiant);
        $stmt->execute();
        $stmt->close();

        $stmt_matiere_commune = $conn->prepare("INSERT INTO matieres_communes_etudiants (id_matiere_commune, id_etudiant, id_annee) VALUES (?, ?, ?)");
        if (!$stmt_matiere_commune) {
            throw new Exception($conn->error);
        }
        foreach ($id_matieres_communes as $id_matiere_commune) {
            $stmt_matiere_commune->bind_param("iii", $id_matiere_commune, $id_etudiant, $id_annee);
            $stmt_matiere_commune->execute();
        }
        $stmt_matiere_commune->close();

        // Update niveaux_matieres
        $stmt = $conn->prepare("DELETE FROM niveaux_matieres WHERE id_niveau = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_niveau);
        $stmt->execute();
        $stmt->close();

        $stmt_niveau_matiere = $conn->prepare("INSERT INTO niveaux_matieres (id_niveau, id_matiere) VALUES (?, ?)");
        if (!$stmt_niveau_matiere) {
            throw new Exception($conn->error);
        }
        foreach ($id_matieres as $id_matiere) {
            $stmt_niveau_matiere->bind_param("ii", $id_niveau, $id_matiere);
            $stmt_niveau_matiere->execute();
        }
        $stmt_niveau_matiere->close();

        // Update niveaux_semestres
        $stmt = $conn->prepare("DELETE FROM niveaux_semestres WHERE id_niveau = ?");
        if (!$stmt) {
            throw new Exception($conn->error);
        }
        $stmt->bind_param("i", $id_niveau);
        $stmt->execute();
        $stmt->close();

        $stmt_niveau_semestre = $conn->prepare("INSERT INTO niveaux_semestres (id_niveau, id_semestre) VALUES (?, ?)");
        if (!$stmt_niveau_semestre) {
            throw new Exception($conn->error);
        }
        $stmt_niveau_semestre->bind_param("ii", $id_niveau, $id_semestre);
        $stmt_niveau_semestre->execute();
        $stmt_niveau_semestre->close();

        // Update matieres table to set id_annee


  
        // Commit the transaction
        $conn->commit();

        // Redirect with success message
        header("Location: profile_completion.php?status=success");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        // Error message in modal
        echo "<script>$(document).ready(function() { $('#errorModal').modal('show'); });</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Profile updated successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    An error occurred while updating the profile. Please try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
