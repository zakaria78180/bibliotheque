<?php
require_once 'config/database.php';

$db = Database::getInstance()->getConnection();
$message = '';
$messageType = '';

// Valeurs par défaut
$titre = $auteur = $isbn = $genre = '';
$annee_publication = date('Y');
$quantite = 1;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $annee_publication = intval($_POST['annee_publication'] ?? date('Y'));
    $genre = trim($_POST['genre'] ?? '');
    $quantite = intval($_POST['quantite'] ?? 1);
    
    // Validation des données
    $errors = [];
    if (empty($titre)) {
        $errors[] = "Le titre est obligatoire";
    }
    if (empty($auteur)) {
        $errors[] = "L'auteur est obligatoire";
    }
    if ($quantite < 1) {
        $errors[] = "La quantité doit être au moins 1";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("INSERT INTO livres (titre, auteur, isbn, annee_publication, genre, quantite, disponible) VALUES (:titre, :auteur, :isbn, :annee, :genre, :quantite, :disponible)");
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':auteur', $auteur);
            $stmt->bindParam(':isbn', $isbn);
            $stmt->bindParam(':annee', $annee_publication);
            $stmt->bindParam(':genre', $genre);
            $stmt->bindParam(':quantite', $quantite);
            $stmt->bindParam(':disponible', $quantite);
            $stmt->execute();
            
            $message = "Le livre a ete ajoute avec succes !";
            $messageType = 'success';
            
            // Reinitialiser les champs
            $titre = $auteur = $isbn = $genre = '';
            $annee_publication = date('Y');
            $quantite = 1;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                $message = "Un livre avec cet ISBN existe deja.";
            } else {
                $message = "Erreur lors de l'ajout du livre.";
            }
            $messageType = 'danger';
        }
    } else {
        $message = implode("<br>", $errors);
        $messageType = 'danger';
    }
}

$pageTitle = "Ajouter un livre";
require_once 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Ajouter un livre</h1>
        <p class="subtitle">Formulaire permettant d'ajouter un nouveau livre dans la base de donnees</p>
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
                           value="<?php echo htmlspecialchars($titre); ?>" 
                           placeholder="Ex: Les Miserables" required>
                </div>
                
                <div class="form-group">
                    <label for="auteur" class="form-label">Auteur <span class="required">*</span></label>
                    <input type="text" id="auteur" name="auteur" class="form-control" 
                           value="<?php echo htmlspecialchars($auteur); ?>" 
                           placeholder="Ex: Victor Hugo" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" id="isbn" name="isbn" class="form-control" 
                           value="<?php echo htmlspecialchars($isbn); ?>" 
                           placeholder="Ex: 978-2070409228">
                </div>
                
                <div class="form-group">
                    <label for="annee_publication" class="form-label">Annee de publication</label>
                    <input type="number" id="annee_publication" name="annee_publication" class="form-control" 
                           value="<?php echo $annee_publication; ?>" 
                           min="1000" max="<?php echo date('Y'); ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="genre" class="form-label">Genre</label>
                    <select id="genre" name="genre" class="form-control">
                        <option value="">Selectionner un genre</option>
                        <option value="Roman" <?php echo ($genre === 'Roman') ? 'selected' : ''; ?>>Roman</option>
                        <option value="Science-Fiction" <?php echo ($genre === 'Science-Fiction') ? 'selected' : ''; ?>>Science-Fiction</option>
                        <option value="Fantasy" <?php echo ($genre === 'Fantasy') ? 'selected' : ''; ?>>Fantasy</option>
                        <option value="Policier" <?php echo ($genre === 'Policier') ? 'selected' : ''; ?>>Policier</option>
                        <option value="Biographie" <?php echo ($genre === 'Biographie') ? 'selected' : ''; ?>>Biographie</option>
                        <option value="Histoire" <?php echo ($genre === 'Histoire') ? 'selected' : ''; ?>>Histoire</option>
                        <option value="Science" <?php echo ($genre === 'Science') ? 'selected' : ''; ?>>Science</option>
                        <option value="Philosophie" <?php echo ($genre === 'Philosophie') ? 'selected' : ''; ?>>Philosophie</option>
                        <option value="Poesie" <?php echo ($genre === 'Poesie') ? 'selected' : ''; ?>>Poesie</option>
                        <option value="Theatre" <?php echo ($genre === 'Theatre') ? 'selected' : ''; ?>>Theatre</option>
                        <option value="Conte" <?php echo ($genre === 'Conte') ? 'selected' : ''; ?>>Conte</option>
                        <option value="Autre" <?php echo ($genre === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="quantite" class="form-label">Quantite</label>
                    <input type="number" id="quantite" name="quantite" class="form-control" 
                           value="<?php echo $quantite; ?>" min="1">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer le livre</button>
                <a href="livres.php" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>

<div class="info-box">
    <h3>Informations</h3>
    <p>Les champs marques d'un <span class="required">*</span> sont obligatoires.</p>
    <p>Les donnees sont validees puis enregistrees dans la base de donnees SQLite.</p>
    <p>Cette page repond au besoin d'ajout de nouvelles ressources dans le systeme de gestion de bibliotheque.</p>
</div>

<?php require_once 'includes/footer.php'; ?>
