<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_des_notes";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Fetch years (annees)
$sql = "SELECT id_annee, annee FROM annees";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $years = [];
    while ($row = $result->fetch_assoc()) {
        $years[] = [
            'id_annee' => $row['id_annee'],
            'annee' => $row['annee']
        ];
    }
    echo json_encode(['status' => 'success', 'years' => $years]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No years found.']);
}

$conn->close();
?>
