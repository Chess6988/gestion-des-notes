<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['firstName_etudiant']) || !isset($_SESSION['lastName_etudiant']) || !isset($_SESSION['id_filiere']) || !isset($_SESSION['id_annee']) || !isset($_SESSION['id_semestre'])) {
    // If not, redirect to the sign-in page
    header("Location: signin_etudiant.html");
    exit();
}

// Retrieve user information from the session
$firstName = $_SESSION['firstName_etudiant'];
$lastName = $_SESSION['lastName_etudiant'];
$filiere = $_SESSION['id_filiere'];
$annee = $_SESSION['id_annee'];
$semestre = $_SESSION['id_semestre'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Welcome, <span id="userName"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></span>!</h5>
                <p class="card-text">Filière: <span id="userFiliere"><?php echo htmlspecialchars($filiere); ?></span></p>
                <p class="card-text">Année Académique: <span id="userAnnee"><?php echo htmlspecialchars($annee); ?></span></p>
                <p class="card-text">Semestre: <span id="userSemestre"><?php echo htmlspecialchars($semestre); ?></span></p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
