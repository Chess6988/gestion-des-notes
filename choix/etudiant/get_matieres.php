<?php
require_once 'db.php';

if (isset($_POST['id_filiere']) && isset($_POST['id_niveau']) && isset($_POST['id_semestre'])) {
    $id_filiere = $_POST['id_filiere'];
    $id_niveau = $_POST['id_niveau'];
    $id_semestre = $_POST['id_semestre'];

    // Fetch matieres
    $stmt = $conn->prepare("SELECT id_matiere, libelle_matiere FROM matieres WHERE id_filiere = ? AND id_niveau = ? AND id_semestre = ?");
    $stmt->bind_param("iii", $id_filiere, $id_niveau, $id_semestre);
    $stmt->execute();
    $result = $stmt->get_result();
    $matieres = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['matieres' => $matieres]);
} else {
    echo json_encode(['matieres' => []]);
}
?>
