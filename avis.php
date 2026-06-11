<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';

// Dépôt / modification d'un avis (réservé aux utilisateurs connectés)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deposer_avis'])) {
    if (!estConnecte()) {
        header('Location: connexion.php');
        exit;
    }

    $utilisateurId = utilisateurConnecteId();
    $livreId = intval($_POST['livre_id']);
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);

    // Vérifier que l'utilisateur a bien emprunté ce livre
    $verif = $db->prepare("SELECT COUNT(*) FROM emprunts WHERE livre_id = ? AND utilisateur_id = ?");
    $verif->execute([$livreId, $utilisateurId]);
    $aEmprunte = $verif->fetchColumn() > 0;

    if (!$aEmprunte) {
        $message = "Vous ne pouvez déposer un avis que sur un livre que vous avez emprunté.";
        $messageType = "danger";
    } elseif ($note < 1 || $note > 5) {
        $message = "La note doit être comprise entre 1 et 5.";
        $messageType = "danger";
    } else {
        // INSERT ou UPDATE (un seul avis par utilisateur et par livre)
        $existe = $db->prepare("SELECT id FROM avis WHERE livre_id = ? AND utilisateur_id = ?");
        $existe->execute([$livreId, $utilisateurId]);
        $avisExistant = $existe->fetch();

        if ($avisExistant) {
            $stmt = $db->prepare("UPDATE avis SET note = ?, commentaire = ?, date_avis = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$note, $commentaire, $avisExistant['id']]);
            $message = "Votre avis a été mis à jour avec succès !";
        } else {
            $stmt = $db->prepare("INSERT INTO avis (livre_id, utilisateur_id, note, commentaire) VALUES (?, ?, ?, ?)");
            $stmt->execute([$livreId, $utilisateurId, $note, $commentaire]);
            $message = "Votre avis a été enregistré avec succès !";
        }
        $messageType = "success";
    }
}

// Filtre optionnel par livre
$livreFiltre = isset($_GET['livre']) ? intval($_GET['livre']) : 0;

// Liste des avis avec jointures (livre + utilisateur)
$sql = "
    SELECT a.id, a.note, a.commentaire, a.date_avis,
           l.id AS livre_id, l.titre, l.auteur,
           u.prenom, u.nom
    FROM avis a
    JOIN livres l ON a.livre_id = l.id
    JOIN utilisateurs u ON a.utilisateur_id = u.id
";
if ($livreFiltre) {
    $sql .= " WHERE a.livre_id = ?";
}
$sql .= " ORDER BY a.date_avis DESC";

$stmt = $db->prepare($sql);
$stmt->execute($livreFiltre ? [$livreFiltre] : []);
$avis = $stmt->fetchAll();

// Note moyenne (globale ou pour le livre filtré)
$sqlMoyenne = "SELECT COUNT(*) AS nb, AVG(note) AS moyenne FROM avis" . ($livreFiltre ? " WHERE livre_id = ?" : "");
$stmtMoy = $db->prepare($sqlMoyenne);
$stmtMoy->execute($livreFiltre ? [$livreFiltre] : []);
$statsMoyenne = $stmtMoy->fetch();

// Livre filtré (pour l'en-tête)
$livreCourant = null;
if ($livreFiltre) {
    $stmtL = $db->prepare("SELECT * FROM livres WHERE id = ?");
    $stmtL->execute([$livreFiltre]);
    $livreCourant = $stmtL->fetch();
}

// Livres empruntés par l'utilisateur connecté (pour le formulaire de dépôt d'avis)
$livresEmpruntes = [];
if (estConnecte()) {
    $stmtE = $db->prepare("
        SELECT DISTINCT l.id, l.titre, l.auteur
        FROM emprunts e
        JOIN livres l ON e.livre_id = l.id
        WHERE e.utilisateur_id = ?
        ORDER BY l.titre
    ");
    $stmtE->execute([utilisateurConnecteId()]);
    $livresEmpruntes = $stmtE->fetchAll();
}

// Fonction d'affichage des étoiles
function afficherEtoiles($note) {
    $html = '<span class="etoiles">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $note ? '★' : '<span class="etoile-vide">★</span>';
    }
    $html .= '</span>';
    return $html;
}

