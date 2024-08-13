<?php
require_once 'db_connect.php';

$conn = getDbConnection();

$sql = "UPDATE profile_enseignant SET new_entry = 0 WHERE new_entry = 1";
if ($conn->query($sql) === TRUE) {
    echo "New entries updated successfully";
} else {
    echo "Error updating records: " . $conn->error;
}

$conn->close();
?>
