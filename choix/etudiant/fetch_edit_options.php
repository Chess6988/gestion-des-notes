<?php
require_once 'db_connect.php';

$filiere = $_POST['filiere'];
$semestre = $_POST['semestre'];
$niveau = $_POST['niveau'];

$response = array();

$matieres_query = "SELECT id_matiere, nom_matiere FROM matieres WHERE id_filiere = $filiere AND id_semestre = $semestre AND id_niveau = $niveau";
$matieres_result = $conn->query($matieres_query);

$matieres_html = "<label for='matieres'>Matières</label><div class='form-group'>";
while ($row = $matieres_result->fetch_assoc()) {
    $matieres_html .= "<div class='form-check'>
        <input class='form-check-input' type='checkbox' name='matieres[]' value='{$row['id_matiere']}' id='matiere{$row['id_matiere']}'>
        <label class='form-check-label' for='matiere{$row['id_matiere']}'>{$row['nom_matiere']}</label>
    </div>";
}
$matieres_html .= "</div>";

$matieres_communes_query = "SELECT id_matiere_commune, nom_matiere_commune FROM matieres_communes WHERE id_semestre = $semestre AND id_niveau = $niveau";
$matieres_communes_result = $conn->query($matieres_communes_query);

$matieres_communes_html = "<label for='matieres_communes'>Matières Communes</label><div class='form-group'>";
while ($row = $matieres_communes_result->fetch_assoc()) {
    $matieres_communes_html .= "<div class='form-check'>
        <input class='form-check-input' type='checkbox' name='matieres_communes[]' value='{$row['id_matiere_commune']}' id='matiere_commune{$row['id_matiere_commune']}'>
        <label class='form-check-label' for='matiere_commune{$row['id_matiere_commune']}'>{$row['nom_matiere_commune']}</label>
    </div>";
}
$matieres_communes_html .= "</div>";

$response['matieres'] = $matieres_html;
$response['matieres_communes'] = $matieres_communes_html;

echo json_encode($response);
?>
