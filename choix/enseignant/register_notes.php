<?php
session_start();
require 'db_connect.php';
header('Content-Type: application/json');

ob_start(); // Start output buffering

$conn = getDbConnection();
$conn->begin_transaction();

try {
    $messages = [];
    for ($i = 0; $i < count($_POST['id_etudiant']); $i++) {
        $id_etudiant = $_POST['id_etudiant'][$i];
        $id_matiere = $_POST['id_matiere'][$i] ?: null;
        $id_matiere_commune = $_POST['id_matiere_commune'][$i] ?: null;
        $cc_note = $_POST['cc_note'][$i];
        $normal_note = $_POST['normal_note'][$i];
        $note_final = $cc_note * 0.3 + $normal_note * 0.7;
        $id_annee = $_POST['id_annee'][$i];

        // Validate cc_note and normal_note
        if ($cc_note < 0 || $cc_note > 20 || $normal_note < 0 || $normal_note > 20) {
            throw new Exception('Notes must be between 0 and 20.');
        }

        // Check if the note already exists
        $stmt_check = $conn->prepare("
            SELECT COUNT(*)
            FROM notes
            WHERE id_etudiant = ?
              AND (id_matiere = ? OR id_matiere_commune = ?)
              AND id_annee = ?
        ");
        $stmt_check->bind_param("iiii", $id_etudiant, $id_matiere, $id_matiere_commune, $id_annee);
        $stmt_check->execute();
        $stmt_check->bind_result($count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($count > 0) {
            // If a duplicate is found, prepare a specific message
            $messages[] = [
                'id_etudiant' => $id_etudiant,
                'text' => "Déjà attribué: Notes for ${cc_note} (CC) and ${normal_note} (Normal) were already saved.",
                'duplicate' => true,
                'cc_note' => $cc_note,
                'normal_note' => $normal_note
            ];
        } else {
            // Insert or update the notes
            $stmt = $conn->prepare("
                INSERT INTO notes (id_etudiant, id_matiere, id_matiere_commune, cc_note, normal_note, note_final, id_annee)
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                cc_note = VALUES(cc_note), normal_note = VALUES(normal_note), note_final = VALUES(note_final)
            ");
            $stmt->bind_param("iiiiiii", $id_etudiant, $id_matiere, $id_matiere_commune, $cc_note, $normal_note, $note_final, $id_annee);
            $stmt->execute();
            $stmt->close();

            // Prepare a success message
            $messages[] = [
                'id_etudiant' => $id_etudiant,
                'text' => "Notes for ${cc_note} (CC) and ${normal_note} (Normal) have been registered.",
                'duplicate' => false,
                'cc_note' => $cc_note,
                'normal_note' => $normal_note,
                'note_final' => $note_final
            ];
        }
    }

    $conn->commit();
    ob_clean(); // Clean the output buffer
    echo json_encode(['status' => 'success', 'messages' => $messages]);
} catch (Exception $e) {
    $conn->rollback();
    ob_clean(); // Clean the output buffer
    echo json_encode(['status' => 'error', 'message' => 'Failed to register notes']);
} finally {
    $conn->close();
}

ob_end_flush(); // End and flush the output buffer
?>