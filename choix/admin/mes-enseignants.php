<?php
session_start();
require_once 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) {
    header("Location: signin_admin.php");
    exit();
}

$conn = getDbConnection();

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
            ORDER BY pe.date_creation DESC";
    
    $result = $conn->query($sql);

    if ($result === false) {
        echo "Error: " . $conn->error;
        return null;
    }

    if ($result->num_rows > 0) {
        return $result;
    }
    return null;
}

$enseignantsProfiles = fetchEnseignantsProfiles($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Enseignants</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
        }
        h2 {
            font-family: 'Arial', sans-serif;
            text-align: center;
            margin-bottom: 40px;
            font-weight: bold;
            color: #343a40;
        }
        .search-bar {
            position: relative;
        }
        #search {
            padding-left: 40px;
            border-radius: 20px;
        }
        .search-icon {
            position: absolute;
            top: 8px;
            left: 10px;
            font-size: 20px;
            color: #6c757d;
        }
        .table-card {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .table thead th {
            background-color: #343a40;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-danger, .btn-outline-danger {
            border-radius: 20px;
            font-weight: bold;
        }
        .btn-danger a {
            color: white;
            text-decoration: none;
        }
        .btn-danger i, .btn-outline-danger i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2><i class="fas fa-chalkboard-teacher"></i> Gestion des Enseignants</h2>
    <div class="search-bar mb-3">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="search" class="form-control" placeholder="Si vous êtes fatigué, recherchez ici...">
    </div>
    <div class="table-card">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th><i class="fas fa-user"></i> Nom</th>
                <th><i class="fas fa-user"></i> Prénom</th>
                <th><i class="fas fa-calendar-alt"></i> Année</th>
                <th><i class="fas fa-book"></i> Matière</th>
                <th><i class="fas fa-code"></i> Course Code</th>
                <th><i class="fas fa-book-reader"></i> Matière Commune</th>
                <th><i class="fas fa-code-branch"></i> Course Code</th>
                <th><i class="fas fa-check-circle"></i> Validé</th>
                <th><i class="fas fa-calendar-check"></i> Date de Création</th>
                <th><i class="fas fa-tools"></i> Actions</th>
            </tr>
            </thead>
            <tbody id="enseignantsTable">
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
                        <td><?= $row['validated'] ? '<i class="fas fa-check"></i> Oui' : '<i class="fas fa-times"></i> Non'; ?></td>
                        <td><?= htmlspecialchars($row['date_creation']); ?></td>
                        <td>
                            <form action="delete_profile.php" method="post" style="display:inline;">
                                <input type="hidden" name="id_profile_enseignant" value="<?= $row['id_profile_enseignant']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">Aucun enseignant trouvé.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <button type="button" class="btn btn-outline-danger mt-3">
        <i class="fas fa-arrow-left"></i> <a href="admin-dashboard.php">Retour à la page d'accueil</a>
    </button>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    $.ajax({
        url: 'update_new_entries.php',
        type: 'POST',
        success: function(response) {
            console.log('New entries updated');
        }
    });

    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#enseignantsTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
</body>
</html>

</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
$(document).ready(function(){
    $.ajax({
        url: 'update_new_entries.php',
        type: 'POST',
        success: function(response) {
            console.log('New entries updated');
        }
    });

    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#enseignantsTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
</body>
</html>

