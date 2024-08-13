<?php
$host = 'localhost';
$db = 'gestion_des_notes';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_semestre, numero FROM semestres";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id_semestre'] . "'>Semester " . $row['numero'] . "</option>";
    }
} else {
    echo "<option value=''>No semesters available</option>";
}

$conn->close();
?>
