<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) {
    header("Location: signin_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_profile_enseignant'])) {
        $idProfileEnseignant = $_POST['id_profile_enseignant'];
        
        $conn = getDbConnection();

        $stmt = $conn->prepare("DELETE FROM profile_enseignant WHERE id_profile_enseignant = ?");
        $stmt->bind_param("i", $idProfileEnseignant);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Profil enseignant supprimé avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression du profil enseignant.";
        }

        $stmt->close();
        $conn->close();
    }
}

header("Location: mes-enseignants.php");
exit();
?>
