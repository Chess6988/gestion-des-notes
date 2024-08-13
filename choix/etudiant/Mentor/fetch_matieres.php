<?php
$host = 'localhost';
$db = 'gestion_des_notes';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$filiereId = $_POST['filiereId'];

$sql = "SELECT nom_matiere FROM matieres WHERE id_filiere = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $filiereId);
$stmt->execute();
$result = $stmt->get_result();

$matieres = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $matieres .= "<li>{$row['nom_matiere']}</li>";
    }
}

echo $matieres;

$stmt->close();
$conn->close();
?>
