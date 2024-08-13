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

// Function to fetch the current profile details
function getProfileDetails($id_etudiant) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM profile_etudiant WHERE id_etudiant = ?");
    $stmt->bind_param("i", $id_etudiant);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();
    return $profile;
}

$profileDetails = getProfileDetails($id_etudiant);
if (!$profileDetails) {
    echo "No profile found. Please complete your profile first.";
    exit();
}

// Fetch the existing values for the form
$id_filiere = $profileDetails['id_filiere'];
$id_niveau = $profileDetails['id_niveau'];
$id_semestre = $profileDetails['id_semestre'];
$id_annee = $profileDetails['id_annee'];
$selected_matieres = [];
$selected_matieres_communes = [];

$matieres_result = $conn->query("SELECT id_matiere FROM matieres_etudiants WHERE id_etudiant = $id_etudiant");
while ($row = $matieres_result->fetch_assoc()) {
    $selected_matieres[] = $row['id_matiere'];
}

$matieres_communes_result = $conn->query("SELECT id_matiere_commune FROM matieres_communes_etudiants WHERE id_etudiant = $id_etudiant");
while ($row = $matieres_communes_result->fetch_assoc()) {
    $selected_matieres_communes[] = $row['id_matiere_commune'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Your Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 50px;
        }
        .btn-custom {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
            border-radius: 30px;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        .btn-custom:hover {
            background-color: blue;
            border-color: #0056b3;
            transform: scale(1.05);
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            color: #fff;
            border-radius: 30px;
            transition: background-color 0.3s, transform 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        .btn-success:hover {
            background-color: red;
            border-color: #218838;
            transform: scale(1.05);
        }
        .form-group label {
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 16px;
        }
        .card-title {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
    <script>
        function fetchOptions() {
            var filiere = document.getElementById('filiere').value;
            var semestre = document.getElementById('semestre').value;
            var niveau = document.getElementById('niveau').value;
            if (filiere && semestre && niveau) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'fetch_edit_options.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        document.getElementById('matieres').innerHTML = response.matieres;
                        document.getElementById('matieres_communes').innerHTML = response.matieres_communes;

                        // Check previously selected checkboxes
                        <?php foreach ($selected_matieres as $id_matiere): ?>
                        document.querySelector('input[name="matieres[]"][value="<?php echo $id_matiere; ?>"]').checked = true;
                        <?php endforeach; ?>
                        <?php foreach ($selected_matieres_communes as $id_matiere_commune): ?>
                        document.querySelector('input[name="matieres_communes[]"][value="<?php echo $id_matiere_commune; ?>"]').checked = true;
                        <?php endforeach; ?>
                    }
                };
                xhr.send('filiere=' + filiere + '&semestre=' + semestre + '&niveau=' + niveau);
            }
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

        window.onload = function() {
            fetchOptions();
        };
    </script>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Editer Votre Profil</h3>
            <form id="profileForm" action="submit_edit_profile.php" method="post" onsubmit="return validateForm()">
                <div class="form-group">
                    <label for="filiere">Filière</label>
                    <select class="form-control" id="filiere" name="filiere" onchange="fetchOptions()">
                        <option value="">Sélectionnez une Filière</option>
                        <?php
                        $result = $conn->query("SELECT id_filiere, nom_filiere FROM filieres");
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['id_filiere'] == $id_filiere ? "selected" : "";
                            echo "<option value=\"{$row['id_filiere']}\" $selected>{$row['nom_filiere']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="semestre">Semestre</label>
                    <select class="form-control" id="semestre" name="semestre" onchange="fetchOptions()">
                        <option value="">Sélectionnez un Semestre</option>
                        <?php
                        $result = $conn->query("SELECT id_semestre, nom_semestre FROM semestres");
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['id_semestre'] == $id_semestre ? "selected" : "";
                            echo "<option value=\"{$row['id_semestre']}\" $selected>{$row['nom_semestre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="niveau">Niveau</label>
                    <select class="form-control" id="niveau" name="niveau" onchange="fetchOptions()">
                        <option value="">Sélectionnez un Niveau</option>
                        <?php
                        $result = $conn->query("SELECT id_niveau, nom_niveau FROM niveaux");
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['id_niveau'] == $id_niveau ? "selected" : "";
                            echo "<option value=\"{$row['id_niveau']}\" $selected>{$row['nom_niveau']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="annee">Année</label>
                    <select class="form-control" id="annee" name="annee">
                        <option value="">Sélectionnez une Année</option>
                        <?php
                        $result = $conn->query("SELECT id_annee, annee FROM annees");
                        while ($row = $result->fetch_assoc()) {
                            $selected = $row['id_annee'] == $id_annee ? "selected" : "";
                            echo "<option value=\"{$row['id_annee']}\" $selected>{$row['annee']}</option>";
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
                Profile updated successfully.
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
                Please select all options and check all checkboxes.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
