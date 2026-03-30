<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'liste';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter'])) {
        $livreId = intval($_POST['livre_id']);
        $utilisateurId = intval($_POST['utilisateur_id']);
        $dateRetour = $_POST['date_retour_prevue'];
        
        // Vérifier la disponibilité du livre
        $livre = $db->prepare("SELECT disponible FROM livres WHERE id = ?");
        $livre->execute([$livreId]);
        $livreData = $livre->fetch();
        
        if ($livreData && $livreData['disponible'] > 0) {
            // Créer l'emprunt
            $stmt = $db->prepare("
                INSERT INTO emprunts (livre_id, utilisateur_id, date_retour_prevue, statut) 
                VALUES (?, ?, ?, 'en_cours')
            ");
            $stmt->execute([$livreId, $utilisateurId, $dateRetour]);
            
            // Mettre à jour la disponibilité du livre
            $db->prepare("UPDATE livres SET disponible = disponible - 1 WHERE id = ?")->execute([$livreId]);
            
            $message = "Emprunt enregistré avec succès!";
            $messageType = "success";
        } else {
            $message = "Ce livre n'est pas disponible.";
            $messageType = "danger";
        }
        $action = 'liste';
    }
}

// Retour d'un livre
if (isset($_GET['retour'])) {
    $id = intval($_GET['retour']);
    
    // Récupérer l'emprunt
    $emprunt = $db->prepare("SELECT livre_id FROM emprunts WHERE id = ? AND statut = 'en_cours'");
    $emprunt->execute([$id]);
    $empruntData = $emprunt->fetch();
    
    if ($empruntData) {
        // Marquer comme retourné
        $stmt = $db->prepare("UPDATE emprunts SET statut = 'retourne', date_retour_effective = DATE('now') WHERE id = ?");
        $stmt->execute([$id]);
        
        // Remettre le livre disponible
        $db->prepare("UPDATE livres SET disponible = disponible + 1 WHERE id = ?")->execute([$empruntData['livre_id']]);
        
        $message = "Retour enregistré avec succès!";
        $messageType = "success";
    }
}

// Suppression
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    
    // Récupérer l'emprunt pour remettre le livre disponible si nécessaire
    $emprunt = $db->prepare("SELECT livre_id, statut FROM emprunts WHERE id = ?");
    $emprunt->execute([$id]);
    $empruntData = $emprunt->fetch();
    
    if ($empruntData && $empruntData['statut'] === 'en_cours') {
        // Remettre le livre disponible
        $db->prepare("UPDATE livres SET disponible = disponible + 1 WHERE id = ?")->execute([$empruntData['livre_id']]);
    }
    
    $stmt = $db->prepare("DELETE FROM emprunts WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Emprunt supprimé avec succès!";
    $messageType = "success";
}

// Récupérer les livres disponibles et utilisateurs pour le formulaire
$livresDisponibles = $db->query("SELECT * FROM livres WHERE disponible > 0 ORDER BY titre")->fetchAll();
$utilisateurs = $db->query("SELECT * FROM utilisateurs WHERE actif = 1 ORDER BY nom, prenom")->fetchAll();

