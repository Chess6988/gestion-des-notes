<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filiere = $_POST['filiere'];
    $semestre = $_POST['semestre'];
    $niveau = $_POST['niveau'];

    // Debugging
    error_log("Filiere: $filiere, Semestre: $semestre, Niveau: $niveau");

    // Fetch matieres
    $stmt = $conn->prepare("SELECT id_matiere, nom_matiere, courseCode FROM matieres WHERE id_filiere = ? AND id_semestre = ? AND id_niveau = ?");
    $stmt->bind_param("iii", $filiere, $semestre, $niveau);
    $stmt->execute();
    $result = $stmt->get_result();
    $matieres = "";
    while ($row = $result->fetch_assoc()) {
        $matieres .= '<div class="form-check">
                        <input class="form-check-input" type="checkbox" name="matieres[]" value="' . $row['id_matiere'] . '" id="matiere' . $row['id_matiere'] . '">
                        <label class="form-check-label" for="matiere' . $row['id_matiere'] . '">' . $row['nom_matiere'] . ' (' . $row['courseCode'] . ')</label>
                      </div>';
    }
    $stmt->close();

    // Fetch matieres_communes
    $stmt = $conn->prepare("SELECT id_matiere_commune, nom_matiere_commune, courseCode FROM matieres_communes WHERE id_semestre = ? AND id_niveau = ?");
    $stmt->bind_param("ii", $semestre, $niveau);
    $stmt->execute();
    $result = $stmt->get_result();
    $matieres_communes = "";
    while ($row = $result->fetch_assoc()) {
        $matieres_communes .= '<div class="form-check">
                                <input class="form-check-input" type="checkbox" name="matieres_communes[]" value="' . $row['id_matiere_commune'] . '" id="matiere_commune' . $row['id_matiere_commune'] . '">
                                <label class="form-check-label" for="matiere_commune' . $row['id_matiere_commune'] . '">' . $row['nom_matiere_commune'] . ' (' . $row['courseCode'] . ')</label>
                              </div>';
    }
    $stmt->close();

    $response = array(
        "matieres" => $matieres,
        "matieres_communes" => $matieres_communes
    );

    echo json_encode($response);
}
?>
