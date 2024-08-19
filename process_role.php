<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_des_notes";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array('status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error)));
}

// Temporary debugging script to log all roles and passwords
$result = $conn->query("SELECT role, rolePassword FROM roles");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        error_log("Role: " . $row["role"] . " - Password: " . $row["rolePassword"]);
    }
} else {
    error_log("0 results");
}

$response = array('status' => 'error', 'message' => 'Mot de passe incorrect');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_STRING);
    $plainPassword = filter_input(INPUT_POST, 'rolePassword', FILTER_SANITIZE_STRING);

    if (empty($role) || empty($plainPassword)) {
        echo json_encode(array('status' => 'error', 'message' => 'Paramètres manquants ou invalides.'));
        exit;
    }

    // SQL query to fetch plain text password from database based on role
    $stmt = $conn->prepare("SELECT rolePassword FROM roles WHERE role = ?");
    if ($stmt) {
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $stmt->bind_result($dbPassword);
        $stmt->fetch();
        $stmt->close();

        // Debugging: Log fetched plain text password
        error_log("Fetched Password from DB: " . $dbPassword);

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
}

$conn->close();
echo json_encode($response);
?>