<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin</title>
</head>
<body>
    <h1>Welcome, Admin!</h1>
    <div id="userInfo">
        <?php if (isset($_SESSION['user'])): ?>
            <p>User ID: <?php echo htmlspecialchars($_SESSION['user']['userId_admin']); ?></p>
            <p>First Name: <?php echo htmlspecialchars($_SESSION['user']['firstName_admin']); ?></p>
            <p>Last Name: <?php echo htmlspecialchars($_SESSION['user']['lastName_admin']); ?></p>
        <?php else: ?>
            <p>User information not found or incomplete.</p>
        <?php endif; ?>
    </div>
</body>
</html>
