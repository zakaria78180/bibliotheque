<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

// Si déjà connecté, rediriger vers les avis
if (estConnecte()) {
    header('Location: avis.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connexion'])) {
    $email = trim($_POST['email']);
    $motDePasse = $_POST['mot_de_passe'];

    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ? AND actif = 1");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch();

    if ($utilisateur && !empty($utilisateur['mot_de_passe']) && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        connecterUtilisateur($utilisateur);
        header('Location: avis.php');
        exit;
    } else {
        $message = "Email ou mot de passe incorrect.";
        $messageType = "danger";
    }
}

$pageTitle = "Connexion";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Connexion</h1>
    <p>Connectez-vous pour déposer un avis sur les livres que vous avez empruntés</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 480px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Se connecter</h2>
    </div>

    <form method="POST">
        <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Mot de passe *</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>

        <button type="submit" name="connexion" class="btn btn-primary">Se connecter</button>
    </form>

    <div class="info-box">
        <h3>Comptes de démonstration</h3>
        <p>Vous pouvez utiliser un compte de test :</p>
        <ul>
            <li>marie.dupont@email.com</li>
            <li>jean.martin@email.com</li>
            <li>sophie.bernard@email.com</li>
        </ul>
        <p>Mot de passe pour tous : <strong>password</strong></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
