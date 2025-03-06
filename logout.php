<?php

include "./parti/header.php";
// logout.php : Déconnecter l'utilisateur
session_start();
session_destroy();

// Supprimer le cookie de session
setcookie('session', '', time() - 3600, "/", "", false, true);

// Redirection vers la page d'accueil
header("Location: /Librairie/index.php");
exit;
