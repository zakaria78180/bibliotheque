<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();

// Statistiques
$totalLivres = $db->query("SELECT COUNT(*) FROM livres")->fetchColumn();
$totalUtilisateurs = $db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$empruntsEnCours = $db->query("SELECT COUNT(*) FROM emprunts WHERE statut = 'en_cours'")->fetchColumn();
$livresDisponibles = $db->query("SELECT SUM(disponible) FROM livres")->fetchColumn();

// Derniers emprunts
$derniersEmprunts = $db->query("
    SELECT e.*, l.titre, u.nom, u.prenom 
    FROM emprunts e 
    JOIN livres l ON e.livre_id = l.id 
    JOIN utilisateurs u ON e.utilisateur_id = u.id 
    ORDER BY e.date_emprunt DESC 
    LIMIT 5
")->fetchAll();

$pageTitle = "Accueil";
include 'includes/header.php';
?>

<div class="page-header">
    <h1>Tableau de bord</h1>
    <p>Bienvenue dans votre application de gestion de bibliothèque</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Livres</h3>
        <div class="value"><?php echo $totalLivres; ?></div>
    </div>
    <div class="stat-card">
        <h3>Livres Disponibles</h3>
        <div class="value"><?php echo $livresDisponibles ?: 0; ?></div>
    </div>
    <div class="stat-card">
        <h3>Utilisateurs</h3>
        <div class="value"><?php echo $totalUtilisateurs; ?></div>
    </div>
    <div class="stat-card">
        <h3>Emprunts en cours</h3>
        <div class="value"><?php echo $empruntsEnCours; ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Derniers emprunts</h2>
        <a href="emprunts.php" class="btn btn-primary">Voir tout</a>
    </div>
    
    <?php if (empty($derniersEmprunts)): ?>
        <p style="color: #64748b; text-align: center; padding: 2rem;">Aucun emprunt enregistré</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Livre</th>
                    <th>Utilisateur</th>
                    <th>Date emprunt</th>
                    <th>Retour prévu</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($derniersEmprunts as $emprunt): ?>
                <tr>
                    <td><?php echo htmlspecialchars($emprunt['titre']); ?></td>
                    <td><?php echo htmlspecialchars($emprunt['prenom'] . ' ' . $emprunt['nom']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($emprunt['date_retour_prevue'])); ?></td>
                    <td>
                        <?php if ($emprunt['statut'] == 'en_cours'): ?>
                            <span class="badge badge-warning">En cours</span>
                        <?php elseif ($emprunt['statut'] == 'retourne'): ?>
                            <span class="badge badge-success">Retourné</span>
                        <?php else: ?>
                            <span class="badge badge-danger">En retard</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1rem;">Actions rapides</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="livres.php?action=ajouter" class="btn btn-primary">Ajouter un livre</a>
            <a href="utilisateurs.php?action=ajouter" class="btn btn-secondary">Ajouter un utilisateur</a>
            <a href="emprunts.php?action=ajouter" class="btn btn-success">Nouvel emprunt</a>
        </div>
    </div>
    
    <div class="card">
        <h3 class="card-title" style="margin-bottom: 1rem;">Navigation</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="livres.php" class="btn btn-secondary">Gérer les livres</a>
            <a href="utilisateurs.php" class="btn btn-secondary">Gérer les utilisateurs</a>
            <a href="emprunts.php" class="btn btn-secondary">Gérer les emprunts</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
