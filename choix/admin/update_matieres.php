<?php
require 'db_connect.php';
$conn = getDbConnection();

header('Content-Type: application/json'); // Ensure JSON response

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_profile_enseignant']) && isset($_POST['new_matiere_id']) && isset($_POST['matiere_type'])) {
    $id_profile_enseignant = intval($_POST['id_profile_enseignant']);
    $new_matiere_id = intval($_POST['new_matiere_id']);
    $matiere_type = $_POST['matiere_type'];

    if ($matiere_type === 'matiere') {
        $sqlUpdate = "UPDATE profile_enseignant SET id_matiere = ? WHERE id_profile_enseignant = ?";
    } else {
        $sqlUpdate = "UPDATE profile_enseignant SET id_matiere_commune = ? WHERE id_profile_enseignant = ?";
    }

    $stmt = $conn->prepare($sqlUpdate);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'SQL preparation failed.']);
        exit();
    }

    $stmt->bind_param("ii", $new_matiere_id, $id_profile_enseignant);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update subject: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}
?>