$pageTitle = "Avis des lecteurs";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Avis des lecteurs</h1>
    <p>
        <?php if ($livreCourant): ?>
            Avis sur « <?php echo htmlspecialchars($livreCourant['titre']); ?> » de <?php echo htmlspecialchars($livreCourant['auteur']); ?>
        <?php else: ?>
            Consultez les avis déposés sur l'ensemble des livres de la bibliothèque
        <?php endif; ?>
    </p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Nombre d'avis</h3>
        <div class="value"><?php echo intval($statsMoyenne['nb']); ?></div>
    </div>
    <div class="stat-card">
        <h3>Note moyenne</h3>
        <div class="value">
            <?php echo $statsMoyenne['nb'] > 0 ? number_format($statsMoyenne['moyenne'], 1) . ' / 5' : '-'; ?>
        </div>
    </div>
</div>

<?php if (estConnecte()): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Déposer un avis</h2>
        </div>

        <?php if (empty($livresEmpruntes)): ?>
            <div class="alert alert-info">
                Vous n'avez emprunté aucun livre pour le moment. Vous pourrez déposer un avis dès que vous aurez emprunté un livre.
            </div>
        <?php else: ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Livre emprunté *</label>
                        <select name="livre_id" class="form-control" required>
                            <option value="">Sélectionner un livre</option>
                            <?php foreach ($livresEmpruntes as $livre): ?>
                                <option value="<?php echo $livre['id']; ?>" <?php echo $livreFiltre === intval($livre['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($livre['titre']); ?> - <?php echo htmlspecialchars($livre['auteur']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="form-help">Seuls les livres que vous avez empruntés sont proposés.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Note *</label>
                        <select name="note" class="form-control" required>
                            <option value="">Choisir une note</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Très bien</option>
                            <option value="3">3 - Bien</option>
                            <option value="2">2 - Moyen</option>
                            <option value="1">1 - Décevant</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Commentaire</label>
                    <textarea name="commentaire" class="form-control" rows="4" placeholder="Partagez votre opinion sur ce livre..."></textarea>
                </div>

                <button type="submit" name="deposer_avis" class="btn btn-primary">Publier mon avis</button>
            </form>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <a href="connexion.php">Connectez-vous</a> pour déposer un avis sur un livre que vous avez emprunté.
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Tous les avis</h2>
        <?php if ($livreFiltre): ?>
            <a href="avis.php" class="btn btn-secondary btn-sm">Voir tous les avis</a>
        <?php endif; ?>
    </div>

    <?php if (empty($avis)): ?>
        <p style="color: #64748b; text-align: center; padding: 2rem;">Aucun avis pour le moment.</p>
    <?php else: ?>
        <div class="avis-liste">
            <?php foreach ($avis as $a): ?>
                <div class="avis-item">
                    <div class="avis-item-header">
                        <div>
                            <a href="avis.php?livre=<?php echo $a['livre_id']; ?>" class="avis-livre-titre">
                                <?php echo htmlspecialchars($a['titre']); ?>
                            </a>
                            <span class="subtitle"> · <?php echo htmlspecialchars($a['auteur']); ?></span>
                        </div>
                        <?php echo afficherEtoiles($a['note']); ?>
                    </div>
                    <?php if (!empty($a['commentaire'])): ?>
                        <p class="avis-commentaire"><?php echo nl2br(htmlspecialchars($a['commentaire'])); ?></p>
                    <?php endif; ?>
                    <div class="avis-meta">
                        Par <?php echo htmlspecialchars($a['prenom'] . ' ' . $a['nom']); ?>
                        le <?php echo date('d/m/Y', strtotime($a['date_avis'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
