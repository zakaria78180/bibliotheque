<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

// Page réservée aux utilisateurs connectés
exigerConnexion();

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';
$utilisateurId = utilisateurConnecteId();

// Suppression d'un de mes avis
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    // On ne supprime que si l'avis appartient bien à l'utilisateur connecté
    $stmt = $db->prepare("DELETE FROM avis WHERE id = ? AND utilisateur_id = ?");
    $stmt->execute([$id, $utilisateurId]);
    $message = "Avis supprimé avec succès !";
    $messageType = "success";
}

// Récupérer tous mes avis avec jointure sur les livres
$stmt = $db->prepare("
    SELECT a.id, a.note, a.commentaire, a.date_avis,
           l.id AS livre_id, l.titre, l.auteur
    FROM avis a
    JOIN livres l ON a.livre_id = l.id
    WHERE a.utilisateur_id = ?
    ORDER BY a.date_avis DESC
");
$stmt->execute([$utilisateurId]);
$mesAvis = $stmt->fetchAll();

function afficherEtoiles($note) {
    $html = '<span class="etoiles">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $note ? '★' : '<span class="etoile-vide">★</span>';
    }
    $html .= '</span>';
    return $html;
}

$pageTitle = "Mes avis";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Mes avis</h1>
    <p>Retrouvez l'ensemble des avis que vous avez déposés</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><?php echo count($mesAvis); ?> avis déposé(s)</h2>
        <a href="avis.php" class="btn btn-primary">Déposer un avis</a>
    </div>

    <?php if (empty($mesAvis)): ?>
        <div class="empty-state">
            <h3>Vous n'avez encore déposé aucun avis</h3>
            <p>Empruntez un livre puis partagez votre opinion avec les autres lecteurs.</p>
            <a href="avis.php" class="btn btn-primary">Déposer un avis</a>
        </div>
    <?php else: ?>
        <div class="avis-liste">
            <?php foreach ($mesAvis as $a): ?>
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
                    <div class="avis-item-footer">
                        <span class="avis-meta">Déposé le <?php echo date('d/m/Y', strtotime($a['date_avis'])); ?></span>
                        <a href="mes_avis.php?supprimer=<?php echo $a['id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
