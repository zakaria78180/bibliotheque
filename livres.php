<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$editLivre = null;

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        $stmt = $db->prepare("
            INSERT INTO livres (titre, auteur, isbn, annee_publication, genre, quantite, disponible) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $quantite = intval($_POST['quantite']);
        $stmt->execute([
            $_POST['titre'],
            $_POST['auteur'],
            $_POST['isbn'],
            $_POST['annee_publication'],
            $_POST['genre'],
            $quantite,
            $quantite
        ]);
        $message = "Livre ajouté avec succès!";
        $messageType = "success";
        $action = 'liste';
    }
    
    if (isset($_POST['modifier'])) {
        $stmt = $db->prepare("
            UPDATE livres SET titre = ?, auteur = ?, isbn = ?, annee_publication = ?, genre = ?, quantite = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $_POST['titre'],
            $_POST['auteur'],
            $_POST['isbn'],
            $_POST['annee_publication'],
            $_POST['genre'],
            $_POST['quantite'],
            $_POST['id']
        ]);
        $message = "Livre modifié avec succès!";
        $messageType = "success";
        $action = 'liste';
    }
}

// Suppression
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    // Vérifier si le livre est emprunté
    $emprunts = $db->prepare("SELECT COUNT(*) FROM emprunts WHERE livre_id = ? AND statut = 'en_cours'");
    $emprunts->execute([$id]);
    if ($emprunts->fetchColumn() > 0) {
        $message = "Impossible de supprimer ce livre: il est actuellement emprunté.";
        $messageType = "danger";
    } else {
        $stmt = $db->prepare("DELETE FROM livres WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Livre supprimé avec succès!";
        $messageType = "success";
    }
    $action = 'liste';
}

// Modifier - charger les données
if ($action === 'modifier' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM livres WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $editLivre = $stmt->fetch();
}

// Recherche
$recherche = isset($_GET['recherche']) ? $_GET['recherche'] : '';
if ($recherche) {
    $stmt = $db->prepare("SELECT * FROM livres WHERE titre LIKE ? OR auteur LIKE ? OR isbn LIKE ? ORDER BY titre");
    $searchTerm = '%' . $recherche . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $db->query("SELECT * FROM livres ORDER BY titre");
}
$livres = $stmt->fetchAll();

$pageTitle = "Gestion des Livres";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Gestion des Livres</h1>
    <p>Ajoutez, modifiez et supprimez des livres de la bibliothèque</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($action === 'ajouter' || $action === 'modifier'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?php echo $action === 'ajouter' ? 'Ajouter un livre' : 'Modifier le livre'; ?></h2>
            <a href="livres.php" class="btn btn-secondary">Retour à la liste</a>
        </div>
        
        <form method="POST">
            <?php if ($editLivre): ?>
                <input type="hidden" name="id" value="<?php echo $editLivre['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="titre" class="form-control" required 
                           value="<?php echo $editLivre ? htmlspecialchars($editLivre['titre']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Auteur *</label>
                    <input type="text" name="auteur" class="form-control" required
                           value="<?php echo $editLivre ? htmlspecialchars($editLivre['auteur']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control"
                           value="<?php echo $editLivre ? htmlspecialchars($editLivre['isbn']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Année de publication</label>
                    <input type="number" name="annee_publication" class="form-control" min="1000" max="<?php echo date('Y'); ?>"
                           value="<?php echo $editLivre ? $editLivre['annee_publication'] : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Genre</label>
                    <input type="text" name="genre" class="form-control"
                           value="<?php echo $editLivre ? htmlspecialchars($editLivre['genre']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Quantité</label>
                    <input type="number" name="quantite" class="form-control" min="1" value="<?php echo $editLivre ? $editLivre['quantite'] : '1'; ?>">
                </div>
            </div>
            
            <button type="submit" name="<?php echo $action === 'ajouter' ? 'ajouter' : 'modifier'; ?>" class="btn btn-primary">
                <?php echo $action === 'ajouter' ? 'Ajouter le livre' : 'Enregistrer les modifications'; ?>
            </button>
        </form>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Liste des livres</h2>
            <a href="livres.php?action=ajouter" class="btn btn-primary">Ajouter un livre</a>
        </div>
        
        <form class="search-form" method="GET">
            <input type="text" name="recherche" class="form-control" placeholder="Rechercher par titre, auteur ou ISBN..." 
                   value="<?php echo htmlspecialchars($recherche); ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <?php if ($recherche): ?>
                <a href="livres.php" class="btn btn-secondary">Effacer</a>
            <?php endif; ?>
        </form>
        
        <?php if (empty($livres)): ?>
            <p style="color: #64748b; text-align: center; padding: 2rem;">Aucun livre trouvé</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>ISBN</th>
                        <th>Année</th>
                        <th>Genre</th>
                        <th>Disponibilité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($livres as $livre): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($livre['titre']); ?></td>
                        <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                        <td><?php echo htmlspecialchars($livre['isbn']); ?></td>
                        <td><?php echo $livre['annee_publication']; ?></td>
                        <td><?php echo htmlspecialchars($livre['genre']); ?></td>
                        <td>
                            <?php if ($livre['disponible'] > 0): ?>
                                <span class="badge badge-success"><?php echo $livre['disponible']; ?>/<?php echo $livre['quantite']; ?></span>
                            <?php else: ?>
                                <span class="badge badge-danger">Indisponible</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="livres.php?action=modifier&id=<?php echo $livre['id']; ?>" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="livres.php?supprimer=<?php echo $livre['id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
