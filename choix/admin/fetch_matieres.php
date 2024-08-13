<?php
include 'db_connect.php';

header('Content-Type: application/json');

function fetchMatieres($conn, $anneeId) {
    $stmt = $conn->prepare("
        SELECT DISTINCT m.id_matiere, m.nom 
        FROM matieres m
        JOIN profile_etudiant pe ON pe.id_matiere = m.id_matiere
        WHERE pe.id_annee = ?
    ");
    $stmt->bind_param("i", $anneeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $matieres = '';
    while ($row = $result->fetch_assoc()) {
        $matieres .= '<div class="form-check">
                        <input class="form-check-input" type="checkbox" name="matieres[]" value="' . $row['id_matiere'] . '" id="matiere' . $row['id_matiere'] . '">
                        <label class="form-check-label" for="matiere' . $row['id_matiere'] . '">' . $row['nom'] . '</label>
                      </div>';
    }
    $stmt->close();
    return $matieres;
}

function fetchMatieresCommunes($conn, $anneeId) {
    $stmt = $conn->prepare("
        SELECT DISTINCT mc.id_matiere_commune, mc.nom 
        FROM matieres_communes mc
        JOIN profile_etudiant pe ON pe.id_matiere_commune = mc.id_matiere_commune
        WHERE pe.id_annee = ?
    ");
    $stmt->bind_param("i", $anneeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $matieres_communes = '';
    while ($row = $result->fetch_assoc()) {
        $matieres_communes .= '<div class="form-check">
                                <input class="form-check-input" type="checkbox" name="matieres_communes[]" value="' . $row['id_matiere_commune'] . '" id="matiere_commune' . $row['id_matiere_commune'] . '">
                                <label class="form-check-label" for="matiere_commune' . $row['id_matiere_commune'] . '">' . $row['nom'] . '</label>
                              </div>';
    }
    $stmt->close();
    return $matieres_communes;
}

$conn = getDbConnection();
$anneeId = $_POST['anneeId'];

$response = [
    'status' => 'error',
    'message' => 'No data found',
    'matieres' => '',
    'matieres_communes' => ''
];

if (isset($anneeId) && !empty($anneeId)) {
    $response['matieres'] = fetchMatieres($conn, $anneeId);
    $response['matieres_communes'] = fetchMatieresCommunes($conn, $anneeId);

    if (!empty($response['matieres']) || !empty($response['matieres_communes'])) {
        $response['status'] = 'success';
        $response['message'] = 'Data fetched successfully';
    }
}

echo json_encode($response);
$conn->close();
?>
