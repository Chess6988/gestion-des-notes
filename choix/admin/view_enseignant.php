<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) {
    header("Location: signin_admin.php");
    exit();
}

$conn = getDbConnection();

function fetchEnseignantProfile($id_enseignant, $conn) {
    $sql = "SELECT e.firstName_enseignant, e.lastName_enseignant, e.date_creation AS date_creation_enseignant,
                   a.annee, m.nom_matiere, m.courseCode AS matiere_code,
                   mc.nom_matiere_commune, mc.courseCode AS matiere_commune_code,
                   pe.validated, pe.date_creation
            FROM enseignants e
            LEFT JOIN profile_enseignant pe ON e.id_enseignant = pe.id_enseignant
            LEFT JOIN matieres m ON pe.id_matiere = m.id_matiere
            LEFT JOIN matieres_communes mc ON pe.id_matiere_commune = mc.id_matiere_commune
            LEFT JOIN annees a ON pe.id_annee = a.id_annee
            WHERE e.id_enseignant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_enseignant);
    $stmt->execute();
    return $stmt->get_result();
}

if (!isset($_GET['id'])) {
    header("Location: valide.php");
    exit();
}

$id_enseignant = $_GET['id'];
$enseignantProfile = fetchEnseignantProfile($id_enseignant, $conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir Enseignant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa;">

<div class="container mt-5">
    <h2 class="mb-4" style="color: #343a40; font-weight: bold; display: flex; align-items: center;">
        <i class="fas fa-chalkboard-teacher" style="margin-right: 10px;"></i> 
        Voir Enseignant
    </h2>
    <table class="table table-hover table-striped">
        <thead class="thead-dark">
        <tr>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-user" style="margin-right: 5px;"></i> Nom
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-user" style="margin-right: 5px;"></i> Prénom
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-calendar-alt" style="margin-right: 5px;"></i> Année
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-book" style="margin-right: 5px;"></i> Matière
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-code" style="margin-right: 5px;"></i> Course Code
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-book" style="margin-right: 5px;"></i> Matière Commune
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-code" style="margin-right: 5px;"></i> Course Code
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-check-circle" style="margin-right: 5px;"></i> Validé
            </th>
            <th style="text-align: center; vertical-align: middle;">
                <i class="fas fa-calendar" style="margin-right: 5px;"></i> Date de Création
            </th>
        </tr>
        </thead>
        <tbody>
        <?php if ($enseignantProfile): ?>
            <?php while ($row = $enseignantProfile->fetch_assoc()): ?>
                <tr style="text-align: center;">
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['lastName_enseignant']); ?></td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['firstName_enseignant']); ?></td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['annee']); ?></td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['nom_matiere']); ?></td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['matiere_code']); ?></td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['nom_matiere_commune']); ?></td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['matiere_commune_code']); ?></td>
                    <td style="vertical-align: middle;">
                        <?= $row['validated'] ? '<i class="fas fa-check text-success"></i> Oui' : '<i class="fas fa-times text-danger"></i> Non'; ?>
                    </td>
                    <td style="vertical-align: middle;"><?= htmlspecialchars($row['date_creation']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center" style="vertical-align: middle;">Aucune information disponible pour cet enseignant.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="valide.php" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff; display: flex; align-items: center;">
        <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Retour
    </a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
