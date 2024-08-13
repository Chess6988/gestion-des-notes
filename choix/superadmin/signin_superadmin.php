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
$firstName_superadmin = $_POST['firstName_superadmin'];
$lastName_superadmin = $_POST['lastName_superadmin'];
$password = $_POST['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM superadmins WHERE firstName_superadmin = ? AND lastName_superadmin = ?");
if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Prepare statement failed: " . $conn->error]));
}

$stmt->bind_param("ss", $firstName_superadmin, $lastName_superadmin);
if (!$stmt->execute()) {
    die(json_encode(["status" => "error", "message" => "Execute statement failed: " . $stmt->error]));
}

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

// If login successful, set session variables
session_start();
$_SESSION['firstName_superadmin'] = $firstName_superadmin;
$_SESSION['lastName_superadmin'] = $lastName_superadmin;

echo json_encode(["status" => "success", "message" => "Login successful."]);

// Close connections
$stmt->close();
$conn->close();
?>
