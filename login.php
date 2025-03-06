<?php 
include "./parti/header.php";

$cookie_duration = 7 * 24 * 60 * 60; // 1 semaine
$message = '';

function redirect($url = 'index.php') {
    header("Location: $url");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $submit = $_POST['submit'];

    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = $_POST['mdp'] ?? '';

    if (empty($email) || empty($password) || ($submit === 'signup' && empty($username))) {
        $message = "Tous les champs sont requis.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse e-mail invalide.";
    } else {
        if ($submit === 'signup') {
            // Validation du mot de passe
            $password_regex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{7,}$/';
            if (!preg_match($password_regex, $password)) {
                $message = "Le mot de passe doit contenir au moins 7 caractères, dont une majuscule, une minuscule et un chiffre.";
            } else {
                // Vérifier si l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM utilisateur WHERE email = ?");
                $stmt->execute([$email]);

                if ($stmt->rowCount() > 0) {
                    $message = "Un compte avec cet email existe déjà.";
                } else {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, email, mdp) VALUES (?, ?, ?)");
                    if ($stmt->execute([$username, $email, $password_hash])) {
                        $message = "Inscription réussie. <a href='login.php'>Connectez-vous</a>";
                    } else {
                        $message = "Erreur lors de l'inscription.";
                    }
                }
            }
        } elseif ($submit === 'login') {
            $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['mdp'])) {
                // Générer un cookie de session sécurisé
                $cookie_value = bin2hex(random_bytes(32));
                $user_id = $user['id'];

                $stmt = $pdo->prepare("UPDATE utilisateur SET session_cookie = ? WHERE id = ?");
                $stmt->execute([$cookie_value, $user_id]);

                setcookie('session', $cookie_value, time() + $cookie_duration, "/", "", true, true);

                redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
            } else {
                $message = "Email ou mot de passe incorrect.";
            }
        }
    }
}

// Vérification de l'utilisateur connecté
$user = null;
if (isset($_COOKIE['session'])) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE session_cookie = ?");
    $stmt->execute([$_COOKIE['session']]);
    $user = $stmt->fetch();
}
?>

<?php if ($user): ?>
<section>
    <div style="display: flex; justify-content: center; padding-top: 2%; flex-direction: column;">
        <div class="hub">
            <h2>Bienvenue, <?= htmlspecialchars($user['nom']); ?> !</h2>
            <p>Vous êtes connecté. Bienvenue dans votre hub utilisateur.</p>
            <a href="./partials/logout.php" class="logout-btn">Se déconnecter</a>
        </div>
        <hr>
      
    </div>
</section>
<?php else: ?>
<div style="margin: auto; display: flex; justify-content: center; padding-top: 2%;">
    <div class="main">
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?= htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <input type="checkbox" id="chk" aria-hidden="true">

       

        <div class="login">
            <form method="POST">
                <label for="chk" aria-hidden="true">Login</label>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="mdp" placeholder="Password" required>
                <button type="submit" name="submit" value="login">Login</button>
                <p> Vous n'avez toujours pas de compte ? <a href="signup.php"> Créer ici </a></p>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