// Filtre par statut
$filtre = isset($_GET['filtre']) ? $_GET['filtre'] : '';
if ($filtre) {
    $stmt = $db->prepare("
        SELECT e.*, l.titre, u.nom, u.prenom 
        FROM emprunts e 
        JOIN livres l ON e.livre_id = l.id 
        JOIN utilisateurs u ON e.utilisateur_id = u.id 
        WHERE e.statut = ?
        ORDER BY e.date_emprunt DESC
    ");
    $stmt->execute([$filtre]);
} else {
    $stmt = $db->query("
        SELECT e.*, l.titre, u.nom, u.prenom 
        FROM emprunts e 
        JOIN livres l ON e.livre_id = l.id 
        JOIN utilisateurs u ON e.utilisateur_id = u.id 
        ORDER BY e.date_emprunt DESC
    ");
}
$emprunts = $stmt->fetchAll();

$pageTitle = "Gestion des Emprunts";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Gestion des Emprunts</h1>
    <p>Enregistrez les emprunts et retours de livres</p>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($action === 'ajouter'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Nouvel emprunt</h2>
            <a href="emprunts.php" class="btn btn-secondary">Retour à la liste</a>
        </div>
        
        <?php if (empty($livresDisponibles)): ?>
            <div class="alert alert-info">Aucun livre disponible pour l'emprunt.</div>
        <?php elseif (empty($utilisateurs)): ?>
            <div class="alert alert-info">Aucun utilisateur enregistré. <a href="utilisateurs.php?action=ajouter">Ajouter un utilisateur</a></div>
        <?php else: ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Livre *</label>
                        <select name="livre_id" class="form-control" required>
                            <option value="">Sélectionner un livre</option>
                            <?php foreach ($livresDisponibles as $livre): ?>
                                <option value="<?php echo $livre['id']; ?>">
                                    <?php echo htmlspecialchars($livre['titre']); ?> - <?php echo htmlspecialchars($livre['auteur']); ?>
                                    (<?php echo $livre['disponible']; ?> disponible(s))
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Utilisateur *</label>
                        <select name="utilisateur_id" class="form-control" required>
                            <option value="">Sélectionner un utilisateur</option>
                            <?php foreach ($utilisateurs as $utilisateur): ?>
                                <option value="<?php echo $utilisateur['id']; ?>">
                                    <?php echo htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date de retour prévue *</label>
                    <input type="date" name="date_retour_prevue" class="form-control" required 
                           min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>">
                </div>
                
                <button type="submit" name="ajouter" class="btn btn-primary">Enregistrer l'emprunt</button>
            </form>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Liste des emprunts</h2>
            <a href="emprunts.php?action=ajouter" class="btn btn-primary">Nouvel emprunt</a>
        </div>
        
        <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem;">
            <a href="emprunts.php" class="btn <?php echo !$filtre ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Tous</a>
            <a href="emprunts.php?filtre=en_cours" class="btn <?php echo $filtre === 'en_cours' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">En cours</a>
            <a href="emprunts.php?filtre=retourne" class="btn <?php echo $filtre === 'retourne' ? 'btn-primary' : 'btn-secondary'; ?> btn-sm">Retournés</a>
        </div>
        
        <?php if (empty($emprunts)): ?>
            <p style="color: #64748b; text-align: center; padding: 2rem;">Aucun emprunt trouvé</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Livre</th>
                        <th>Utilisateur</th>
                        <th>Date emprunt</th>
                        <th>Retour prévu</th>
                        <th>Retour effectif</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emprunts as $emprunt): ?>
                    <?php 
                        $enRetard = $emprunt['statut'] === 'en_cours' && strtotime($emprunt['date_retour_prevue']) < time();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                        <td><?php echo htmlspecialchars($emprunt['prenom'] . ' ' . $emprunt['nom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($emprunt['date_retour_prevue'])); ?></td>
                        <td><?php echo $emprunt['date_retour_effective'] ? date('d/m/Y', strtotime($emprunt['date_retour_effective'])) : '-'; ?></td>
                        <td>
                            <?php if ($emprunt['statut'] === 'retourne'): ?>
                                <span class="badge badge-success">Retourné</span>
                            <?php elseif ($enRetard): ?>
                                <span class="badge badge-danger">En retard</span>
                            <?php else: ?>
                                <span class="badge badge-warning">En cours</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if ($emprunt['statut'] === 'en_cours'): ?>
                                <a href="emprunts.php?retour=<?php echo $emprunt['id']; ?>" class="btn btn-success btn-sm"
                                   onclick="return confirm('Confirmer le retour de ce livre?')">Retour</a>
                            <?php endif; ?>
                            <a href="emprunts.php?supprimer=<?php echo $emprunt['id']; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet emprunt?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
