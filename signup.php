<?php
include "./parti/header.php";
require_once "pdo.php";

$cookie_duration = 7 * 24 * 60 * 60; // 1 semaine
$message = '';

// Fonction de redirection sécurisée
function redirect($url = 'index.php') {
    header("Location: $url");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && $_POST['submit'] === 'signup') {
    // Récupération et nettoyage des données
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mdp'] ?? '';

    // Vérifications des champs
    if (empty($email) || empty($password) || empty($username)) {
        $message = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse e-mail invalide.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{7,}$/', $password)) {
        $message = "Le mot de passe doit contenir au moins 7 caractères, une majuscule, une minuscule et un chiffre.";
    } else {
        // Vérifier si l'email est déjà utilisé
        $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $message = "Un compte avec cet email existe déjà.";
        } else {
            // Hachage du mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion dans la base de données
            $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $password_hash])) {
                $message = "Inscription réussie. <a href='login.php'>Connectez-vous</a>";
            } else {
                $message = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<div class="container">
    <h2>Inscription</h2>
    <?php if (!empty($message)): ?>
        <p style="color: red;"> <?= htmlspecialchars($message); ?> </p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="mdp" placeholder="Mot de passe" required>
        <button type="submit" name="submit" value="signup">S'inscrire</button>
    </form>
    <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
</div>

<?php include "./parti/footer.php"; ?>
