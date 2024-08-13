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
$firstName_admin = $_POST['firstName_admin'];
$lastName_admin = $_POST['lastName_admin'];
$password = $_POST['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM admins WHERE firstName_admin = ? AND lastName_admin = ?");
$stmt->bind_param("ss", $firstName_admin, $lastName_admin);
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
$_SESSION['id_admin'] = $row['id_admin'];
$_SESSION['firstName_admin'] = $firstName_admin;
$_SESSION['lastName_admin'] = $lastName_admin;

echo json_encode(["status" => "success", "message" => "Login successful."]);

// Close connections
$stmt->close();
$conn->close();
?>
