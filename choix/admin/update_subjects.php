<?php
// update_subjects.php
require 'db_connect.php';

if(isset($_POST['id_annee']) && isset($_POST['id_enseignant'])){
  $id_annee = $_POST['id_annee'];
  $id_enseignant = $_POST['id_enseignant'];
  // Get arrays of selected subjects; default to empty arrays if not set
  $selectedMatieres = isset($_POST['matieres']) ? $_POST['matieres'] : array();
  $selectedMatieresComm = isset($_POST['matieres_communes']) ? $_POST['matieres_communes'] : array();
  
  $conn = getDbConnection();
  
  // Fetch currently registered Matières for this teacher and year
  $currentMatieres = array();
  $sql = "SELECT id_matiere FROM profile_enseignant WHERE id_enseignant = ? AND id_annee = ? AND id_matiere IS NOT NULL";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $id_enseignant, $id_annee);
  $stmt->execute();
  $result = $stmt->get_result();
  while($row = $result->fetch_assoc()){
    $currentMatieres[] = $row['id_matiere'];
  }
  $stmt->close();
  
  // Fetch currently registered Matières Communes
  $currentMatieresComm = array();
  $sql = "SELECT id_matiere_commune FROM profile_enseignant WHERE id_enseignant = ? AND id_annee = ? AND id_matiere_commune IS NOT NULL";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $id_enseignant, $id_annee);
  $stmt->execute();
  $result = $stmt->get_result();
  while($row = $result->fetch_assoc()){
    $currentMatieresComm[] = $row['id_matiere_commune'];
  }
  $stmt->close();
  
  // Determine additions and removals for Matières
  $toAddMatieres = array_diff($selectedMatieres, $currentMatieres);
  $toRemoveMatieres = array_diff($currentMatieres, $selectedMatieres);
  
  // Determine additions and removals for Matières Communes
  $toAddMatieresComm = array_diff($selectedMatieresComm, $currentMatieresComm);
  $toRemoveMatieresComm = array_diff($currentMatieresComm, $selectedMatieresComm);
  
  // Insert new Matières
  foreach($toAddMatieres as $id_matiere){
    $stmt = $conn->prepare("INSERT INTO profile_enseignant (id_enseignant, id_annee, id_matiere) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_enseignant, $id_annee, $id_matiere);
    $stmt->execute();
    $stmt->close();
  }
  
  // Delete removed Matières
  foreach($toRemoveMatieres as $id_matiere){
    $stmt = $conn->prepare("DELETE FROM profile_enseignant WHERE id_enseignant = ? AND id_annee = ? AND id_matiere = ?");
    $stmt->bind_param("iii", $id_enseignant, $id_annee, $id_matiere);
    $stmt->execute();
    $stmt->close();
  }
  
  // Insert new Matières Communes
  foreach($toAddMatieresComm as $id_matiere_commune){
    $stmt = $conn->prepare("INSERT INTO profile_enseignant (id_enseignant, id_annee, id_matiere_commune) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_enseignant, $id_annee, $id_matiere_commune);
    $stmt->execute();
    $stmt->close();
  }
  
  // Delete removed Matières Communes
  foreach($toRemoveMatieresComm as $id_matiere_commune){
    $stmt = $conn->prepare("DELETE FROM profile_enseignant WHERE id_enseignant = ? AND id_annee = ? AND id_matiere_commune = ?");
    $stmt->bind_param("iii", $id_enseignant, $id_annee, $id_matiere_commune);
    $stmt->execute();
    $stmt->close();
  }
  
  $conn->close();
  
  echo json_encode(array("status" => "success"));
} else {
  echo json_encode(array("status" => "error", "message" => "Invalid parameters."));
}
?>
