<?php
session_start();

if (!isset($_SESSION['firstName_enseignant']) || !isset($_SESSION['lastName_enseignant'])) {
    header("Location: signin_enseignant.php");
    exit();
}

require 'db_connect.php';

$conn = getDbConnection();
$id_enseignant = $_SESSION['id_enseignant']; // assuming you store the enseignant's ID in the session

// Check if the profile is already validated
$sql = "SELECT validated FROM profile_enseignant WHERE id_enseignant = ? AND validated = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_enseignant);
$stmt->execute();
$stmt->store_result();

$profile_completed = $stmt->num_rows > 0;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Subjects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            font-weight: bold;
            color: #343a40;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group label {
            font-weight: bold;
            color: #343a40;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-danger a {
            color: inherit;
            text-decoration: none;
        }
        .form-control {
            border-radius: 5px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .alert {
            margin-top: 20px;
        }
        .alert .close {
            color: #000;
        }
        #subjectsResult h3 {
            font-weight: bold;
            color: #495057;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        #matieresList, #matieresCommunesList {
            list-style-type: none;
            padding-left: 0;
        }
        #matieresList li, #matieresCommunesList li {
            padding: 10px;
            background-color: #f8f9fa;
            margin-bottom: 5px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .modal-header, .modal-footer {
            background-color: #f1f3f5;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-book-open"></i> Generate Subjects</h2>
    <?php if ($profile_completed): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle"></i> Your profile is already completed.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <script>
            setTimeout(function() {
                document.querySelector('.alert').style.display = 'none';
            }, 5000);
        </script>
        <button type="button" class="btn btn-outline-danger mt-3">
            <a href="index.php"><i class="fas fa-home"></i> Retour à la page d'accueil</a>
        </button>
    <?php else: ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <script>
                setTimeout(function() {
                    document.querySelector('.alert').style.display = 'none';
                    document.getElementById('generateSubjectsForm').style.display = 'none';
                }, 5000);
            </script>
        <?php endif; ?>
        <form id="generateSubjectsForm">
            <div class="form-group">
                <label for="id_annee"><i class="fas fa-calendar-alt"></i> Select Year:</label>
                <select class="form-control" id="id_annee" name="id_annee" required>
                    <option value="">Select Year</option>
                    <?php
                    // Fetch years from the database
                    $conn = getDbConnection(); // Open connection again
                    $result = $conn->query("SELECT id_annee, annee FROM annees");
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['id_annee'] . '">' . $row['annee'] . '</option>';
                    }
                    $conn->close(); // Close the connection after fetching years
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-cogs"></i> Generate</button>
        </form>
        <form id="registerSubjectsForm" style="margin-top: 20px;">
            <div id="subjectsResult">
                <h3><i class="fas fa-book"></i> Matieres</h3>
                <input type="text" id="searchMatieres" class="form-control mb-3" placeholder="Search Matieres...">
                <ul id="matieresList"></ul>
                <h3><i class="fas fa-users"></i> Matieres Communes</h3>
                <input type="text" id="searchMatieresCommunes" class="form-control mb-3" placeholder="Search Matieres Communes...">
                <ul id="matieresCommunesList"></ul>
            </div>
            <input type="hidden" name="id_annee_hidden" id="id_annee_hidden">
            <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Register Subjects</button>
        </form>
    <?php endif; ?>
</div>

<!-- Bootstrap Modal for messages -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>


<script>
$(document).ready(function() {
    $('#generateSubjectsForm').on('submit', function(e) {
        e.preventDefault();
        var id_annee = $('#id_annee').val();
        $('#id_annee_hidden').val(id_annee); // Set the hidden field value

        $.ajax({
            type: 'POST',
            url: 'generate_subjects.php',
            data: { id_annee: id_annee },
            success: function(response) {
                var data = JSON.parse(response);
                var matieresList = $('#matieresList');
                var matieresCommunesList = $('#matieresCommunesList');

                matieresList.empty();
                matieresCommunesList.empty();

                $.each(data.matieres, function(index, matiere) {
                    matieresList.append('<div class="form-check"><input class="form-check-input" type="checkbox" name="matieres[]" value="' + matiere.id_matiere + '"><label class="form-check-label">' + matiere.nom_matiere + '</label></div>');
                });

                $.each(data.matieres_communes, function(index, matiere_commune) {
                    matieresCommunesList.append('<div class="form-check"><input class="form-check-input" type="checkbox" name="matieres_communes[]" value="' + matiere_commune.id_matiere_commune + '"><label class="form-check-label">' + matiere_commune.nom_matiere_commune + '</label></div>');
                });
            }
        });
    });

    $('#registerSubjectsForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: 'register_subjects.php',
            data: formData,
            success: function(response) {
                console.log("Response from register_subjects.php:", response);
                var data = JSON.parse(response);
                $('#messageContent').text(data.message);
                $('#messageModal').modal('show');
                if (data.status === 'success') {
                    setTimeout(function() {
                        $('#messageModal').modal('hide');
                        window.location.href = 'wait_for_validation.php';
                    }, 3000);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown);
            }
        });
    });

    // Search filter for Matieres
    $('#searchMatieres').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#matieresList div').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Search filter for Matieres Communes
    $('#searchMatieresCommunes').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#matieresCommunesList div').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>
</body>
</html>
