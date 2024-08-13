<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_des_notes";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get form data
$firstName_etudiant = $_POST['firstName_etudiant'];
$lastName_etudiant = $_POST['lastName_etudiant'];
$password = $_POST['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM etudiants WHERE firstName_etudiant = ? AND lastName_etudiant = ? LIMIT 1");

$stmt->bind_param("ss", $firstName_etudiant, $lastName_etudiant);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit();
}

// Verify password
$row = $result->fetch_assoc();
if (!password_verify($password, $row['password'])) {
    echo json_encode(["status" => "error", "message" => "Incorrect password."]);
    exit();
}

// If login successful, set session or redirect
// If login successful, set session or redirect
session_start();
$_SESSION['userId_etudiant'] = $row['id_etudiant'];
$_SESSION['firstName_etudiant'] = $row['firstName_etudiant'];
$_SESSION['lastName_etudiant'] = $row['lastName_etudiant'];


echo json_encode(["status" => "success", "message" => "Login successful."]);

// Close connections
$stmt->close();
$conn->close();
?>
