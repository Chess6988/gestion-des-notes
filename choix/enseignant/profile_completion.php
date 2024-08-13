<?php
session_start();

if (!isset($_SESSION['firstName_enseignant']) || !isset($_SESSION['lastName_enseignant'])) {
    header("Location: signin_enseignant.php");
    exit();
}

require 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Completion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Profile Completion</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <script>
                setTimeout(function() {
                    document.querySelector('.alert').style.display = 'none';
                    document.getElementById('profile-form').style.display = 'none';
                }, 5000);
            </script>
        <?php endif; ?>
        
        <form id="profile-form" action="generate_subjects.php" method="post">
            <div class="form-group">
                <label for="annee">Select Annee:</label>
                <select class="form-control" id="annee" name="annee">
                    <?php
                    $conn = getDbConnection();
                    $sql = "SELECT id_annee, annee FROM annees";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['id_annee'] . "'>" . $row['annee'] . "</option>";
                        }
                    }
                    $conn->close();
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate Subjects</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
