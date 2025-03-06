<?php

session_start();
include "./parti/header.php";
require_once 'pdo.php';

// Récupération des utilisateurs pour affichage
$liste = $pdo->query("SELECT * FROM utilisateur");
$biblio = $liste->fetchAll();
?>


<div class="menu-principal">
        <h1>My Library</h1>
        <h3>Ici vous pouvez faire votre sélection de vos livres favoris !</h3>
    </div>

    <!-- Recherche de livres -->
    <div class="search">
        <form id="search-form">
            <input type="text" name="search" id="search-input" placeholder="Rechercher un livre">
            <button type="submit">Rechercher</button>
            <button type="favoris">Favoris</button>
        </form>
    </div>

    <!-- Affichage des livres -->
    <div class="search-results"></div>

<?php

    include "./parti/footer.php";
    

    ?>