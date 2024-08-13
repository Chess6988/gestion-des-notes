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
                   pe.id_profile_enseignant, pe.id_annee, pe.id_matiere, pe.id_matiere_commune, pe.validated
            FROM enseignants e
            LEFT JOIN profile_enseignant pe ON e.id_enseignant = pe.id_enseignant
            WHERE e.id_enseignant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_enseignant);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateEnseignantProfile($id_profile_enseignant, $id_annee, $id_matiere, $id_matiere_commune, $conn) {
    $sql = "UPDATE profile_enseignant 
            SET id_annee = ?, id_matiere = ?, id_matiere_commune = ? 
            WHERE id_profile_enseignant = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $id_annee, $id_matiere, $id_matiere_commune, $id_profile_enseignant);
    return $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id_profile_enseignant = intval($_POST['id_profile_enseignant']);
    $id_annee = intval($_POST['id_annee']);
    $id_matiere = !empty($_POST['id_matiere']) ? intval($_POST['id_matiere']) : null;
    $id_matiere_commune = !empty($_POST['id_matiere_commune']) ? intval($_POST['id_matiere_commune']) : null;

    if (updateEnseignantProfile($id_profile_enseignant, $id_annee, $id_matiere, $id_matiere_commune, $conn)) {
        $_SESSION['success_message'] = "Profil de l'enseignant mis à jour avec succès.";
        header("Location: valide.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Échec de la mise à jour du profil de l'enseignant.";
    }
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: valide.php");
    exit();
}

$id_enseignant = intval($_GET['id']);
$enseignantProfile = fetchEnseignantProfile($id_enseignant, $conn);

$annees = $conn->query("SELECT id_annee, annee FROM annees")->fetch_all(MYSQLI_ASSOC);
$matieres = $conn->query("SELECT id_matiere, nom_matiere FROM matieres")->fetch_all(MYSQLI_ASSOC);
$matieres_communes = $conn->query("SELECT id_matiere_commune, nom_matiere_commune FROM matieres_communes")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Enseignant</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"><!-- Font Awesome -->
    <style>
        /* Custom Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
            font-weight: bold;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        h2 i {
            margin-right: 10px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            display: flex;
            align-items: center;
        }
        .btn-primary i {
            margin-right: 5px;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2><i class="fas fa-user-edit"></i> Modifier Enseignant</h2>

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
            <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if ($enseignantProfile): ?>
        <form action="edit_enseignant.php?id=<?= $id_enseignant; ?>" method="post">
            <div class="form-group">
                <label for="firstName"><i class="fas fa-user"></i> Prénom</label>
                <input type="text" class="form-control" id="firstName" name="firstName_enseignant" 
                       value="<?= htmlspecialchars($enseignantProfile['firstName_enseignant']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="lastName"><i class="fas fa-user"></i> Nom</label>
                <input type="text" class="form-control" id="lastName" name="lastName_enseignant" 
                       value="<?= htmlspecialchars($enseignantProfile['lastName_enseignant']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="id_annee"><i class="fas fa-calendar-alt"></i> Année</label>
                <input type="text" id="search" class="form-control mb-3" placeholder="Rechercher...">
                <select class="form-control" id="id_annee" name="id_annee" required>
                    <option value="">Sélectionner l'année</option>
                    <?php foreach ($annees as $annee): ?>
                        <option value="<?= $annee['id_annee']; ?>" <?= ($annee['id_annee'] == $enseignantProfile['id_annee']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($annee['annee']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_matiere"><i class="fas fa-book"></i> Matière</label>
                <select class="form-control" id="id_matiere" name="id_matiere">
                    <option value="">Sélectionner la matière</option>
                    <?php foreach ($matieres as $matiere): ?>
                        <option value="<?= $matiere['id_matiere']; ?>" <?= ($matiere['id_matiere'] == $enseignantProfile['id_matiere']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($matiere['nom_matiere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_matiere_commune"><i class="fas fa-book"></i> Matière Commune</label>
                <select class="form-control" id="id_matiere_commune" name="id_matiere_commune">
                    <option value="">Sélectionner la matière commune</option>
                    <?php foreach ($matieres_communes as $matiere_commune): ?>
                        <option value="<?= $matiere_commune['id_matiere_commune']; ?>" <?= ($matiere_commune['id_matiere_commune'] == $enseignantProfile['id_matiere_commune']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($matiere_commune['nom_matiere_commune']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="id_profile_enseignant" value="<?= $enseignantProfile['id_profile_enseignant']; ?>">
            <button type="submit" name="update" class="btn btn-primary">
                <i class="fas fa-save"></i> Mettre à jour
            </button>
            <a href="valide.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </form>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> Aucun enseignant trouvé.
        </div>
        <a href="valide.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    <?php endif; ?>
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


<script>
    $(document).ready(function() {
        $("#search").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#id_annee option").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
