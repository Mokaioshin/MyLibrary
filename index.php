<?php 
// Inclure la connexion à la base de données
require_once 'pdo.php';
include './parti/header.php';

session_start();
$cookie_duration = 7 * 24 * 60 * 60; // 1 semaine
$message = '';

// Fonction de redirection
function redirect($url = 'index.php') {
    header("Location: $url");
    exit();
}

// Vérification de l'utilisateur connecté
$user = null;
if (isset($_COOKIE['session'])) {
    $session = htmlspecialchars($_COOKIE['session']); // Sécurisation basique
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE session_cookie = :session");
    $stmt->execute(['session' => $session]);
    $user = $stmt->fetch();
}
?>

<div class="menu-principal">
    <h1>My Library</h1>
    <h3>Ici vous pouvez faire votre sélection de vos livres favoris !</h3>
</div>

<!-- Recherche de livres -->
<div class="search">
    <form id="search-form">
        <input type="text" name="search" id="search-input" placeholder="Rechercher un livre">
        <button type="submit" id="submit">Rechercher</button>
        <button id="favoris-btn">Favoris</button>
        <button id="add-fav-btn">Ajouter aux favoris</button>
    </form>
</div>

<!-- Affichage des livres -->
<div class="search-results"></div>
<script  src="script.js"></script>
<?php 
// Fin du fichier PHP
?>
