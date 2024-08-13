<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> SAdmin</title>
</head>
<body>
    <h1>Welcome, SAdmin!</h1>
    <div id="userInfo">
        <?php if (isset($_SESSION['user'])): ?>
            <p>User ID: <?php echo htmlspecialchars($_SESSION['user']['userId_superadmin']); ?></p>
            <p>First Name: <?php echo htmlspecialchars($_SESSION['user']['firstName_superadmin']); ?></p>
            <p>Last Name: <?php echo htmlspecialchars($_SESSION['user']['lastName_superadmin']); ?></p>
        <?php else: ?>
            <p>User information not found or incomplete.</p>
        <?php endif; ?>
    </div>
</body>
</html>
