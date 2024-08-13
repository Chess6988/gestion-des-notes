<?php
session_start();
require_once 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) {
    header("Location: signin_admin.php");
    exit();
}

$conn = getDbConnection();

// Function to fetch enseignants' profiles that are not validated
function fetchEnseignantsProfiles($conn) {
    $sql = "SELECT pe.id_profile_enseignant, e.id_enseignant, e.firstName_enseignant, e.lastName_enseignant, 
                   a.annee, m.nom_matiere, m.courseCode AS matiere_code,
                   mc.nom_matiere_commune, mc.courseCode AS matiere_commune_code,
                   pe.validated, pe.date_creation
            FROM enseignants e
            JOIN profile_enseignant pe ON e.id_enseignant = pe.id_enseignant
            LEFT JOIN matieres m ON pe.id_matiere = m.id_matiere
            LEFT JOIN matieres_communes mc ON pe.id_matiere_commune = mc.id_matiere_commune
            LEFT JOIN annees a ON pe.id_annee = a.id_annee
            WHERE pe.validated = 0
            ORDER BY pe.date_creation DESC";
    
    $result = $conn->query($sql);

    if ($result === false) {
        // Output the error message for debugging
        echo "Error: " . $conn->error;
        return null;
    }

    if ($result->num_rows > 0) {
        return $result;
    }
    return null;
}

// Function to validate an enseignant profile
function validateEnseignant($id_profile_enseignant, $conn) {
    $sql = "UPDATE profile_enseignant SET validated = 1 WHERE id_profile_enseignant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_profile_enseignant);
    return $stmt->execute();
}

// Handle validation request
if (isset($_POST['validate'])) {
    $id_profile_enseignant = $_POST['id_profile_enseignant'];
    if (validateEnseignant($id_profile_enseignant, $conn)) {
        $_SESSION['success_message'] = "Enseignant profile validated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to validate the enseignant profile.";
    }
    header("Location: valide.php");
    exit();
}

$enseignantsProfiles = fetchEnseignantsProfiles($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des Enseignants</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #f4f7f9;
        }
        .container {
            max-width: 1200px;
            margin-top: 50px;
        }
        h2 {
            font-family: 'Arial', sans-serif;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
            color: #343a40;
        }
        .table-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .table thead th {
            background-color: #343a40;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-outline-success a, .btn-outline-success a:visited {
            color: #28a745;
            text-decoration: none;
        }
        .btn-outline-success a:hover {
            color: white;
        }
        .btn-info, .btn-success, .btn-warning, .btn-outline-success {
            border-radius: 20px;
            font-weight: bold;
        }
        .alert {
            border-radius: 20px;
        }
        .btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Validation des Enseignants</h2>

    <!-- Success and Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Table of enseignants awaiting validation -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Année</th>
                    <th>Matière</th>
                    <th>Course Code</th>
                    <th>Matière Commune</th>
                    <th>Course Code</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($enseignantsProfiles): ?>
                    <?php while ($row = $enseignantsProfiles->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['lastName_enseignant']); ?></td>
                            <td><?= htmlspecialchars($row['firstName_enseignant']); ?></td>
                            <td><?= htmlspecialchars($row['annee']); ?></td>
                            <td><?= htmlspecialchars($row['nom_matiere']); ?></td>
                            <td><?= htmlspecialchars($row['matiere_code']); ?></td>
                            <td><?= htmlspecialchars($row['nom_matiere_commune']); ?></td>
                            <td><?= htmlspecialchars($row['matiere_commune_code']); ?></td>
                            <td>
                                <a href="view_enseignant.php?id=<?= $row['id_enseignant']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <form action="valide.php" method="post" style="display:inline;">
                                    <input type="hidden" name="id_profile_enseignant" value="<?= $row['id_profile_enseignant']; ?>">
                                    <button type="submit" name="validate" class="btn btn-success btn-sm">
                                        <i class="fas fa-check"></i> Valider
                                    </button>
                                </form>
                                <a href="edit_enseignant.php?id=<?= $row['id_enseignant']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucun enseignant en attente de validation.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <button type="button" class="btn btn-outline-success mt-3">
        <i class="fas fa-arrow-left"></i> <a href="admin-dashboard.php">Retour à la page d'accueil</a>
    </button>
</div>




<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

