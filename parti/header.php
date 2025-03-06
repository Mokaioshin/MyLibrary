<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librairie</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>

<body>

    <header class="header-outer">
        <!-- Affichage du menu de navigation en fonction de la connexion -->
        <?php if (isset($_COOKIE['session'])): ?>
            <a href="logout.php">Se déconnecter</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
        <?php endif; ?>
    </header>