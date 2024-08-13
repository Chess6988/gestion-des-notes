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

$firstName_enseignant = $_POST['firstName_enseignant'];
$lastName_enseignant = $_POST['lastName_enseignant'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

// Check if passwords match
if ($password !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
    exit();
}

// Check if the user already exists
$sql = "SELECT * FROM enseignants WHERE firstName_enseignant = ? AND lastName_enseignant = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}
$stmt->bind_param("ss", $firstName_enseignant, $lastName_enseignant);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'L\'utilisateur existe déjà.']);
} else {
    // Insert the new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO enseignants (firstName_enseignant, lastName_enseignant, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("sss", $firstName_enseignant, $lastName_enseignant, $hashedPassword);

    if ($stmt->execute()) {
        // Store user information in session
        $_SESSION['user'] = [
            'userId_enseignant' => $stmt->insert_id,
            'firstName_enseignant' => $firstName_enseignant,
            'lastName_enseignant' => $lastName_enseignant
        ];
        echo json_encode(['status' => 'success', 'message' => 'Compte créé avec succès.', 'userId_enseignant' => $stmt->insert_id, 'firstName_enseignant' => $firstName_enseignant, 'lastName_enseignant' => $lastName_enseignant]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du compte: ' . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>
