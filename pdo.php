<?php
// pdo.php : Connexion à la base de données
$dsn = "mysql:host=localhost;dbname=biblio;charset=utf8mb4";
$utilisateur = "root";
$mdp = "";

try {
    $pdo = new PDO($dsn, $utilisateur, $mdp);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $er) {
    die("Erreur de connexion : " . $er->getMessage());
}
