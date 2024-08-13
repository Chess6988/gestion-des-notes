<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['id_enseignant'])) {
    header("Location: signin_enseignant.php");
    exit();
}

$id_enseignant = $_SESSION['id_enseignant'];

function checkValidationStatus($id_enseignant, $conn) {
    $sql = "SELECT validated FROM profile_enseignant WHERE id_enseignant = ? AND validated = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_enseignant);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        return true;
    }
    return false;
}

$conn = getDbConnection();

if (checkValidationStatus($id_enseignant, $conn)) {
    $conn->close();
    header("Location: index.php");
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En attente de validation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Arial', sans-serif;
            background-image: url('imm.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .alert {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .alert i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .container {
            max-width: 600px;
            text-align: center;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-image: url('ime.jpg') no-repeat center center fixed;
            background-size: cover;
        }
        

        h2 {
            color: #333;
            margin-bottom: 1rem;
        }

        .loading-icon {
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            from {transform: rotate(0deg);}
            to {transform: rotate(360deg);}
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>En attente de validation</h2>
        <div class="alert alert-info" role="alert">
            <i class="fas fa-hourglass-half loading-icon"></i>
            Votre profil est en attente de validation par l'administrateur. Veuillez patienter...
        </div>
    </div>
    <meta http-equiv="refresh" content="10"> <!-- Refresh the page every 10 seconds -->
</body>
</html>

