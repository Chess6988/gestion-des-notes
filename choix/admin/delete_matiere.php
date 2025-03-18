<?php
require 'db_connect.php';
$conn = getDbConnection();

header('Content-Type: application/json'); // Ensure correct JSON response

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
    exit();
}

// ✅ Log Request Data for Debugging
error_log("🛠 DELETE Request Data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_matiere']) && isset($_POST['matiere_type'])) {
    $id_matiere = filter_var($_POST['id_matiere'], FILTER_VALIDATE_INT);
    $matiere_type = $_POST['matiere_type']; // Either 'matiere' or 'matiere_commune'

    // ✅ Ensure `id_matiere` is a valid integer
    if ($id_matiere === false || $id_matiere <= 0) {
        error_log("⚠️ Invalid subject ID received: " . $_POST['id_matiere']);
        echo json_encode(['success' => false, 'error' => 'Invalid subject ID.']);
        exit();
    }

    $conn->begin_transaction(); // ✅ Start Transaction for Safe Deletion

    try {
        if ($matiere_type === 'matiere') {
            // ✅ Check if the subject exists before deletion
            $checkStmt = $conn->prepare("SELECT id_matiere FROM matieres WHERE id_matiere = ?");
            $checkStmt->bind_param("i", $id_matiere);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("Subject does not exist.");
            }
            $checkStmt->close();

            // ✅ Delete from `profile_enseignant` first (to prevent FK constraint issues)
            $sql1 = "DELETE FROM profile_enseignant WHERE id_matiere = ?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("i", $id_matiere);
            $stmt1->execute();
            $stmt1->close();

            // ✅ Now delete from `matieres`
            $sql2 = "DELETE FROM matieres WHERE id_matiere = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $id_matiere);
            $deleteSuccess = $stmt2->execute();
            $stmt2->close();
        } elseif ($matiere_type === 'matiere_commune') {
            // ✅ Check if the common subject exists before deletion
            $checkStmt = $conn->prepare("SELECT id_matiere_commune FROM matieres_communes WHERE id_matiere_commune = ?");
            $checkStmt->bind_param("i", $id_matiere);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("Common subject does not exist.");
            }
            $checkStmt->close();

            // ✅ Delete from `profile_enseignant` first
            $sql1 = "DELETE FROM profile_enseignant WHERE id_matiere_commune = ?";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("i", $id_matiere);
            $stmt1->execute();
            $stmt1->close();

            // ✅ Now delete from `matieres_communes`
            $sql2 = "DELETE FROM matieres_communes WHERE id_matiere_commune = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $id_matiere);
            $deleteSuccess = $stmt2->execute();
            $stmt2->close();
        } else {
            throw new Exception("Invalid subject type.");
        }

        if ($deleteSuccess) {
            $conn->commit(); // ✅ Commit Transaction
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to delete subject.");
        }
    } catch (Exception $e) {
        $conn->rollback(); // ❌ Rollback on Error
        error_log("🚨 DELETE ERROR: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}
?>
