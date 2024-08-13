<?php
include 'db_connect.php';

header('Content-Type: application/json');

function assignMatiere($conn, $enseignantId, $matiereIds, $matiereCommuneIds) {
    $conn->begin_transaction();
    try {
        foreach ($matiereIds as $matiereId) {
            $stmt = $conn->prepare("INSERT INTO admins_enseignants_matieres (id_enseignant, id_matiere) VALUES (?, ?)");
            $stmt->bind_param("ii", $enseignantId, $matiereId);
            $stmt->execute();
            $stmt->close();
        }

        foreach ($matiereCommuneIds as $matiereCommuneId) {
            $stmt = $conn->prepare("INSERT INTO admins_enseignants_matieres (id_enseignant, id_matiere_commune) VALUES (?, ?)");
            $stmt->bind_param("ii", $enseignantId, $matiereCommuneId);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        return ['status' => 'success', 'enseignantId' => $enseignantId];
    } catch (Exception $e) {
        $conn->rollback();
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

$conn = getDbConnection();

$enseignantId = $_POST['enseignantId'];
$matiereIds = $_POST['matieres'] ?? [];
$matiereCommuneIds = $_POST['matieres_communes'] ?? [];

$response = assignMatiere($conn, $enseignantId, $matiereIds, $matiereCommuneIds);

echo json_encode($response);
$conn->close();
?>
 