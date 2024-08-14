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
$notes = [];
$student_name = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_annee'])) {
    $id_annee = $_POST['id_annee'];
    
    // Fetch notes based on id_etudiant and id_annee
    $stmt = $conn->prepare("
        SELECT n.*, 
               m.nom_matiere, m.courseCode AS courseCodeMatiere, 
               mc.nom_matiere_commune, mc.courseCode AS courseCodeMatiereCommune, 
               f.nom_filiere, s.nom_semestre, niv.nom_niveau,
               e.firstName_etudiant, e.lastName_etudiant
        FROM notes n 
        LEFT JOIN matieres m ON n.id_matiere = m.id_matiere 
        LEFT JOIN matieres_communes mc ON n.id_matiere_commune = mc.id_matiere_commune 
        LEFT JOIN filieres f ON m.id_filiere = f.id_filiere OR mc.id_filiere = f.id_filiere 
        LEFT JOIN semestres s ON m.id_semestre = s.id_semestre OR mc.id_semestre = s.id_semestre 
        LEFT JOIN niveaux niv ON m.id_niveau = niv.id_niveau OR mc.id_niveau = niv.id_niveau
        LEFT JOIN etudiants e ON n.id_etudiant = e.id_etudiant
        WHERE n.id_etudiant = ? AND n.id_annee = ?");
    $stmt->bind_param("ii", $id_etudiant, $id_annee);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
        $student_name = $row['firstName_etudiant'] . ' ' . $row['lastName_etudiant'];
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('https://www.transparenttextures.com/patterns/asfalt-dark.png');
            background-size: cover;
            background-position: center;
            color: #ffffff;
            font-family: 'Arial', sans-serif;
            position: relative;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }
        .container {
            position: relative;
            z-index: 2;
            margin-top: 50px;
            max-width: 900px;
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
        }
        h2 {
            margin-bottom: 30px;
            font-weight: bold;
            color: #ffffff;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        .table th, .table td {
            text-align: center;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .alert {
            margin-top: 20px;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .table-danger {
            background-color: rgba(248, 215, 218, 0.7) !important;
            color: #721c24 !important;
        }
        .table-success {
            background-color: rgba(212, 237, 218, 0.7) !important;
            color: #155724 !important;
        }
        .table-info {
            background-color: rgba(204, 229, 255, 0.7) !important;
            color: #004085 !important;
        }
        .comment-cell {
            font-weight: bold;
            font-style: italic;
        }
        .form-group label {
            font-weight: bold;
            color: #ffffff;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <h2 class="text-center">Student Report</h2>
        <form method="POST" action="report.php">
            <div class="form-group">
                <label for="id_annee">Select Academic Year:</label>
                <select class="form-control" id="id_annee" name="id_annee" required>
                    <option value="">Select Year</option>
                    <?php
                    $result = $conn->query("SELECT id_annee, annee FROM annees");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id_annee'] . '">' . $row['annee'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Fetch Report</button>
        </form>

        <?php if (isset($notes) && count($notes) > 0): ?>
            <div class="alert alert-info mt-4" role="alert">
                Report for the selected academic year.
            </div>
            <div class="alert alert-secondary">
                <strong>Student Name: <?= $student_name ?></strong><br>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>CC Note</th>
                            <th>Normal Note</th>
                            <th>Final Note</th>
                            
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_matiere = 0;
                        $passed = 0;
                        $failed = 0;
                        $total_note_final = 0;

                        foreach ($notes as $note): 
                            $total_matiere++;
                            $is_pass = $note['note_final'] >= 10;

                            if ($is_pass) {
                                $passed++;
                            } else {
                                $failed++;
                            }

                            // Accumulate total of final notes for average calculation
                            $total_note_final += $note['note_final'];

                            // Determine the row class based on note_final
                            $row_class = '';
                            if ($note['note_final'] < 10) {
                                $row_class = 'table-danger'; // Red for failed
                            } elseif ($note['note_final'] > 10) {
                                $row_class = 'table-info'; // Blue for passed
                            }

                            // Add comments based on note_final value
                            $comment = '';
                            if ($note['note_final'] == 0) {
                                $comment = 'Vous êtes très faible, redoublez d\'effort.';
                            } elseif ($note['note_final'] == 20) {
                                $comment = 'Félicitations!';
                            } elseif ($note['note_final'] > 15) {
                                $comment = 'Très bien.';
                            } elseif ($note['note_final'] == 10) {
                                $comment = 'Passable.';
                            } elseif ($note['note_final'] > 10 && $note['note_final'] <= 15) {
                                $comment = 'Bien.';
                            }
                        ?>
                            <tr class="<?= $row_class ?>">
                                <td><?= $note['nom_matiere'] ? $note['nom_matiere'] : $note['nom_matiere_commune'] ?></td>
                                
                                <td><?= $note['cc_note'] ?? 'N/A' ?></td>
                                <td><?= $note['normal_note'] ?? 'N/A' ?></td>
                                <td><?= $note['note_final'] ?? 'N/A' ?></td>
                               
                                <td class="comment-cell"><?= $comment ?></td> <!-- Display the comment here -->
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php
                // Calculate the average note_final /20
                $average_note_final = $total_note_final / $total_matiere;
            ?>
            <div class="alert alert-secondary">
                <strong>Total matières et matières communes: <?= $total_matiere ?></strong><br>
                <strong>Nombre de matières passées: <?= $passed ?></strong><br>
                <strong>Nombre de matières rattrapées: <?= $failed ?></strong><br>
                <strong>Moyenne générale: <?= number_format($average_note_final, 2) ?> / 20</strong> <!-- Display average -->
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="alert alert-warning mt-4" role="alert">
                No records found for the selected academic year.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>