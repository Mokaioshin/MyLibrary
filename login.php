<?php
require_once 'pdo.php';
include './parti/header.php';

session_start();
$cookie_duration = 7 * 24 * 60 * 60; // 1 semaine
$message = '';

function redirect($url = 'index.php') {
    header("Location: $url");
    exit();
}

// Vérification du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['mdp'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse e-mail invalide.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $cookie_value = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("UPDATE utilisateur SET session_cookie = ? WHERE id = ?");
            $stmt->execute([$cookie_value, $user['id']]);

            setcookie('session', $cookie_value, [
                'expires' => time() + $cookie_duration,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);

            session_regenerate_id(true);

            //redirect('index.php');
            header("location: index.php");
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    }
}

// Vérification de la session utilisateur
$user = null;
if (!empty($_COOKIE['session'])) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE session_cookie = ?");
    $stmt->execute([$_COOKIE['session']]);
    $user = $stmt->fetch();

    if ($user) {
        redirect('index.php');
    }
}
?>

<?php if ($user): ?>
    <h2>Bienvenue, <?= htmlspecialchars($user['nom']); ?> !</h2>
    <a href="logout.php">Se déconnecter</a>
<?php else: ?>
    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Mot de passe</label>
        <input type="password" name="mdp" required>
        <button type="submit" name="submit">Login</button>
        <p>Pas encore de compte ? <a href="signup.php">Inscrivez-vous</a></p>
    </form>
    <p style="color: red;"> <?= htmlspecialchars($message); ?> </p>
<?php endif; ?>

<?php include './parti/footer.php'; ?>
