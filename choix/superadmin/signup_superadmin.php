<?php
header('Content-Type: application/json');

session_start();

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

$firstName_superadmin = $_POST['firstName_superadmin'];
$lastName_superadmin = $_POST['lastName_superadmin'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Check if passwords match
if ($password !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
    exit();
}

// Check if the user already exists
$sql = "SELECT * FROM superadmins WHERE firstName_superadmin = ? AND lastName_superadmin = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}
$stmt->bind_param("ss", $firstName_superadmin, $lastName_superadmin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'L\'utilisateur existe déjà.']);
} else {
    // Insert the new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql_insert = "INSERT INTO superadmins (firstName_superadmin, lastName_superadmin, password) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    if (!$stmt_insert) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }
    $stmt_insert->bind_param("sss", $firstName_superadmin, $lastName_superadmin, $hashedPassword);

    if ($stmt_insert->execute()) {
        // Store user information in session
        $_SESSION['user'] = [
            'userId_superadmin' => $stmt_insert->insert_id,
            'firstName_superadmin' => $firstName_superadmin,
            'lastName_superadmin' => $lastName_superadmin,
        ];
        echo json_encode(['status' => 'success', 'message' => 'Compte créé avec succès.', 'userId_superadmin' => $stmt_insert->insert_id, 'firstName_superadmin' => $firstName_superadmin, 'lastName_superadmin' => $lastName_superadmin]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du compte: ' . $stmt_insert->error]);
    }
}

$stmt->close();
$conn->close();
?>
