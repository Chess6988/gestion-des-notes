<?php
session_start(); // Start the session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_des_notes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['userId_etudiant'])) {
        $id_etudiant = $_SESSION['userId_etudiant']; // Get student ID from session
        $id_filiere = $_POST['filiere'];
        $id_niveau = $_POST['niveau'];
        $id_annee = $_POST['annee'];
        $id_semestre = $_POST['semestre'];
        $id_matieres = $_POST['matieres']; // Assume this is an array
        $id_matieres_communes = $_POST['matieres_communes']; // Assume this is an array

        // Start transaction
        $conn->begin_transaction();

        try {
            // Update etudiants table
            $stmt = $conn->prepare("UPDATE etudiants SET id_filiere = ?, id_niveau = ? WHERE id_etudiant = ?");
            $stmt->bind_param("iii", $id_filiere, $id_niveau, $id_etudiant);
            $stmt->execute();
            $stmt->close();

            // Insert into profile_etudiant
            $stmt_profile = $conn->prepare("INSERT INTO profile_etudiant (id_etudiant, id_filiere, id_semestre, id_niveau, id_matiere, id_matiere_commune, id_annee) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($id_matieres as $id_matiere) {
                foreach ($id_matieres_communes as $id_matiere_commune) {
                    $stmt_profile->bind_param("iiiiiii", $id_etudiant, $id_filiere, $id_semestre, $id_niveau, $id_matiere, $id_matiere_commune, $id_annee);
                    $stmt_profile->execute();
                }
            }
            $stmt_profile->close();

            // Insert into etudiants_annees
            $stmt_annee = $conn->prepare("INSERT INTO etudiants_annees (id_etudiant, id_annee) VALUES (?, ?)");
            $stmt_annee->bind_param("ii", $id_etudiant, $id_annee);
            $stmt_annee->execute();
            $stmt_annee->close();

            // Insert into etudiants_semestres
            $stmt_semestre = $conn->prepare("INSERT INTO etudiants_semestres (id_etudiant, id_semestre) VALUES (?, ?)");
            $stmt_semestre->bind_param("ii", $id_etudiant, $id_semestre);
            $stmt_semestre->execute();
            $stmt_semestre->close();

            // Insert into matieres_etudiants
            $stmt_matiere = $conn->prepare("INSERT INTO matieres_etudiants (id_matiere, id_etudiant, id_annee) VALUES (?, ?, ?)");
            foreach ($id_matieres as $id_matiere) {
                $stmt_matiere->bind_param("iii", $id_matiere, $id_etudiant, $id_annee);
                $stmt_matiere->execute();
            }
            $stmt_matiere->close();

            // Insert into matieres_communes_etudiants
            $stmt_matiere_commune = $conn->prepare("INSERT INTO matieres_communes_etudiants (id_matiere_commune, id_etudiant, id_annee) VALUES (?, ?, ?)");
            foreach ($id_matieres_communes as $id_matiere_commune) {
                $stmt_matiere_commune->bind_param("iii", $id_matiere_commune, $id_etudiant, $id_annee);
                $stmt_matiere_commune->execute();
            }
            $stmt_matiere_commune->close();

            // Insert into niveaux_matieres
            $stmt_niveau_matiere = $conn->prepare("INSERT INTO niveaux_matieres (id_niveau, id_matiere) VALUES (?, ?)");
            foreach ($id_matieres as $id_matiere) {
                $stmt_niveau_matiere->bind_param("ii", $id_niveau, $id_matiere);
                $stmt_niveau_matiere->execute();
            }
            $stmt_niveau_matiere->close();

            // Insert into niveaux_semestres
            $stmt_niveau_semestre = $conn->prepare("INSERT INTO niveaux_semestres (id_niveau, id_semestre) VALUES (?, ?)");
            $stmt_niveau_semestre->bind_param("ii", $id_niveau, $id_semestre);
            $stmt_niveau_semestre->execute();
            $stmt_niveau_semestre->close();

            // Commit transaction
            $conn->commit();

            // Redirect back to profile_completion.php with a success message
            header("Location: profile_completion.php?status=success");
            exit();
        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "User not logged in";
    }
}

$conn->close();
?>
