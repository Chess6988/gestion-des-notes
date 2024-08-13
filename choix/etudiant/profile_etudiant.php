<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etudiant Profile</title>
</head>
<body>
    <h1>Welcome, Etudiant!</h1>
    <div id="userInfo">
        <?php if (isset($_SESSION['user'])): ?>
            <p>User ID: <?php echo htmlspecialchars($_SESSION['user']['userId_etudiant']); ?></p>
            <p>First Name: <?php echo htmlspecialchars($_SESSION['user']['firstName_etudiant']); ?></p>
            <p>Last Name: <?php echo htmlspecialchars($_SESSION['user']['lastName_etudiant']); ?></p>
        <?php else: ?>
            <p>User information not found or incomplete.</p>
        <?php endif; ?>
    </div>
</body>
</html>
