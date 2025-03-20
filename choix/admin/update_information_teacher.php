<?php
session_start();

if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) { 
    header("Location: signin_admin.php"); 
    exit();
}

require 'db_connect.php';
$conn = getDbConnection();

if (!$conn) {
    die("Database connection failed.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Teacher Information</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <style>
  body { 
    margin-top: 30px; 
}

.form-section { 
    margin-bottom: 30px; 
}

.text-right { 
    text-align: right !important; 
}

/* Ensure the modal content is scrollable on small screens */
.modal-body {
    overflow-x: auto; /* Enable horizontal scrolling */
    max-height: 400px; /* Set a max height to prevent overflow */
    padding: 10px; /* Adjust padding for better layout */
}

/* Ensure the table fits within the modal */
.modal-body table {
    width: 100%; /* Ensure table does not exceed modal width */
    table-layout: fixed; /* Ensures columns stay inside the modal */
    display: block;
    overflow-x: auto;
}

/* Adjust buttons inside the modal */
.modal-body table td {
    white-space: nowrap; /* Prevent text wrapping */
    text-align: center; /* Center align text for readability */
}

/* Reduce button size */
.modal-body table td .btn {
    width: auto; /* Allow button to shrink dynamically */
    max-width: 85px; /* Reduce max-width to ensure buttons fit */
    padding: 3px 5px; /* Reduce padding */
    font-size: 12px; /* Make text slightly smaller */
    display: inline-block; /* Keep buttons inside the table cell */
    margin: 2px; /* Add spacing between buttons */
}

/* Special fix for Supprimer button */
.modal-body table td .btn-supprimer-matiere {
    max-width: 75px; /* Reduce Supprimer button width */
    font-size: 11px; /* Reduce font size for better fit */
    padding: 2px 4px; /* Reduce padding */
    white-space: nowrap; /* Prevent text from breaking */
}

/* Ensure table columns do not overflow */
@media screen and (max-width: 768px) {
    .modal-dialog {
        max-width: 95%; /* Ensure modal fits within mobile screens */
    }
    
    .modal-body {
        padding: 10px;
    }

    /* Stack buttons vertically on small screens */
    .modal-body table td {
        display: block;
        text-align: center;
    }

    /* Further reduce button size for small screens */
    .modal-body table td .btn {
        width: auto; /* Allow flexibility */
        max-width: 75px; /* Reduce button width */
        font-size: 11px; /* Smaller font for mobile */
        padding: 2px 4px; /* Reduce padding */
    }
}


    
  </style>
</head>
<body>
<div class="container">
  <h2 class="mb-4">Update Teacher Information</h2>

  <!-- Academic Year Selection -->
  <div id="selectionForm" class="form-section">
    <form id="yearTeacherForm">
      <div class="form-group">
        <label for="id_annee">Select Academic Year</label>
        <select class="form-control" id="id_annee" name="id_annee" required>
          <option value="">Select Year</option>
          <?php
            $sql = "SELECT id_annee, annee FROM annees";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id_annee']}'>{$row['annee']}</option>";
                }
            }
            $conn->close();
          ?>
        </select>
      </div>
      <button type="button" id="viewTeachers" class="btn btn-info">View Teachers</button>
    </form>
  </div>

  <!-- Teacher Table -->
  <div class="table-responsive mt-4">
    <table class="table table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>Teacher Name</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody id="teacherTableBody">
        <!-- Populated dynamically -->
      </tbody>
    </table>
  </div>

  <!-- Bootstrap Modal for Matières -->
  <div class="modal fade" id="matiereModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Liste des Matières</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Nom Matière</th>
                <th>Course Code</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="matiereTableBody">
              <!-- Dynamically populated -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap Toast Container -->
<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 9999;">
    <div id="toastContainer"></div>
</div>

</div>

