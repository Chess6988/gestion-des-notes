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

// Function to check if the profile is completed
function isProfileCompleted($id_etudiant) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM profile_etudiant WHERE id_etudiant = ?");
    $stmt->bind_param("i", $id_etudiant);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

$profileCompleted = isProfileCompleted($id_etudiant);
if ($profileCompleted) {
    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Profile Already Completed</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            body {
                background-color: #f0f8ff;
                font-family: 'Arial', sans-serif;
            }
            .card {
                border: none;
                border-radius: 15px;
                background-color: #fff;
                box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            }
            .btn-custom, .btn-warning {
                background-color: #007bff;
                border-color: #007bff;
                color: #fff;
                border-radius: 10px;
                transition: background-color 0.3s;
            }
            .btn-custom:hover, .btn-warning:hover {
                background-color: #0056b3;
                border-color: #0056b3;
            }
            .card-text {
                font-size: 22px;
                color: #333;
            }
            hr {
                border-top: 2px solid #007bff;
            }
        </style>
    </head>
    <body>
    <div class="container mt-5">
        <div class="card text-center">
            <div class="card-body">
                <p class="card-text">Vous avez déjà complété votre profil etudiant</p><br><hr>
                <p>
                    <a href="Mentor/index.html" class="btn btn-success"><i class="fas fa-door-open"></i> Accéder à la plateforme</a>
                </p>
                <p>
                    <a href="edit_profile.php" class="btn btn-primary"><i class="fas fa-edit"></i> Editer votre profil</a>

                </p>
                <p>
                    <a href="view_information.php" class="btn btn-outline-warning"><i class="fas fa-eye"></i> Regarder vos information ici</a>
                </p>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
} else {
    // Form to complete the profile
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Complétez Votre Profil</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            body {
                background-color: #f0f8ff;
                font-family: 'Arial', sans-serif;
            }
            .card {
                border: none;
                border-radius: 15px;
                background-color: #fff;
                box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
            }
            .btn-custom {
                background-color: #007bff;
                border-color: #007bff;
                color: #fff;
                border-radius: 10px;
                transition: background-color 0.3s;
            }
            .btn-custom:hover {
                background-color: #0056b3;
                border-color: #0056b3;
            }
            .btn-success {
                background-color: #28a745;
                border-color: #28a745;
                color: #fff;
                border-radius: 10px;
                transition: background-color 0.3s;
            }
            .btn-success:hover {
                background-color: #218838;
                border-color: #218838;
            }
            .form-group label {
                color: #333;
                font-size: 16px;
            }
            .form-control {
                border-radius: 10px;
            }
            .card-title {
                font-size: 26px;
                font-weight: bold;
                color: #007bff;
            }
            .card-body {
                padding: 30px;
            }
        </style>
        <script>
            function fetchOptions() {
                var filiere = document.getElementById('filiere').value;
                var semestre = document.getElementById('semestre').value;
                var niveau = document.getElementById('niveau').value;

                if (filiere && semestre && niveau) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'fetch_options.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            document.getElementById('matieres').innerHTML = response.matieres;
                            document.getElementById('matieres_communes').innerHTML = response.matieres_communes;
                        }
                    };
                    xhr.send('filiere=' + filiere + '&semestre=' + semestre + '&niveau=' + niveau);
                }
            }

            function checkAll() {
                var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                });
            }

            function validateForm() {
                var allChecked = true;
                var checkboxes = document.querySelectorAll('#matieres input[type="checkbox"], #matieres_communes input[type="checkbox"]');
                checkboxes.forEach(function(checkbox) {
                    if (!checkbox.checked) {
                        allChecked = false;
                    }
                });
                if (!allChecked) {
                    $('#errorModal').modal('show');
                    return false;
                } else {
                    $('#successModal').modal('show');
                    return true;
                }
            }
        </script>
    </head>
    <body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center">Complétez Votre Profil</h3>
                <form id="profileForm" action="submit_profile.php" method="post" onsubmit="return validateForm()">
                    <div class="form-group">
                        <label for="filiere">Filière</label>
                        <select class="form-control" id="filiere" name="filiere" onchange="fetchOptions()" required>
                            <option value="">Sélectionnez une Filière</option>
                            <?php
                            $result = $conn->query("SELECT id_filiere, nom_filiere FROM filieres");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"{$row['id_filiere']}\">{$row['nom_filiere']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semestre">Semestre</label>
                        <select class="form-control" id="semestre" name="semestre" onchange="fetchOptions()" required>
                            <option value="">Sélectionnez un Semestre</option>
                            <?php
                            $result = $conn->query("SELECT id_semestre, nom_semestre FROM semestres");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"{$row['id_semestre']}\">{$row['nom_semestre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="niveau">Niveau</label>
                        <select class="form-control" id="niveau" name="niveau" onchange="fetchOptions()" required>
                            <option value="">Sélectionnez un Niveau</option>
                            <?php
                            $result = $conn->query("SELECT id_niveau, nom_niveau FROM niveaux");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"{$row['id_niveau']}\">{$row['nom_niveau']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="annee">Année</label>
                        <select class="form-control" id="annee" name="annee" required>
                            <option value="">Sélectionnez une Année</option>
                            <?php
                            $result = $conn->query("SELECT id_annee, annee FROM annees");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value=\"{$row['id_annee']}\">{$row['annee']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" id="matieres">
                        <!-- Matieres will be populated here by fetchOptions -->
                    </div>
                    <div class="form-group" id="matieres_communes">
                        <!-- Matieres communes will be populated here by fetchOptions -->
                    </div>
                    <button type="button" class="btn  btn-outline-primary" onclick="checkAll()">
                        <i class="fas fa-check-circle"></i> Tout cocher
                    </button>
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-paper-plane"></i> Soumettre
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Profile submitted successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Please select all subjects before submitting.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
}
?>
