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

// Debugging: Log the received POST data
file_put_contents('debug.log', print_r($_POST, true));

// Check if all required fields are set
if (!isset($_POST['firstName_enseignant']) || !isset($_POST['lastName_enseignant']) || !isset($_POST['password'])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit();
}

// Get form data
$firstName_enseignant = $_POST['firstName_enseignant'];
$lastName_enseignant = $_POST['lastName_enseignant'];
$password = $_POST['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM enseignants WHERE firstName_enseignant = ? AND lastName_enseignant = ?");
$stmt->bind_param("ss", $firstName_enseignant, $lastName_enseignant);
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
session_start();
$_SESSION['id_enseignant'] = $row['id_enseignant'];  // Ensure id_enseignant is set
$_SESSION['firstName_enseignant'] = $firstName_enseignant;
$_SESSION['lastName_enseignant'] = $lastName_enseignant;

echo json_encode(["status" => "success", "message" => "Login successful."]);

// Close connections
$stmt->close();
$conn->close();
?>
