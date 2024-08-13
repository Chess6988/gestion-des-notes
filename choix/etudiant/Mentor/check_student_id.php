<?php
$host = 'localhost';
$db = 'gestion_des_notes';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentId = $_POST['studentId'];

$sql = "SELECT id_etudiant FROM etudiants WHERE id_etudiant = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$stmt->store_result();

$response = array('exists' => $stmt->num_rows > 0);

echo json_encode($response);

$stmt->close();
$conn->close();
?>
