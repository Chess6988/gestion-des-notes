<?php
// generate_subjects.php
require 'db_connect.php';

if(isset($_POST['id_annee']) && isset($_POST['id_enseignant'])){
  $id_annee = $_POST['id_annee'];
  $id_enseignant = $_POST['id_enseignant'];
  $conn = getDbConnection();
  
  // Query for available Matières for the selected academic year.
  // Exclude those already registered by this teacher (from profile_enseignant)
  $queryMatieres = "SELECT m.id_matiere, m.nom_matiere 
                    FROM matieres m 
                    WHERE m.id_annee = ? 
                    AND m.id_matiere NOT IN (
                      SELECT id_matiere 
                      FROM profile_enseignant 
                      WHERE id_enseignant = ? AND id_annee = ? AND id_matiere IS NOT NULL
                    )";
  $stmt = $conn->prepare($queryMatieres);
  $stmt->bind_param("iii", $id_annee, $id_enseignant, $id_annee);
  $stmt->execute();
  $result = $stmt->get_result();
  $matieres = array();
  while($row = $result->fetch_assoc()){
    $matieres[] = $row;
  }
  $stmt->close();
  
  // Query for available Matières Communes
  // (Assuming a separate table "matieres_communes" with fields id_matiere_commune, nom_matiere_commune and id_annee)
  $queryMatieresComm = "SELECT mc.id_matiere_commune, mc.nom_matiere_commune 
                        FROM matieres_communes mc 
                        WHERE mc.id_annee = ? 
                        AND mc.id_matiere_commune NOT IN (
                          SELECT id_matiere_commune 
                          FROM profile_enseignant 
                          WHERE id_enseignant = ? AND id_annee = ? AND id_matiere_commune IS NOT NULL
                        )";
  $stmt2 = $conn->prepare($queryMatieresComm);
  $stmt2->bind_param("iii", $id_annee, $id_enseignant, $id_annee);
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  $matieres_communes = array();
  while($row = $result2->fetch_assoc()){
    $matieres_communes[] = $row;
  }
  $stmt2->close();
  
  $conn->close();
  
  $response = array(
    "matieres" => $matieres,
    "matieres_communes" => $matieres_communes
  );
  echo json_encode($response);
}
?>
