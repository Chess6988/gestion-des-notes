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

$firstName_admin = $_POST['firstName_admin'];
$lastName_admin = $_POST['lastName_admin'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Check if passwords match
if ($password !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
    exit();
}

// Check if the user already exists
$sql = "SELECT * FROM admins WHERE firstName_admin = ? AND lastName_admin = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}
$stmt->bind_param("ss", $firstName_admin, $lastName_admin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'L\'utilisateur existe déjà.']);
} else {
    // Insert the new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO admins (firstName_admin, lastName_admin, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("sss", $firstName_admin, $lastName_admin, $hashedPassword);

    if ($stmt->execute()) {
        // Store user information in session
        $_SESSION['id_admin'] = $stmt->insert_id;
        $_SESSION['firstName_admin'] = $firstName_admin;
        $_SESSION['lastName_admin'] = $lastName_admin;
        echo json_encode(['status' => 'success', 'message' => 'Compte créé avec succès.', 'id_admin' => $stmt->insert_id, 'firstName_admin' => $firstName_admin, 'lastName_admin' => $lastName_admin]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du compte: ' . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>
