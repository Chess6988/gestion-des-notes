<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user'])) {
    header('Location: signin_superadminadmin.html');
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un compte (SuperAdmin)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Modifier un compte (SuperAdmin)</h5>
                <form id="modifyForm"  method="post" action="modify_info_superadmin.php">
                    <div class="form-group">
                        <label for="firstName_superadmin">First Name (Nom)</label>
                        <input type="text" class="form-control" id="firstName_superadmin" name="firstName_superadmin" value=" <?php echo htmlspecialchars($user['firstName_superadmin']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName_superadmin">Last Name (Prénom)</label>
                        <input type="text" class="form-control" id="lastName_superadmin" name="lastName_superadmin" value="<?php echo htmlspecialchars($user['lastName_superadmin']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">New Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Mettre à jour les informations</button>
                </form><br>
                <p class="mt-3" style="color:red;">Cliquez ici <a href="signin_superadmin.html">Login</a> si votre compte est OK</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Confirmation Messages -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirmationMessage">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modalRedirectBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#modifyForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'update_superadmin.php',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#confirmationMessage').text(response.message);
                            $('#confirmationModal').modal('show');

                            $('#modalRedirectBtn').click(function() {
                                window.location.href = 'signin_superadmin.html';
                            });
                        } else {
                            $('#confirmationMessage').text(response.message);
                            $('#confirmationModal').modal('show');
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#confirmationMessage').text('An error occurred. Please try again.');
                        $('#confirmationModal').modal('show');
                    }
                });
            });
        });
    </script>
</body>
</html>
