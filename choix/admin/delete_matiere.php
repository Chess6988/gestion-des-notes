<?php
require 'db_connect.php';
$conn = getDbConnection();

header('Content-Type: application/json');

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

error_log("🛠 DELETE Request Data: " . print_r($_POST, true));

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method.");
    }

    if (!isset($_POST['id_profile_enseignant'])) {
        throw new Exception("Missing required parameter: id_profile_enseignant.");
    }

    $id_profile = filter_var($_POST['id_profile_enseignant'], FILTER_VALIDATE_INT);

    if (!$id_profile || $id_profile <= 0) {
        throw new Exception("Invalid Profile ID received: " . $_POST['id_profile_enseignant']);
    }

    // ✅ DELETE the entire row from profile_enseignant
    $sql = "DELETE FROM profile_enseignant WHERE id_profile_enseignant = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id_profile);
    $deleteSuccess = $stmt->execute();

    if (!$deleteSuccess) {
        throw new Exception("Failed to delete subject: " . $stmt->error);
    }

    $stmt->close();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("❌ ERROR: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