<script>
$(document).ready(function() {
    // Fetch teachers when 'View Teachers' is clicked
    $('#viewTeachers').click(function() {
    var id_annee = $('#id_annee').val();
    if (id_annee === "") {
        showToast("Please select an academic year first.", "warning");
        return;
    }

    $.ajax({
        type: 'GET',
        url: 'get_teachers.php',
        data: { id_annee: id_annee },
        dataType: 'json',
        success: function(response) {
            // ✅ Check if response contains valid data and is not an error
            if (response && Array.isArray(response) && response.length > 0) {
                var tableHtml = '';
                $.each(response, function(i, teacher) {
                    tableHtml += `
                        <tr>
                            <td>${teacher.firstName_enseignant} ${teacher.lastName_enseignant}</td>
                            <td class="text-right">
                                <button class="btn btn-primary btn-voir-matieres" 
                                        data-enseignant-id="${teacher.id_enseignant}" 
                                        data-annee-id="${id_annee}">
                                    Voir ses matières
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#teacherTableBody').html(tableHtml);
            } 
            // ✅ Show toast only if no teachers were found
            else if (response && response.length === 0) {
                showToast("Aucun Enseignant ont enregistrez les matieres en cette annee.", "warning");
                $('#teacherTableBody').html('');
            } 
            // ✅ Handle unexpected errors in response
            else {
                showToast("Unexpected error occurred. Please try again.", "danger");
                $('#teacherTableBody').html('');
            }
        },
        error: function(xhr, status, error) {
            showToast("AJAX Error: " + error, "danger");
        }
    });
});


    // Fetch subjects when 'Voir ses matières' is clicked
    $(document).on('click', '.btn-voir-matieres', function() {
        var id_enseignant = $(this).data('enseignant-id');
        var id_annee = $(this).data('annee-id');

        $.ajax({
            type: 'GET',
            url: 'get_matiere_for_teacher.php',
            data: { id_enseignant: id_enseignant, id_annee: id_annee },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert("Error: " + response.error);
                    return;
                }

                var matiereHtml = "";

                // ✅ Fix: Ensure correct ID and Type for Matieres
                response.matieres.forEach(function(matiere) {
    console.log("🛠 CHECKING MATIERE:", matiere);  // ✅ Debug matiere object

    matiereHtml += `
        <tr data-profile-id="${matiere.id_profile_enseignant}" data-matiere-id="${matiere.id_matiere}">
            <td>${matiere.nom_matiere}</td>
            <td>${matiere.courseCode}</td>
            <td>
                <button class="btn btn-warning btn-sm btn-edit-matiere"
                        data-matiere-type="matiere">
                    Edit
                </button>
                <button class="btn btn-danger btn-sm btn-supprimer-matiere" 
                        data-profile-id="${matiere.id_profile_enseignant}" 
                        data-matiere-id="${matiere.id_matiere}"  
                        data-matiere-type="matiere">
                    Supprimer
                </button>
            </td>
        </tr>
    `;
});



                // ✅ Fix: Use Correct `id_matiere_commune` for matieres_communes
                response.matieresCommunes.forEach(function(matiere) {
    console.log("🛠 CHECKING COMMUNE MATIERE:", matiere);  // ✅ Debugging

    matiereHtml += `
        <tr data-profile-id="${matiere.id_profile_enseignant}" data-matiere-id="${matiere.id_matiere_commune}">
            <td>${matiere.nom_matiere} (Commune)</td>
            <td>${matiere.courseCode}</td>
            <td>
                <button class="btn btn-warning btn-sm btn-edit-matiere"
                        data-matiere-type="matiere_commune">
                    Edit
                </button>
                <button class="btn btn-danger btn-sm btn-supprimer-matiere" 
                        data-profile-id="${matiere.id_profile_enseignant}" 
                        data-matiere-id="${matiere.id_matiere_commune}"  
                        data-matiere-type="matiere_commune">
                    Supprimer
                </button>
            </td>
        </tr>
    `;
});


                if (matiereHtml === "") {
                    matiereHtml = '<tr><td colspan="3" class="text-center">No Data Available</td></tr>';
                }

                $('#matiereTableBody').html(matiereHtml);

                // ✅ Fix: Remove existing "Add" button before adding new one
                $('.add-matiere-btn').remove();

                // Add single "Add Matière" button at the bottom
                $('#matiereModal .modal-body').append(`
                    <div class="text-right mt-3 add-matiere-btn">
                        <button class="btn btn-success" id="addMatiere">Add Matière</button>
                    </div>
                `);

                $('#matiereModal').modal('show');
            },
            error: function(xhr, status, error) {
                alert("AJAX Error: " + error);
            }
        });
    });


    // ✅ Fix: Handle 'Supprimer' button click & ensure correct ID is sent
    $(document).on('click', '.btn-supprimer-matiere', function() {
    console.log("🛠 DELETE BUTTON CLICKED. FULL HTML: ", $(this)[0].outerHTML);  // ✅ Logs full button HTML

    var matiereId = $(this).attr('data-matiere-id');
    var profileId = $(this).attr('data-profile-id');
    var matiereType = $(this).attr('data-matiere-type');
    var row = $(this).closest('tr');

    console.log("🛠 Captured IDs: matiereId =", matiereId, "| profileId =", profileId, "| Type =", matiereType); 

    if (!matiereId || matiereId.trim() === "" || matiereId === "undefined") {
        console.error("🚨 ERROR: The delete button is missing 'data-matiere-id'.");
        showToast("Error: Unable to delete subject. Data missing.", "danger");
        return;
    }

    matiereId = parseInt(matiereId, 10);
    if (isNaN(matiereId) || matiereId <= 0) {
        console.error("🚨 ERROR: Invalid subject ID.");
        showToast("Error: Invalid subject ID.", "danger");
        return;
    }

    if (!confirm("Are you sure you want to delete this subject?")) {
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'delete_matiere.php',
        data: { id_matiere: matiereId, matiere_type: matiereType },
        dataType: 'json',
        success: function(response) {
            console.log("🔄 DELETE RESPONSE:", response);
            if (response.success) {
                row.fadeOut(500, function() { $(this).remove(); });
                showToast("Subject deleted successfully!", "success");
            } else {
                showToast("Error deleting subject: " + response.error, "danger");
            }
        },
        error: function(xhr, status, error) {
            showToast("AJAX Error: " + error, "danger");
        }
    });
});

    // ✅ Fix: Handle 'Edit' button click
    $(document).on('click', '.btn-edit-matiere', function() {
    var profileId = $(this).closest('tr').data('profile-id'); // ✅ Fixed
    var matiereType = $(this).data('matiere-type');

    console.log("Fetching data for profile ID:", profileId, "Matiere Type:", matiereType); // Debugging

    $.ajax({
        type: 'GET',
        url: 'edit_matieres.php',
        data: { id_profile_enseignant: profileId, matiere_type: matiereType },
        dataType: 'json',
        success: function(response) {
            console.log("Response from server:", response); // Debugging

            if (response.error) {
                showToast("Error fetching data: " + response.error, "danger");
                return;
            }

            // Populate dropdown in modal
            var optionsHtml = "";
            response.subjects.forEach(function(subject) {
                optionsHtml += `<option value="${subject.id}">${subject.name}</option>`;
            });

            $('#editMatiereDropdown').html(optionsHtml);
            $('#editMatiereModal').modal('show');

            // Handle Save
            $('#saveEditMatiere').off('click').on('click', function() {
                var newMatiereId = $('#editMatiereDropdown').val();

                $.ajax({
                    type: 'POST',
                    url: 'update_matieres.php',
                    data: { id_profile_enseignant: profileId, new_matiere_id: newMatiereId, matiere_type: matiereType },
                    dataType: 'json',
                    success: function(updateResponse) {
                        console.log("Update Response:", updateResponse); // Debugging

                        if (updateResponse.success) {
                            showToast("Subject updated successfully!", "success");
                            $('#editMatiereModal').modal('hide');
                        } else {
                            showToast("Error updating subject: " + updateResponse.error, "danger");
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast("AJAX Error: " + error, "danger");
                    }
                });
            });
        },
        error: function(xhr, status, error) {
            showToast("AJAX Error: " + error, "danger");
        }
    });
});

function showToast(message, type) {
    var toastId = "toast-" + Math.random().toString(36).substr(2, 5); // Unique ID
    var toastHtml = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    `;

    $('#toastContainer').append(toastHtml);
    var toastElement = $('#' + toastId);
    toastElement.toast({ delay: 10000 }); // 10 seconds
    toastElement.toast('show');

    setTimeout(function() {
        toastElement.fadeOut(500, function() {
            $(this).remove();
        });
    }, 10500); // Ensures toast fades out after 10.5 seconds
}


});
</script>


</body>
</html>
