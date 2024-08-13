<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

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

$user = $_SESSION['user'];
$firstName_superadmin = $_POST['firstName_superadmin'];
$lastName_superadmin = $_POST['lastName_superadmin'];
$newPassword = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Validate password if it is being changed
if (!empty($newPassword)) {
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
        exit();
    }
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
} else {
    // Use the existing hashed password if the password is not being changed
    $hashedPassword = $user['password'];
}

// Update user information in the database
$sql = "UPDATE  superadmins  SET firstName_superadmin = ?, lastName_superadmin = ?, password = ? WHERE id_superadmin = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}
$stmt->bind_param("sssi", $firstName_superadmin, $lastName_superadmin, $hashedPassword, $user['userId_superadmin']);

if ($stmt->execute()) {
    // Update session information
    $_SESSION['user']['firstName_superadmin'] = $firstName_superadmin;
    $_SESSION['user']['lastName_superadmin'] = $lastName_superadmin;
    $_SESSION['user']['password'] = $hashedPassword;
    echo json_encode(['status' => 'success', 'message' => 'Informations mises à jour avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour des informations: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
