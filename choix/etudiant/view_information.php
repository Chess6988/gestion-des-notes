<?php
session_start();
require_once 'db_connect.php';

// Debugging session variable
error_log("Session userId_etudiant at start: " . ($_SESSION['userId_etudiant'] ?? 'not set'));

// Verify if the student's ID is set in the session
if (!isset($_SESSION['userId_etudiant'])) {
    echo "Student ID is not set in session. Please create an account first.";
    exit();
}

$id_etudiant = $_SESSION['userId_etudiant'];

// Fetch student profile information
$sql_profile = "
SELECT e.id_etudiant, e.firstName_etudiant, e.lastName_etudiant, f.nom_filiere AS filiere, s.nom_semestre AS semestre, a.annee AS annee, n.nom_niveau AS niveau
FROM profile_etudiant pe
JOIN etudiants e ON pe.id_etudiant = e.id_etudiant
JOIN filieres f ON pe.id_filiere = f.id_filiere
JOIN semestres s ON pe.id_semestre = s.id_semestre
JOIN annees a ON pe.id_annee = a.id_annee
JOIN niveaux n ON pe.id_niveau = n.id_niveau
WHERE pe.id_etudiant = ?";

$stmt_profile = $conn->prepare($sql_profile);
if (!$stmt_profile) {
    die("Prepare failed: " . $conn->error);
}
$stmt_profile->bind_param("i", $id_etudiant);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
$profile = $result_profile->fetch_assoc();

if (!$profile) {
    echo "No profile information found for the student ID: " . htmlspecialchars($id_etudiant);
    exit();
}

// Fetch student subjects
$sql_matieres = "
SELECT m.nom_matiere AS matiere
FROM matieres_etudiants me
JOIN matieres m ON me.id_matiere = m.id_matiere
WHERE me.id_etudiant = ?";
$stmt_matieres = $conn->prepare($sql_matieres);
if (!$stmt_matieres) {
    die("Prepare failed: " . $conn->error);
}
$stmt_matieres->bind_param("i", $id_etudiant);
$stmt_matieres->execute();
$result_matieres = $stmt_matieres->get_result();

// Fetch common subjects
$sql_matieres_communes = "
SELECT mc.nom_matiere_commune AS matieres_communes
FROM matieres_communes_etudiants mce
JOIN matieres_communes mc ON mce.id_matiere_commune = mc.id_matiere_commune
WHERE mce.id_etudiant = ?";
$stmt_matieres_communes = $conn->prepare($sql_matieres_communes);
if (!$stmt_matieres_communes) {
    die("Prepare failed: " . $conn->error);
}
$stmt_matieres_communes->bind_param("i", $id_etudiant);
$stmt_matieres_communes->execute();
$result_matieres_communes = $stmt_matieres_communes->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos Informations</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .table th {
            background-color: #343a40;
            color: #fff;
            text-transform: uppercase;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn i {
            margin-right: 5px;
        }
        .btn-block {
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        ul {
            padding-left: 20px;
        }
        ul li {
            margin-bottom: 5px;
        }
        ul li::before {
            content: '\f0da'; /* FontAwesome icon: fa-chevron-right */
            font-family: "Font Awesome 5 Free";
            padding-right: 10px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-user-graduate"></i> Vos Informations</h2>
        <table class="table table-bordered">
          
            <tr>
                <th>First Name</th>
                <td><?php echo htmlspecialchars($profile['firstName_etudiant']); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?php echo htmlspecialchars($profile['lastName_etudiant']); ?></td>
            </tr>
            <tr>
                <th>Filiere</th>
                <td><?php echo htmlspecialchars($profile['filiere']); ?></td>
            </tr>
            <tr>
                <th>Semestre</th>
                <td><?php echo htmlspecialchars($profile['semestre']); ?></td>
            </tr>
            <tr>
                <th>Annee</th>
                <td><?php echo htmlspecialchars($profile['annee']); ?></td>
            </tr>
            <tr> 
                <th>Niveau</th>
                <td><?php echo htmlspecialchars($profile['niveau']); ?></td>
            </tr>
            <tr>
                <th>Matieres</th>
                <td>
                    <ul>
                        <?php while ($matiere = $result_matieres->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($matiere['matiere']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Matieres Communes</th>
                <td>
                    <ul>
                        <?php while ($matiere_commune = $result_matieres_communes->fetch_assoc()): ?>
                            <li><?php echo htmlspecialchars($matiere_commune['matieres_communes']); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </td>
            </tr>
        </table>
        <p>
            <a href="edit_profile.php" class="btn btn-primary btn-block"><i class="fas fa-edit"></i> Edit Your Profile</a>
        </p>
        <p>
            <a href="Mentor/index.html" class="btn btn-danger btn-block"><i class="fas fa-door-open"></i> Access the Platform</a>
        </p>
    </div>
</body>
</html>


<?php
$stmt_profile->close();
$stmt_matieres->close();
$stmt_matieres_communes->close();
$conn->close();
?>
