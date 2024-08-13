<?php
session_start();

// Include the database connection file
require_once 'db_connect.php';

// Ensure the user is logged in and is a teacher
if (!isset($_SESSION['userId_enseignant'])) {
    header('Location: signin_enseignant.php');
    exit;
}

$userId_enseignant = $_SESSION['userId_enseignant'];

// Fetch filter options
function fetchOptions($conn, $table, $idColumn, $nameColumn) {
    $stmt = $conn->prepare("SELECT $idColumn, $nameColumn FROM $table");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$filieres = fetchOptions($conn, 'filieres', 'id_filiere', 'nom_filiere');
$niveaux = fetchOptions($conn, 'niveaux', 'id_niveau', 'nom_niveau');
$semestres = fetchOptions($conn, 'semestres', 'id_semestre', 'nom_semestre');
$annees = fetchOptions($conn, 'annees', 'id_annee', 'annee');

// Fetch filtered courses based on form submission
$filterConditions = [];
$filterParams = [];
$filterTypes = '';

if (isset($_GET['semestre']) && $_GET['semestre'] !== '') {
    $filterConditions[] = 'm.id_semestre = ?';
    $filterParams[] = $_GET['semestre'];
    $filterTypes .= 'i';
}

if (isset($_GET['niveau']) && $_GET['niveau'] !== '') {
    $filterConditions[] = 'm.id_niveau = ?';
    $filterParams[] = $_GET['niveau'];
    $filterTypes .= 'i';
}

if (isset($_GET['filiere']) && $_GET['filiere'] !== '') {
    $filterConditions[] = 'm.id_filiere = ?';
    $filterParams[] = $_GET['filiere'];
    $filterTypes .= 'i';
}

if (isset($_GET['annee']) && $_GET['annee'] !== '') {
    $filterConditions[] = 'm.id_annee = ?';
    $filterParams[] = $_GET['annee'];
    $filterTypes .= 'i';
}

$whereClause = '';
if (!empty($filterConditions)) {
    $whereClause = ' AND ' . implode(' AND ', $filterConditions);
}

$query = "
SELECT m.id_matiere, m.nom_matiere, m.courseCode, f.nom_filiere, n.nom_niveau, s.nom_semestre, a.annee,
       COUNT(pe.id_etudiant) AS nombre_etudiants
FROM matieres m
JOIN filieres f ON m.id_filiere = f.id_filiere
JOIN niveaux n ON m.id_niveau = n.id_niveau
JOIN semestres s ON m.id_semestre = s.id_semestre
JOIN annees a ON m.id_annee = a.id_annee
LEFT JOIN profile_etudiant pe ON m.id_matiere = pe.id_matiere
WHERE m.id_matiere IN (
    SELECT id_matiere
    FROM matieres_etudiants
    WHERE id_etudiant IN (
        SELECT id_etudiant
        FROM etudiants
        WHERE id_filiere = f.id_filiere
    )
) AND m.id_matiere IN (
    SELECT id_matiere
    FROM matieres_etudiants
    WHERE id_etudiant IN (
        SELECT id_etudiant
        FROM etudiants
        WHERE id_niveau = n.id_niveau
    )
) AND m.id_matiere IN (
    SELECT id_matiere
    FROM matieres_etudiants
    WHERE id_etudiant IN (
        SELECT id_etudiant
        FROM etudiants
        WHERE id_semestre = s.id_semestre
    )
) AND m.id_matiere IN (
    SELECT id_matiere
    FROM matieres_etudiants
    WHERE id_etudiant IN (
        SELECT id_etudiant
        FROM etudiants
        WHERE id_annee = a.id_annee
    )
) $whereClause
GROUP BY m.id_matiere, m.nom_matiere, m.courseCode, f.nom_filiere, n.nom_niveau, s.nom_semestre, a.annee";

$stmt = $conn->prepare($query);
if ($filterTypes !== '') {
    $stmt->bind_param($filterTypes, ...$filterParams);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mes Cours</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Mes Cours</h1>
        <form method="GET" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="semestre">Semestre</label>
                    <select id="semestre" name="semestre" class="form-control">
                        <option value="">Tous</option>
                        <?php foreach ($semestres as $semestre): ?>
                            <option value="<?php echo htmlspecialchars($semestre['id_semestre']); ?>">
                                <?php echo htmlspecialchars($semestre['nom_semestre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="niveau">Niveau</label>
                    <select id="niveau" name="niveau" class="form-control">
                        <option value="">Tous</option>
                        <?php foreach ($niveaux as $niveau): ?>
                            <option value="<?php echo htmlspecialchars($niveau['id_niveau']); ?>">
                                <?php echo htmlspecialchars($niveau['nom_niveau']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="filiere">Filière</label>
                    <select id="filiere" name="filiere" class="form-control">
                        <option value="">Tous</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?php echo htmlspecialchars($filiere['id_filiere']); ?>">
                                <?php echo htmlspecialchars($filiere['nom_filiere']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="annee">Année</label>
                    <select id="annee" name="annee" class="form-control">
                        <option value="">Tous</option>
                        <?php foreach ($annees as $annee): ?>
                            <option value="<?php echo htmlspecialchars($annee['id_annee']); ?>">
                                <?php echo htmlspecialchars($annee['annee']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </form>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Code du Cours</th>
                            <th>Filière</th>
                            <th>Niveau</th>
                            <th>Semestre</th>
                            <th>Année</th>
                            <th>Nombre d'étudiants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nom_matiere']); ?></td>
                                <td><?php echo htmlspecialchars($row['courseCode']); ?></td>
                                <td><?php echo htmlspecialchars($row['nom_filiere']); ?></td>
                                <td><?php echo htmlspecialchars($row['nom_niveau']); ?></td>
                                <td><?php echo htmlspecialchars($row['nom_semestre']); ?></td>
                                <td><?php echo htmlspecialchars($row['annee']); ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_etudiants']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
