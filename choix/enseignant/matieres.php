<?php
session_start();
require 'db_connect.php';

$conn = getDbConnection();
$id_enseignant = $_SESSION['id_enseignant']; // assuming you store the enseignant's ID in the session

// Fetch matiere and matiere_commune chosen by enseignant
$sql = "SELECT 
            pe.id_matiere, m.nom_matiere, 
            pe.id_matiere_commune, mc.nom_matiere_commune, 
            pe.id_annee, a.annee
        FROM profile_enseignant pe
        LEFT JOIN matieres m ON pe.id_matiere = m.id_matiere
        LEFT JOIN matieres_communes mc ON pe.id_matiere_commune = mc.id_matiere_commune
        LEFT JOIN annees a ON pe.id_annee = a.id_annee
        WHERE pe.id_enseignant = ? AND pe.validated = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_enseignant);
$stmt->execute();
$result = $stmt->get_result();

$matieres = [];
while ($row = $result->fetch_assoc()) {
    $matieres[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 800px;
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 30px;
        }
        .list-group-item {
            border: none;
            border-radius: 5px;
            background-color: #e9ecef;
            margin-bottom: 10px;
            padding: 15px;
            transition: background-color 0.3s ease;
        }
        .list-group-item:hover {
            background-color: #dee2e6;
        }
        .btn-info {
            background-color: blue;
            border-color: red;
            border-radius: 20px;
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            border-bottom: none;
        }
        .modal-title {
            font-weight: bold;
            color: #343a40;
        }
        .btn-primary {
            border-radius: 20px;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            border-radius: 20px;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn i {
            margin-right: 5px;
        }
        /* Add this for responsiveness */
        .list-group {
            overflow-x: auto;
        }
        .sidebar {
            height: 100vh;
            background: blue;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 1000;
            width: 220px;
            box-shadow: 3px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar h2 {
            font-size: 24px;
            margin: 20px;
            color: #ffcc00;
            font-weight: bold;
        }
        .sidebar .nav-link {
            font-size: 18px;
            margin: 10px 0;
            color: white;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
            color: #ffcc00;
        }
        .sidebar .nav-link:hover {
            background-color: #1e282c;
            border-radius: 4px;
        }
        .menu-icon {
            display: none;
            position: absolute;
            top: 15px;
            left: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #ffcc00;
            z-index: 1001;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .content {
                margin-left: 0;
            }
            .menu-icon {
                display: block;
            }
            
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="menu-icon" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <nav class="sidebar active">
        <div class="sidebar-sticky pt-3 text-center"><br>
            
            <ul class="nav flex-column">
            <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-book"></i> Page d'acceuil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="active"><i class="fas fa-book"></i> Mes Matières</a>
                </li>
            
            </ul>
        </div>
    </nav>
<div class="container">
    <h2>Manage Subjects</h2>
    <div class="list-group">
        <!-- Loop through subjects and display them -->
        <?php foreach ($matieres as $matiere): ?>
            <?php if ($matiere['id_matiere']): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-book"></i> <strong>Matiere:</strong> <?= $matiere['nom_matiere'] ?></span>
                    <button class="btn btn-info btn-sm show-students-btn" data-matiere-id="<?= $matiere['id_matiere'] ?>" data-annee-id="<?= $matiere['id_annee'] ?>">
                        <i class="fas fa-users"></i> Show Students
                    </button>
                </div>
            <?php endif; ?>
            <?php if ($matiere['id_matiere_commune']): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-book-reader"></i> <strong>Matiere Commune:</strong> <?= $matiere['nom_matiere_commune'] ?></span>
                    <button class="btn btn-info btn-sm show-students-btn" data-matiere-commune-id="<?= $matiere['id_matiere_commune'] ?>" data-annee-id="<?= $matiere['id_annee'] ?>">
                        <i class="fas fa-users"></i> Show Students
                    </button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <button>
            <a href="index.php">Retour accueil</a>
        </button>
    </div>
</div>
</body>
</html>


<!-- Modal to display students -->
<div class="modal fade" id="studentsModal" tabindex="-1" role="dialog" aria-labelledby="studentsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentsModalLabel"><i class="fas fa-user-graduate"></i> Your Students who registered this subjects  </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="studentsForm">
                    <div id="studentsList"></div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary mt-3" id="submitGradesBtn">
                            <i class="fas fa-save"></i> Submit Grades
                        </button>
                        <button type="button" class="btn btn-secondary mt-3" id="editGradesBtn">
                            <i class="fas fa-edit"></i> Edit Grades
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>


<script>


function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

$(document).ready(function() {
    $('.show-students-btn').on('click', function() {
        var matiereId = $(this).data('matiere-id');
        var matiereCommuneId = $(this).data('matiere-commune-id');
        var anneeId = $(this).data('annee-id');
        var data = {};

        if (matiereId) {
            data = { id_matiere: matiereId, id_annee: anneeId };
        } else if (matiereCommuneId) {
            data = { id_matiere_commune: matiereCommuneId, id_annee: anneeId };
        }

        $.ajax({
            type: 'POST',
            url: 'fetch_students.php',
            data: data,
            success: function(response) {
                try {
                    var responseText = typeof response === "string" ? response : JSON.stringify(response);
                    var students = JSON.parse(responseText);
                    var studentsList = $('#studentsList');
                    studentsList.empty();

                    students.forEach(function(student) {
    var cc_note = student.cc_note || '';
    var normal_note = student.normal_note || '';
    var note_final = student.note_final || '';

    // Only show the "Rattraper" message if the final note is less than 10 and has been set
    var rattraperMessage = (note_final && note_final < 10) ? '<span class="text-danger"> - Rattraper</span>' : '';

    var message = student.duplicate
        ? `<div class="alert alert-warning">Déjà attribué: ${cc_note} (CC), ${normal_note} (Normal), ${note_final} (Final)</div>`
        : '';

    var inputFields = `
    
        <div class="form-group">
        
            <label>${student.firstName_etudiant} ${student.lastName_etudiant}${rattraperMessage}</label>
            <input type="hidden" name="id_etudiant[]" value="${student.id_etudiant}">
            <input type="hidden" name="id_matiere[]" value="${matiereId || ''}">
            <input type="hidden" name="id_matiere_commune[]" value="${matiereCommuneId || ''}">
            <input type="hidden" name="id_annee[]" value="${anneeId}">
            <input type="number" name="cc_note[]" class="form-control" value="${cc_note}" placeholder="CC Note" min="0" max="20" required>
            <input type="number" name="normal_note[]" class="form-control" value="${normal_note}" placeholder="Normal Note" min="0" max="20" required>
            <input type="number" name="note_final[]" class="form-control" value="${note_final}" placeholder="Final Note" readonly>
            <div class="bootstrap-message" id="message-${student.id_etudiant}">${message}</div>
            <button type="button" class="btn btn-secondary edit-grades-btn mt-2" data-id_etudiant="${student.id_etudiant}">Edit</button>
        </div>
    `;
    studentsList.append(inputFields);
});


                    $('#studentsModal').modal('show');
                } catch (e) {
                    console.error("Error parsing response: ", e);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
            }
        });
    });

    $('#submitGradesBtn').on('click', function(e) {
        e.preventDefault();
        var formData = $('#studentsForm').serialize();

        $.ajax({
            type: 'POST',
            url: 'register_notes.php',
            data: formData,
            success: function(response) {
                try {
                    var responseText = typeof response === "string" ? response : JSON.stringify(response);
                    var data = JSON.parse(responseText);

                    if (data.status === 'success') {
                        data.messages.forEach(function(message) {
                            var studentMessage = message.duplicate
                                ? `<div class="alert alert-warning">Déjà attribué: ${message.cc_note} (CC), ${message.normal_note} (Normal), ${message.note_final} (Final)</div>`
                                : `<div class="alert alert-success">Registered: ${message.cc_note} (CC), ${message.normal_note} (Normal), ${message.note_final} (Final)</div>`;
                            $(`#message-${message.id_etudiant}`).html(studentMessage);
                        });
                    }
                } catch (e) {
                    console.error("Error parsing response: ", e);
                }
            }
        });
    });

    $('#editGradesBtn').on('click', function(e) {
        e.preventDefault();
        var formData = $('#studentsForm').serialize();

        $.ajax({
            type: 'POST',
            url: 'update_notes.php',
            data: formData,
            success: function(response) {
                try {
                    var responseText = typeof response === "string" ? response : JSON.stringify(response);
                    var data = JSON.parse(responseText);

                    if (data.status === 'success') {
                        data.messages.forEach(function(message) {
                            var rattraperMessage = message.note_final < 10
                                ? '<div class="alert alert-danger">Rattraper encore</div>'
                                : '<div class="alert alert-success">A passer le rattrapage</div>';
                            var studentMessage = `<div class="alert alert-success">Updated: ${message.cc_note} (CC) and ${message.normal_note} (Normal)</div>${rattraperMessage}`;
                            $(`#message-${message.id_etudiant}`).html(studentMessage);
                        });
                    }
                } catch (e) {
                    console.error("Error parsing response: ", e);
                }
            }
        });
    });
});
</script>
</body>
</html>
