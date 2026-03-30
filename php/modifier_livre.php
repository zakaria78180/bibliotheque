<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';
$livre = null;

// Recuperer l'ID du livre
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: livres.php');
    exit;
}

// Recuperer les informations du livre
try {
    $stmt = $db->prepare("SELECT * FROM livres WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $livre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$livre) {
        header('Location: livres.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: livres.php');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $annee_publication = intval($_POST['annee_publication'] ?? 0);
    $genre = trim($_POST['genre'] ?? '');
    $quantite = intval($_POST['quantite'] ?? 1);
    
    // Validation des donnees
    $errors = [];
    if (empty($titre)) {
        $errors[] = "Le titre est obligatoire";
    }
    if (empty($auteur)) {
        $errors[] = "L'auteur est obligatoire";
    }
    if ($quantite < 1) {
        $errors[] = "La quantite doit etre au moins 1";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE livres SET titre = :titre, auteur = :auteur, isbn = :isbn, annee_publication = :annee, genre = :genre, quantite = :quantite WHERE id = :id");
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':auteur', $auteur);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':annee', $annee_publication);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $message = "Le livre a ete modifie avec succes !";
            $messageType = 'success';
            
            // Mettre a jour les donnees affichees
            $livre['titre'] = $titre;
            $livre['auteur'] = $auteur;
            $livre['isbn'] = $isbn;
            $livre['annee_publication'] = $annee_publication;
            $livre['genre'] = $genre;
            $livre['quantite'] = $quantite;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                $message = "Un livre avec cet ISBN existe deja.";
            } else {
                $message = "Erreur lors de la modification du livre.";
            }
            $messageType = 'danger';
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = 'danger';
    }
}

$pageTitle = "Modifier un livre";
require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Modifier un livre</h1>
        <p class="subtitle">Mettre a jour les informations du livre #<?php echo $id; ?></p>
    </div>
    <a href="livres.php" class="btn btn-secondary">Retour a la liste</a>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Informations du livre</h2>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="titre" class="form-label">Titre <span class="required">*</span></label>
                    <input type="text" id="titre" name="titre" class="form-control" 
                           value="<?php echo htmlspecialchars($livre['titre']); ?>" 
                           placeholder="Ex: Les Miserables" required>
                </div>
                
                <div class="form-group">
                    <label for="auteur" class="form-label">Auteur <span class="required">*</span></label>
                    <input type="text" id="auteur" name="auteur" class="form-control" 
                           value="<?php echo htmlspecialchars($livre['auteur']); ?>" 
                           placeholder="Ex: Victor Hugo" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" class="form-control" 
                           value="<?php echo htmlspecialchars($livre['isbn'] ?? ''); ?>" 
                           placeholder="Ex: 978-2070409228">
                </div>
                
                <div class="form-group">
                    <label for="annee_publication" class="form-label">Annee de publication</label>
                    <input type="number" id="annee_publication" name="annee_publication" class="form-control" 
                           value="<?php echo $livre['annee_publication'] ?? ''; ?>" 
                           min="1000" max="<?php echo date('Y'); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="genre" class="form-label">Genre</label>
                    <select id="genre" name="genre" class="form-control">
                        <option value="">Selectionner un genre</option>
                        <option value="Roman" <?php echo (($livre['genre'] ?? '') === 'Roman') ? 'selected' : ''; ?>>Roman</option>
                        <option value="Science-Fiction" <?php echo (($livre['genre'] ?? '') === 'Science-Fiction') ? 'selected' : ''; ?>>Science-Fiction</option>
                        <option value="Fantasy" <?php echo (($livre['genre'] ?? '') === 'Fantasy') ? 'selected' : ''; ?>>Fantasy</option>
                        <option value="Policier" <?php echo (($livre['genre'] ?? '') === 'Policier') ? 'selected' : ''; ?>>Policier</option>
                        <option value="Biographie" <?php echo (($livre['genre'] ?? '') === 'Biographie') ? 'selected' : ''; ?>>Biographie</option>
                        <option value="Histoire" <?php echo (($livre['genre'] ?? '') === 'Histoire') ? 'selected' : ''; ?>>Histoire</option>
                        <option value="Science" <?php echo (($livre['genre'] ?? '') === 'Science') ? 'selected' : ''; ?>>Science</option>
                        <option value="Philosophie" <?php echo (($livre['genre'] ?? '') === 'Philosophie') ? 'selected' : ''; ?>>Philosophie</option>
                        <option value="Poesie" <?php echo (($livre['genre'] ?? '') === 'Poesie') ? 'selected' : ''; ?>>Poesie</option>
                        <option value="Theatre" <?php echo (($livre['genre'] ?? '') === 'Theatre') ? 'selected' : ''; ?>>Theatre</option>
                        <option value="Conte" <?php echo (($livre['genre'] ?? '') === 'Conte') ? 'selected' : ''; ?>>Conte</option>
                        <option value="Autre" <?php echo (($livre['genre'] ?? '') === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantite" class="form-label">Quantite</label>
                    <input type="number" id="quantite" name="quantite" class="form-control" 
                           value="<?php echo $livre['quantite'] ?? 1; ?>" min="1">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="livres.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<div class="info-box">
    <h3>Maintenance evolutive</h3>
    <p>Cette page repond au besoin de <strong>maintenance evolutive</strong> de l'application.</p>
    <p>Elle permet de mettre a jour les informations d'un livre existant dans le respect des bonnes pratiques de developpement.</p>
    <p>Les modifications sont validees et enregistrees dans la base de donnees SQLite.</p>
</div>

<?php require_once 'includes/footer.php'; ?>
