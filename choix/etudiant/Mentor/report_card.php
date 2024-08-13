<?php
session_start();
require_once 'db_connect.php';

// Verify if the student's ID is set in the session
if (!isset($_SESSION['userId_etudiant'])) {
    echo "Student ID is not set in session. Please create an account first.";
    exit();
}

$id_etudiant = $_SESSION['userId_etudiant'];
$notes = [];
$student_name = '';
$selected_annee = '';
$selected_filiere = ''; // Added variable to store selected filiere

// Fetch available academic years
$annees = [];
$annee_stmt = $conn->prepare("SELECT id_annee, annee FROM annees");
$annee_stmt->execute();
$annee_result = $annee_stmt->get_result();
while ($row = $annee_result->fetch_assoc()) {
    $annees[] = $row;
}
$annee_stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_annee'])) {
    $id_annee = $_POST['id_annee'];

    // Fetch notes based on id_etudiant and id_annee
    $stmt = $conn->prepare("
        SELECT n.*, 
               IFNULL(m.nom_matiere, mc.nom_matiere_commune) AS matiere_name,
               IFNULL(m.courseCode, mc.courseCode) AS course_code,
               IFNULL(f1.nom_filiere, f2.nom_filiere) AS nom_filiere,
               s.nom_semestre, niv.nom_niveau,
               e.firstName_etudiant, e.lastName_etudiant,
               a.annee
        FROM notes n 
        LEFT JOIN matieres m ON n.id_matiere = m.id_matiere 
        LEFT JOIN matieres_communes mc ON n.id_matiere_commune = mc.id_matiere_commune 
        LEFT JOIN filieres f1 ON m.id_filiere = f1.id_filiere 
        LEFT JOIN filieres f2 ON mc.id_filiere = f2.id_filiere 
        LEFT JOIN semestres s ON m.id_semestre = s.id_semestre OR mc.id_semestre = s.id_semestre 
        LEFT JOIN niveaux niv ON m.id_niveau = niv.id_niveau OR mc.id_niveau = niv.id_niveau 
        LEFT JOIN etudiants e ON n.id_etudiant = e.id_etudiant 
        LEFT JOIN annees a ON n.id_annee = a.id_annee 
        WHERE n.id_etudiant = ? AND n.id_annee = ?
    ");
    $stmt->bind_param("ii", $id_etudiant, $id_annee);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
        $student_name = $row['firstName_etudiant'] . ' ' . $row['lastName_etudiant'];
        $selected_annee = $row['annee'];
        $selected_filiere = $row['nom_filiere']; // Store nom_filiere
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultat Final</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f4f9;
        color: #333;
        margin: 0;
        padding: 20px;
    }

    .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        max-width: 900px;
        margin: auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-content img {
        max-width: 100px;
    }

    .header-content div {
        text-align: center;
    }

    .header-content p {
        margin: 0;
        font-weight: bold;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .table th {
        background-color: #08387F;
        color: #fff;
    }

    .odd-row {
        background-color: #f2f2f2;
    }

    .final-mark {
        color: red;
        font-weight: bold;
    }

    .signature-section {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .signature {
        text-align: center;
    }

    .signature p {
        margin-bottom: 60px; /* Space for the signature */
        border-bottom: 1px solid #000;
        width: 250px;
        margin-left: auto;
        margin-right: auto;
    }

    .signature h6 {
        margin: 0;
    }

    .average-score {
        text-align: right;
        margin-top: 20px;
        font-weight: bold;
    }

    .print-button {
        text-align: center;
        margin-top: 20px;
    }

    .print-button button {
        background-color: #08387F;
        color: #fff;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .print-button button:hover {
        background-color: #002b5b;
    }

    /* Add media query for small screens */
    @media (max-width: 768px) {
        .signature-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .signature {
            width: 100%;
            margin-bottom: 20px;
        }

        .signature p {
            width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form to select academic year -->
        <form method="POST" action="" id="anneeForm" style="<?= isset($_POST['id_annee']) ? 'display:none;' : '' ?>">
            <div class="mb-3">
                <label for="id_annee" class="form-label">Select Academic Year</label>
                <select class="form-select" id="id_annee" name="id_annee" required>
                    <option value="">Choose...</option>
                    <?php foreach ($annees as $annee): ?>
                        <option value="<?= $annee['id_annee'] ?>"><?= $annee['annee'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <!-- Display student results -->
        <div class="header-content">
            <div class="left-info">
                <p><strong>Name:</strong> <?= $student_name ?></p>
                <p><strong>Filière choisie:</strong> <?= $selected_filiere ?? '' ?></p>
                <p><strong>Niveau:</strong> <?= $notes[0]['nom_niveau'] ?? '' ?></p>
            </div>
            <div class="center-info">
                <img src="logo-ime-p.png" alt="University Logo" id="university-logo">
            </div>
            <div class="right-info">
                <p><strong>Semestre:</strong> <?= $notes[0]['nom_semestre'] ?? '' ?></p>
                <p><strong>Année académique:</strong> <?= $selected_annee ?></p>
                <p><strong>Date:</strong> <?= date('Y-m-d') ?></p>
            </div>
        </div>
        
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Liste des Matières</th>
                        <th>CC notes</th>
                        <th>Normal notes</th>
                        <th>Note finale</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    $count = 0;
                    if (isset($notes) && count($notes) > 0): ?>
                        <?php foreach ($notes as $index => $note): 
                            $total += $note['note_final'];
                            $count++;
                        ?>
                            <tr class="<?= $index % 2 == 0 ? 'odd-row' : '' ?>">
                                <td><?= $index + 1 ?></td>
                                <td><?= $note['matiere_name'] ?></td>
                                <td><?= $note['cc_note'] ?></td>
                                <td><?= $note['normal_note'] ?></td>
                                <td class="final-mark"><?= $note['note_final'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No results available for this year.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Average score section -->
        <?php if ($count > 0): ?>
            <div class="average-score">
                <p>Moyenne: <?= number_format($total / $count, 2) ?>/20</p>
            </div>
        <?php endif; ?>

        <div class="signature-section">
            <div class="signature">
                <p></p>
                <h6>Signature de l'étudiant</h6>
            </div>
            <div class="signature">
                <p></p>
                <h6>Signature du Directeur</h6>
            </div>
            <div class="signature">
                <p></p>
                <h6>Signature du Chef de département</h6>
            </div>
        </div>

        <!-- Print button -->
        <div class="print-button">
            <button onclick="printPDF()">Print Results</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        function preloadImage(url) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = url;
            });
        }

        async function printPDF() {
            const printButton = document.querySelector('.print-button');

            // Hide print button before generating PDF
            printButton.style.display = 'none';

            try {
                // Preload logo image if necessary
                const logoImage = await preloadImage('logo-ime-p.png');
                document.getElementById('university-logo').src = logoImage.src;

                // Select the element to convert to PDF
                const element = document.querySelector('.container');

                // Generate the PDF
                await html2pdf().from(element).save('resultat_final.pdf');
            } catch (error) {
                console.error('Error generating PDF:', error);
            } finally {
                // Show print button after generating PDF
                printButton.style.display = 'block';
            }
        }
    </script>
</body>
</html>
