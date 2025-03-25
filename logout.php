<?php
session_start();
session_destroy();

// Supprimer le cookie de session
//setcookie('session', '', time() - 3600, "/", "", true, true);

// Redirection vers la page d'accueil
header("Location: index.php");
exit();
