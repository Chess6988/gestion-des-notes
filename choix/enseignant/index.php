<?php
session_start();

if (!isset($_SESSION['firstName_enseignant']) || !isset($_SESSION['lastName_enseignant'])) {
    // If the session variables are not set, redirect to the login page
    header("Location: signin_enseignant.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f5;
            background-image: linear-gradient(135deg, #1e3c72, #2a5298), url("ime.jpg");
            background-repeat: no-repeat;
            background-size: cover;
            color: #333;
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
        .content {
            padding: 40px;
            margin-left: 220px;
            transition: margin-left 0.3s ease-in-out;
        }
        .content h1 {
            font-size: 36px;
            color: white;
            margin-bottom: 20px;
            font-weight: 900;
        }
        .content p {
            font-size: 18px;
            color: white;
        }
        .btn-custom {
            background-color: #ffcc00;
            color: #1e282c;
            border-radius: 4px;
            font-weight: bold;
        }
        .btn-custom:hover {
            background-color: #ff9900;
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
</head>
<body>
    <div class="menu-icon" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <nav class="sidebar active">
        <div class="sidebar-sticky pt-3 text-center">
            <h2>Tableau de Bord</h2>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="matieres.php"><i class="fas fa-book"></i> Mes Matières</a>
                </li>
            
            </ul>
        </div>
    </nav>
    <div class="content">
        <div class="text-center mt-5">
            <h1>Welcome, <?php echo $_SESSION['firstName_enseignant'] . ' ' . $_SESSION['lastName_enseignant']; ?> sur votre Tableau de Bord</h1>
            <p>Sélectionnez une option dans la barre latérale pour commencer.</p>
        </div>
    </div>
    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
</body>
</html>
