<?php
require 'db_connect.php';
$conn = getDbConnection();

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

// Debugging: Log received parameters
error_log("Received GET request: " . print_r($_GET, true));

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id_profile_enseignant']) && isset($_GET['matiere_type'])) {
    $id_profile_enseignant = intval($_GET['id_profile_enseignant']);
    $matiere_type = $_GET['matiere_type'];

    // Fetch existing assignment
    $sqlGetCurrent = "SELECT id_annee, id_matiere, id_matiere_commune FROM profile_enseignant WHERE id_profile_enseignant = ?";
    $stmt = $conn->prepare($sqlGetCurrent);
    $stmt->bind_param("i", $id_profile_enseignant);
    $stmt->execute();
    $currentData = $stmt->get_result()->fetch_assoc();

    if (!$currentData) {
        echo json_encode(['success' => false, 'error' => 'Profile not found. Check if id_profile_enseignant exists.']);
        exit();
    }

    $excludedId = ($matiere_type === 'matiere') ? $currentData['id_matiere'] : $currentData['id_matiere_commune'];

    $sqlSubjects = ($matiere_type === 'matiere') ? 
        "SELECT id_matiere AS id, nom_matiere AS name FROM matieres WHERE id_matiere != ? ORDER BY nom_matiere" :
        "SELECT id_matiere_commune AS id, nom_matiere_commune AS name FROM matieres_communes WHERE id_matiere_commune != ? ORDER BY nom_matiere_commune";

    $stmt = $conn->prepare($sqlSubjects);
    $stmt->bind_param("i", $excludedId);
    $stmt->execute();
    $subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'id_annee' => $currentData['id_annee'],
        'subjects' => $subjects
    ]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request. No valid id_profile_enseignant received.']);
}
?>
