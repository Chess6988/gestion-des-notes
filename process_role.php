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
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

$response = array('status' => 'error', 'message' => 'Mot de passe incorrect');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $plainPassword = $_POST['rolePassword'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT rolePassword FROM roles WHERE role = ?");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $stmt->bind_result($dbPassword);
    if ($stmt->fetch()) {
        // Verify the password
        if ($dbPassword !== null && $dbPassword === $plainPassword) {
            error_log("Password match successful");
            $response = array('status' => 'success', 'message' => 'Le rôle sélectionné a été confirmé avec succès', 'role' => $role);
        } else {
            error_log("Password match failed");
            $response['message'] = 'Mot de passe incorrect';
        }
    } else {
        $response['message'] = 'Erreur de requête SQL';
        error_log("SQL error: " . $conn->error);
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>