<?php
session_start();
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_enseignant = $_SESSION['id_enseignant'];
    $id_annee = $_POST['id_annee_hidden']; // Corrected field name
    $matieres = $_POST['matieres'] ?? [];
    $matieres_communes = $_POST['matieres_communes'] ?? [];

    file_put_contents('log.txt', "Received data: id_enseignant=$id_enseignant, id_annee=$id_annee, matieres=" . json_encode($matieres) . ", matieres_communes=" . json_encode($matieres_communes) . "\n", FILE_APPEND);

    $conn = getDbConnection();

    $conn->begin_transaction();

    try {
        // Insert selected matieres
        foreach ($matieres as $id_matiere) {
            $stmt = $conn->prepare("
                INSERT INTO profile_enseignant (id_enseignant, id_annee, id_matiere, validated, new_entry)
                VALUES (?, ?, ?, 0, 1)
            ");
            $stmt->bind_param("iii", $id_enseignant, $id_annee, $id_matiere);
            $stmt->execute();
            file_put_contents('log.txt', "Inserted matiere: $id_matiere\n", FILE_APPEND);
        }

        // Insert selected matieres_communes
        foreach ($matieres_communes as $id_matiere_commune) {
            $stmt = $conn->prepare("
                INSERT INTO profile_enseignant (id_enseignant, id_annee, id_matiere_commune, validated, new_entry)
                VALUES (?, ?, ?, 0, 1)
            ");
            $stmt->bind_param("iii", $id_enseignant, $id_annee, $id_matiere_commune);
            $stmt->execute();
            file_put_contents('log.txt', "Inserted matiere_commune: $id_matiere_commune\n", FILE_APPEND);
        }

        $conn->commit();
        $_SESSION['success_message'] = "Subjects registered successfully.";
        echo json_encode(['status' => 'success', 'message' => 'Subjects registered successfully.']);
    } catch (Exception $e) {
        $conn->rollback();
        file_put_contents('log.txt', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Failed to register subjects.']);
    } finally {
        $conn->close();
    }
}
?>
