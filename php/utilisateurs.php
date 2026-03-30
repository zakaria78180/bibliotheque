<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';
$editUtilisateur = null;

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        $stmt = $db->prepare("
            INSERT INTO utilisateurs (nom, prenom, email, telephone, adresse) 
            VALUES (?, ?, ?, ?, ?)
        ");
        try {
            $stmt->execute([
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['adresse']
            ]);
            $message = "Utilisateur ajouté avec succès!";
            $messageType = "success";
            $action = 'liste';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $message = "Cet email est déjà utilisé.";
            } else {
                $message = "Erreur lors de l'ajout de l'utilisateur.";
            }
            $messageType = "danger";
        }
    }
    
    if (isset($_POST['modifier'])) {
        $stmt = $db->prepare("
            UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?
            WHERE id = ?
        ");
        try {
            $stmt->execute([
                $_POST['nom'],
                $_POST['prenom'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['adresse'],
                $_POST['id']
            ]);
            $message = "Utilisateur modifié avec succès!";
            $messageType = "success";
            $action = 'liste';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                $message = "Cet email est déjà utilisé.";
            } else {
                $message = "Erreur lors de la modification.";
            }
            $messageType = "danger";
        }
    }
}

// Suppression
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    // Vérifier si l'utilisateur a des emprunts en cours
    $emprunts = $db->prepare("SELECT COUNT(*) FROM emprunts WHERE utilisateur_id = ? AND statut = 'en_cours'");
    $emprunts->execute([$id]);
    if ($emprunts->fetchColumn() > 0) {
        $message = "Impossible de supprimer cet utilisateur: il a des emprunts en cours.";
        $messageType = "danger";
    } else {
        $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Utilisateur supprimé avec succès!";
        $messageType = "success";
    }
    $action = 'liste';
}

// Modifier - charger les données
if ($action === 'modifier' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $editUtilisateur = $stmt->fetch();
}

// Recherche
$recherche = isset($_GET['recherche']) ? $_GET['recherche'] : '';
if ($recherche) {
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? ORDER BY nom, prenom");
    $searchTerm = '%' . $recherche . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
} else {
    $stmt = $db->query("SELECT * FROM utilisateurs ORDER BY nom, prenom");
}
$utilisateurs = $stmt->fetchAll();

$pageTitle = "Gestion des Utilisateurs";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Gestion des Utilisateurs</h1>
    <p>Gérez les membres de la bibliothèque</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($action === 'ajouter' || $action === 'modifier'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?php echo $action === 'ajouter' ? 'Ajouter un utilisateur' : 'Modifier l\'utilisateur'; ?></h2>
            <a href="utilisateurs.php" class="btn btn-secondary">Retour à la liste</a>
        </div>
        
        <form method="POST">
            <?php if ($editUtilisateur): ?>
                <input type="hidden" name="id" value="<?php echo $editUtilisateur['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="nom" class="form-control" required 
                           value="<?php echo $editUtilisateur ? htmlspecialchars($editUtilisateur['nom']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="prenom" class="form-control" required
                           value="<?php echo $editUtilisateur ? htmlspecialchars($editUtilisateur['prenom']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required
                           value="<?php echo $editUtilisateur ? htmlspecialchars($editUtilisateur['email']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control"
                           value="<?php echo $editUtilisateur ? htmlspecialchars($editUtilisateur['telephone']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Adresse</label>
                <textarea name="adresse" class="form-control" rows="3"><?php echo $editUtilisateur ? htmlspecialchars($editUtilisateur['adresse']) : ''; ?></textarea>
            </div>
            
            <button type="submit" name="<?php echo $action === 'ajouter' ? 'ajouter' : 'modifier'; ?>" class="btn btn-primary">
                <?php echo $action === 'ajouter' ? 'Ajouter l\'utilisateur' : 'Enregistrer les modifications'; ?>
            </button>
        </form>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Liste des utilisateurs</h2>
            <a href="utilisateurs.php?action=ajouter" class="btn btn-primary">Ajouter un utilisateur</a>
        </div>
        
        <form class="search-form" method="GET">
            <input type="text" name="recherche" class="form-control" placeholder="Rechercher par nom, prénom ou email..." 
                   value="<?php echo htmlspecialchars($recherche); ?>">
            <button type="submit" class="btn btn-primary">Rechercher</button>
            <?php if ($recherche): ?>
                <a href="utilisateurs.php" class="btn btn-secondary">Effacer</a>
            <?php endif; ?>
        </form>
        
        <?php if (empty($utilisateurs)): ?>
            <p style="color: #64748b; text-align: center; padding: 2rem;">Aucun utilisateur trouvé</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['telephone']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($utilisateur['date_inscription'])); ?></td>
                        <td class="actions">
                            <a href="utilisateurs.php?action=modifier&id=<?php echo $utilisateur['id']; ?>" class="btn btn-secondary btn-sm">Modifier</a>
                            <a href="utilisateurs.php?supprimer=<?php echo $utilisateur['id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
