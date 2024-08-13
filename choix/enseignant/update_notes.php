<?php
session_start();
require 'db_connect.php';
header('Content-Type: application/json');

// Start output buffering
ob_start();

$conn = getDbConnection();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    if (
        !isset($_POST['id_etudiant'], $_POST['cc_note'], $_POST['normal_note'], $_POST['id_annee']) ||
        !is_array($_POST['id_etudiant']) || !is_array($_POST['cc_note']) || !is_array($_POST['normal_note']) || !is_array($_POST['id_annee'])
    ) {
        throw new Exception('Invalid input data.');
    }

    $id_etudiants = $_POST['id_etudiant'];
    $id_matieres = isset($_POST['id_matiere']) ? $_POST['id_matiere'] : [];
    $id_matieres_communes = isset($_POST['id_matiere_commune']) ? $_POST['id_matiere_commune'] : [];
    $cc_notes = $_POST['cc_note'];
    $normal_notes = $_POST['normal_note'];
    $id_annees = $_POST['id_annee'];

    $stmt = $conn->prepare("UPDATE notes SET cc_note = ?, normal_note = ?, note_final = ? 
                            WHERE id_etudiant = ? AND id_annee = ? AND 
                            (id_matiere = IFNULL(?, id_matiere) OR id_matiere_commune = IFNULL(?, id_matiere_commune))");
    if (!$stmt) {
        throw new Exception('Failed to prepare SQL statement: ' . $conn->error);
    }

    $messages = [];
    for ($i = 0; $i < count($id_etudiants); $i++) {
        $id_etudiant = $id_etudiants[$i];
        $cc_note = $cc_notes[$i];
        $normal_note = $normal_notes[$i];
        $id_matiere = !empty($id_matieres[$i]) ? $id_matieres[$i] : null;
        $id_matiere_commune = !empty($id_matieres_communes[$i]) ? $id_matieres_communes[$i] : null;
        $id_annee = $id_annees[$i];
        $note_final = ($cc_note * 0.3 + $normal_note * 0.7);

        // Validate cc_note and normal_note
        if ($cc_note < 0 || $cc_note > 20 || $normal_note < 0 || $normal_note > 20) {
            throw new Exception('Notes must be between 0 and 20.');
        }

        if (!$stmt->bind_param('dddiiss', $cc_note, $normal_note, $note_final, $id_etudiant, $id_annee, $id_matiere, $id_matiere_commune)) {
            throw new Exception('Failed to bind parameters: ' . $stmt->error);
        }
        if ($stmt->execute()) {
            $messages[] = [
                'id_etudiant' => $id_etudiant,
                'cc_note' => $cc_note,
                'normal_note' => $normal_note,
                'note_final' => $note_final,
                'text' => 'Notes updated successfully', // This message is sent back to the front end
            ];
        } else {
            throw new Exception('Failed to execute SQL query: ' . $stmt->error);
        }
    }

    // Clean the output buffer before echoing JSON
    ob_clean();
    echo json_encode(['status' => 'success', 'messages' => $messages]);
} catch (Exception $e) {
    // Clean the output buffer before echoing JSON
    ob_clean();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
ob_end_flush();
?>