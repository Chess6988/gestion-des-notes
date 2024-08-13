<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) {
    header("Location: signin_admin.php");
    exit();
}

function fetchProfilesEnseignant() {
    $conn = getDbConnection();
    $sql = "SELECT pe.id_profile_enseignant, e.firstName_enseignant, e.lastName_enseignant, a.annee, m.nom_matiere, mc.nom_matiere_commune, pe.date_creation
            FROM profile_enseignant pe
            JOIN enseignants e ON pe.id_enseignant = e.id_enseignant
            JOIN annees a ON pe.id_annee = a.id_annee
            LEFT JOIN matieres m ON pe.id_matiere = m.id_matiere
            LEFT JOIN matieres_communes mc ON pe.id_matiere_commune = mc.id_matiere_commune
            WHERE pe.validated = 0";
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

function validateProfile($profile_id) {
    $conn = getDbConnection();

    // Check if the profile already exists
    $check_sql = "SELECT * FROM profile_enseignant WHERE id_profile_enseignant = ? AND validated = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $profile_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $check_stmt->close();
        $conn->close();
        return false; // Profile already validated
    }

    $check_stmt->close();

    // Validate the profile
    $sql = "UPDATE profile_enseignant SET validated = 1 WHERE id_profile_enseignant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    return true;
}

$profileExists = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profile_id = $_POST['profile_id'];
    $profileExists = !validateProfile($profile_id);
}

$profiles = fetchProfilesEnseignant();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valider Profils Enseignants</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Profils Enseignants à Valider</h2>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Année</th>
            <th>Matière</th>
            <th>Matière Commune</th>
            <th>Date de Création</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $profiles->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_profile_enseignant'] ?></td>
                <td><?= $row['firstName_enseignant'] ?></td>
                <td><?= $row['lastName_enseignant'] ?></td>
                <td><?= $row['annee'] ?></td>
                <td><?= $row['nom_matiere'] ?></td>
                <td><?= $row['nom_matiere_commune'] ?></td>
                <td><?= $row['date_creation'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="profile_id" value="<?= $row['id_profile_enseignant'] ?>">
                        <button type="submit" class="btn btn-success">Valider</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="duplicateModalLabel">Erreur de Validation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Ce profil existe déjà et a été validé.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
<?php if ($profileExists): ?>
$(document).ready(function() {
    $('#duplicateModal').modal('show');
});
<?php endif; ?>
</script>
</body>
</html>
