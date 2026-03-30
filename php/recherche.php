<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$recherche = isset($_GET['q']) ? trim($_GET['q']) : '';
$genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
$disponible = isset($_GET['disponible']) ? $_GET['disponible'] : '';
$resultats = [];
$totalResultats = 0;

// Recherche dans la base de donnees
if (!empty($recherche) || !empty($genre) || $disponible !== '') {
    try {
        $sql = "SELECT * FROM livres WHERE 1=1";
        $params = [];
        
        if (!empty($recherche)) {
            $sql .= " AND (titre LIKE :recherche OR auteur LIKE :recherche2 OR isbn LIKE :recherche3)";
            $params[':recherche'] = '%' . $recherche . '%';
            $params[':recherche2'] = '%' . $recherche . '%';
            $params[':recherche3'] = '%' . $recherche . '%';
        }
        
        if (!empty($genre)) {
            $sql .= " AND genre = :genre";
            $params[':genre'] = $genre;
        }
        
        if ($disponible !== '') {
            if ($disponible === '1') {
                $sql .= " AND disponible > 0";
            } else {
                $sql .= " AND disponible = 0";
            }
        }
        
        $sql .= " ORDER BY titre ASC";
        
        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalResultats = count($resultats);
    } catch (PDOException $e) {
        $resultats = [];
    }
}

// Recuperer les genres pour le filtre
$genres = [];
try {
    $stmt = $db->query("SELECT DISTINCT genre FROM livres WHERE genre IS NOT NULL AND genre != '' ORDER BY genre");
    $genres = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $genres = [];
}

$pageTitle = "Rechercher un livre";
require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Rechercher un livre</h1>
        <p class="subtitle">Recherchez un livre par titre, auteur, ISBN, genre ou disponibilite</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Criteres de recherche</h2>
    </div>
    <div class="card-body">
        <form method="GET" action="">
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="q" class="form-label">Recherche par titre, auteur ou ISBN</label>
                    <input type="text" id="q" name="q" class="form-control" 
                           value="<?php echo htmlspecialchars($recherche); ?>" 
                           placeholder="Entrez un titre, un nom d'auteur ou un ISBN...">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="genre" class="form-label">Genre</label>
                    <select id="genre" name="genre" class="form-control">
                        <option value="">Tous les genres</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?php echo htmlspecialchars($g); ?>" <?php echo ($genre === $g) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="disponible" class="form-label">Disponibilite</label>
                    <select id="disponible" name="disponible" class="form-control">
                        <option value="">Tous</option>
                        <option value="1" <?php echo ($disponible === '1') ? 'selected' : ''; ?>>Disponible</option>
                        <option value="0" <?php echo ($disponible === '0') ? 'selected' : ''; ?>>Indisponible</option>
                    </select>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Rechercher</button>
                <a href="recherche.php" class="btn btn-secondary">Reinitialiser</a>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($recherche) || !empty($genre) || $disponible !== ''): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Resultats de recherche</h2>
        <span class="badge badge-success"><?php echo $totalResultats; ?> livre(s) trouve(s)</span>
    </div>
    <div class="card-body">
        <?php if ($totalResultats > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>ISBN</th>
                        <th>Annee</th>
                        <th>Genre</th>
                        <th>Disponibilite</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultats as $livre): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($livre['titre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                            <td><?php echo htmlspecialchars($livre['isbn'] ?? '-'); ?></td>
                            <td><?php echo $livre['annee_publication'] ?? '-'; ?></td>
                            <td>
                                <?php if (!empty($livre['genre'])): ?>
                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($livre['genre']); ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($livre['disponible'] > 0): ?>
                                    <span class="badge badge-success"><?php echo $livre['disponible']; ?>/<?php echo $livre['quantite']; ?></span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Indisponible</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="modifier_livre.php?id=<?php echo $livre['id']; ?>" class="btn btn-sm btn-secondary">Modifier</a>
                                <?php if ($livre['disponible'] > 0): ?>
                                    <a href="emprunts.php?livre_id=<?php echo $livre['id']; ?>" class="btn btn-sm btn-primary">Emprunter</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <h3>Aucun resultat</h3>
                <p>Aucun livre ne correspond a vos criteres de recherche.</p>
                <a href="recherche.php" class="btn btn-primary">Nouvelle recherche</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="info-box">
    <h3>Fonctionnalite de recherche</h3>
    <p>Cette page permet de rechercher des livres selon plusieurs criteres :</p>
    <ul>
        <li><strong>Titre, auteur ou ISBN</strong> : recherche textuelle</li>
        <li><strong>Genre</strong> : filtre par genre litteraire</li>
        <li><strong>Disponibilite</strong> : filtre les livres disponibles ou empruntes</li>
    </ul>
    <p>Les donnees sont recuperees depuis la base SQLite via des requetes SQL parametrees (protection contre les injections SQL).</p>
</div>

<?php require_once 'includes/footer.php'; ?>
