<?php
$host = 'localhost';
$db = 'gestion_des_notes';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id_filiere, nom FROM filieres";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id_filiere'] . "'>" . $row['nom'] . "</option>";
    }
} else {
    echo "<option value=''>No filieres available</option>";
}

$conn->close();
?>
