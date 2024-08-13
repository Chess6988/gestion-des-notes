<?php
session_start();
require_once 'db_connect.php';

// Check if the user is an admin
if (!isset($_SESSION['firstName_admin']) || !isset($_SESSION['lastName_admin'])) {
    header("Location: signin_admin.php");
    exit();
}

$conn = getDbConnection();

function fetchNewEntriesCount($conn) {
    $sql = "SELECT COUNT(*) as new_entries FROM profile_enseignant WHERE validated = 0";
    $result = $conn->query($sql);
    if ($result === false) {
        echo "Error: " . $conn->error;
        return null;
    }
    $row = $result->fetch_assoc();
    return $row['new_entries'];
}

$newEntriesCount = fetchNewEntriesCount($conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #c4cfdb;
            overflow-x: hidden;
        }
        .sidebar {
            height: 100%;
            width: 100%;
            background-color: rgb(6, 6, 17);
            color: white;
            position: fixed;
            top: 0;
            left: -100%;
            transition: left 0.3s ease-in-out;
            z-index: 1000;
            overflow-y: auto;
            font-weight: bolder;
        }
        .sidebar.active {
            left: 0;
        }
        .sidebar h2 {
            font-size: 24px;
            margin: 20px;
            color: #ffc107;
        }
        .sidebar .nav-link {
            font-size: 18px;
            margin: 10px 0;
            color: white;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            border-radius: 4px;
        }
        .content {
            padding: 20px;
            margin-left: 0;
            transition: margin-left 0.3s ease-in-out;
        }
        .content.active {
            margin-left: 0;
        }
        .content h1 {
            font-size: 36px;
            color: #343a40;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 18px;
            color: #6c757d;
        }
        .btn-custom {
            background-color: #ffc107;
            color: white;
            border-radius: 4px;
        }
        .btn-custom:hover {
            background-color: #e0a800;
        }
        .menu-icon {
            display: block;
            position: fixed;
            top: 15px;
            left: 15px;
            font-size: 24px;
            
            color: #ffc107;
            z-index: 1001;
        }
        .notification {
            background-color: white;
            color: rgb(255, 9, 9);
            border-radius: 45%;
            padding: 2px 8px;
            font-size: 14px;
            margin-left: 18px;
        }
        /* Gamified Elements */
        .sidebar h2, .content h1, .btn-custom {
            font-family: 'Comic Sans MS', cursive, sans-serif;
            
        }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 15px;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .sidebar .nav-link .badge {
            background-color: red;
            color: white;
            font-size: 12px;
            margin-left: auto;
        }
        .btn-custom {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transform: translateY(0);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        @media (max-width: 768px) {
            .sidebar {
                left: -100%;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                margin-left: 0;
            }
            .content.active {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="menu-icon" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <nav class="sidebar">
        <div class="sidebar-sticky pt-3 text-center">
            <h2>Tableau de Bord Admin</h2>
            
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="mes-enseignants.php"><i class="fas fa-users"><?php if ($newEntriesCount > 0): ?>
                        <span class="badge badge-danger"><?= $newEntriesCount ?> Nouveau message</span>
                    <?php endif; ?></i> Mes Enseignants</a>
                    
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="valide.php"><i class="fas fa-user"></i> Valider Compte</a>
                </li>
            
                
                <li class="nav-item">
                    <a class="nav-link" href="gestion-notes.html"><i class="fas fa-edit"></i> Gestion des Notes</a>
                </li>
               
            </ul>
        </div>
    </nav>
    <div class="content">
        <div class="text-center mt-5">
        
            
            <h1>Welcome, <?php echo $_SESSION['firstName_admin'] . ' ' . $_SESSION['lastName_admin']; ?> sur votre Tableau de Bord</h1>
            <p>Sélectionnez une option dans la barre latérale pour commencer.</p>
           
        
           
            
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
        }
    </script>
</body>
</html>
