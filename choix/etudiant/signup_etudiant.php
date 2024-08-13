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

// Use null coalescing operator to handle undefined keys and check for missing data
$firstName_etudiant = $_POST['firstName_etudiant'] ?? null;
$lastName_etudiant = $_POST['lastName_etudiant'] ?? null;
$password = $_POST['password'] ?? null;
$confirmPassword = $_POST['confirmPassword'] ?? null;

if (!$firstName_etudiant || !$lastName_etudiant || !$password || !$confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit();
}

// Check if passwords match
if ($password !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
    exit();
}

// Check if the user already exists
$sql = "SELECT * FROM etudiants WHERE firstName_etudiant = ? AND lastName_etudiant = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
    exit();
}
$stmt->bind_param("ss", $firstName_etudiant, $lastName_etudiant);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'L\'utilisateur existe déjà.']);
} else {
    // Insert the new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sql = "INSERT INTO etudiants (firstName_etudiant, lastName_etudiant, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("sss", $firstName_etudiant, $lastName_etudiant, $hashedPassword);

    if ($stmt->execute()) {
        // Store user information in session
        $id_etudiant = $stmt->insert_id; // Fetch the last inserted ID from the statement
        $_SESSION['userId_etudiant'] = $id_etudiant;
        $_SESSION['firstName_etudiant'] = $firstName_etudiant;
        $_SESSION['lastName_etudiant'] = $lastName_etudiant;

        // Debugging session variables
        error_log("Session userId_etudiant: " . $_SESSION['userId_etudiant']);
        error_log("Session firstName_etudiant: " . $_SESSION['firstName_etudiant']);
        error_log("Session lastName_etudiant: " . $_SESSION['lastName_etudiant']);

        echo json_encode(['status' => 'success', 'message' => 'Compte créé avec succès.', 'userId_etudiant' => $id_etudiant, 'firstName_etudiant' => $firstName_etudiant, 'lastName_etudiant' => $lastName_etudiant]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du compte: ' . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>
