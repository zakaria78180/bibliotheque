<?php
$pageTitle = "Documentation";
include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Documentation Technique</h1>
        <p class="subtitle">Cahier des charges, conception et documentation du projet BTS SIO SLAM</p>
    </div>
</div>

<!-- Navigation Documentation -->
<div class="doc-nav">
    <a href="#cahier-charges" class="doc-nav-item active">Cahier des charges</a>
    <a href="#mcd" class="doc-nav-item">MCD</a>
    <a href="#mld" class="doc-nav-item">MLD</a>
    <a href="#architecture" class="doc-nav-item">Architecture</a>
    <a href="#api" class="doc-nav-item">API REST</a>
    <a href="#securite" class="doc-nav-item">Securite</a>
</div>

<!-- Cahier des charges -->
<section id="cahier-charges" class="card doc-section">
    <h2 class="card-title">1. Cahier des Charges</h2>
    
    <div class="doc-block">
        <h3>1.1 Contexte du projet</h3>
        <p>La bibliotheque municipale souhaite moderniser sa gestion des ouvrages et des emprunts. Le systeme actuel, base sur des fiches papier, ne permet pas un suivi efficace des livres et des adherents.</p>
    </div>
    
    <div class="doc-block">
        <h3>1.2 Objectifs</h3>
        <ul>
            <li>Gerer le catalogue des livres (ajout, modification, suppression, recherche)</li>
            <li>Gerer les adherents/utilisateurs de la bibliotheque</li>
            <li>Suivre les emprunts et retours de livres</li>
            <li>Calculer automatiquement les disponibilites</li>
            <li>Fournir des statistiques sur l'activite de la bibliotheque</li>
        </ul>
    </div>
    
    <div class="doc-block">
        <h3>1.3 Besoins utilisateurs</h3>
        <table>
            <thead>
                <tr>
                    <th>Acteur</th>
                    <th>Besoin</th>
                    <th>Priorite</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bibliothecaire</td>
                    <td>Ajouter/modifier/supprimer des livres</td>
                    <td><span class="badge badge-danger">Haute</span></td>
                </tr>
                <tr>
                    <td>Bibliothecaire</td>
                    <td>Gerer les adherents</td>
                    <td><span class="badge badge-danger">Haute</span></td>
                </tr>
                <tr>
                    <td>Bibliothecaire</td>
                    <td>Enregistrer les emprunts et retours</td>
                    <td><span class="badge badge-danger">Haute</span></td>
                </tr>
                <tr>
                    <td>Bibliothecaire</td>
                    <td>Rechercher un livre par titre, auteur ou ISBN</td>
                    <td><span class="badge badge-warning">Moyenne</span></td>
                </tr>
                <tr>
                    <td>Bibliothecaire</td>
                    <td>Consulter les statistiques</td>
                    <td><span class="badge badge-success">Basse</span></td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="doc-block">
        <h3>1.4 Contraintes techniques</h3>
        <ul>
            <li><strong>Langage backend :</strong> PHP 8.x</li>
            <li><strong>Base de donnees :</strong> SQLite (portable, sans installation serveur)</li>
            <li><strong>Frontend :</strong> HTML5, CSS3, JavaScript vanilla</li>
            <li><strong>Architecture :</strong> MVC simplifie</li>
            <li><strong>API :</strong> REST avec reponses JSON</li>
            <li><strong>Securite :</strong> Requetes preparees PDO, echappement XSS</li>
        </ul>
    </div>
    
    <div class="doc-block">
        <h3>1.5 Contraintes juridiques (RGPD)</h3>
        <ul>
            <li>Collecte minimale des donnees personnelles (nom, prenom, email, telephone)</li>
            <li>Finalite clairement definie : gestion des emprunts de livres</li>
            <li>Duree de conservation limitee des donnees</li>
            <li>Droit d'acces, de rectification et de suppression pour les utilisateurs</li>
            <li>Securisation des donnees personnelles</li>
        </ul>
        <p><a href="rgpd.php" class="btn btn-secondary btn-sm">Voir la page RGPD complete</a></p>
    </div>
</section>

<!-- MCD -->
<section id="mcd" class="card doc-section">
    <h2 class="card-title">2. Modele Conceptuel de Donnees (MCD)</h2>
    
    <div class="doc-block">
        <h3>2.1 Description des entites</h3>
        <p>Le MCD represente les entites metier et leurs associations:</p>
    </div>
    
    <div class="mcd-diagram">
        <div class="mcd-entity">
            <div class="mcd-entity-header">LIVRE</div>
            <div class="mcd-entity-body">
                <div class="mcd-attribute"><span class="mcd-key">#</span> id</div>
                <div class="mcd-attribute">titre</div>
                <div class="mcd-attribute">auteur</div>
                <div class="mcd-attribute">isbn</div>
                <div class="mcd-attribute">annee_publication</div>
                <div class="mcd-attribute">genre</div>
                <div class="mcd-attribute">quantite</div>
                <div class="mcd-attribute">disponible</div>
            </div>
        </div>
        
        <div class="mcd-relation">
            <div class="mcd-relation-line"></div>
            <div class="mcd-relation-name">EMPRUNTE</div>
            <div class="mcd-cardinality">
                <span class="card-left">1,n</span>
                <span class="card-right">0,n</span>
            </div>
        </div>
        
        <div class="mcd-entity">
            <div class="mcd-entity-header">UTILISATEUR</div>
            <div class="mcd-entity-body">
                <div class="mcd-attribute"><span class="mcd-key">#</span> id</div>
                <div class="mcd-attribute">nom</div>
                <div class="mcd-attribute">prenom</div>
                <div class="mcd-attribute">email</div>
                <div class="mcd-attribute">telephone</div>
                <div class="mcd-attribute">adresse</div>
                <div class="mcd-attribute">date_inscription</div>
                <div class="mcd-attribute">actif</div>
            </div>
        </div>
    </div>
    
    <div class="doc-block" style="margin-top: 2rem;">
        <h3>2.2 Cardinalites</h3>
        <table>
            <thead>
                <tr>
                    <th>Association</th>
                    <th>Cardinalite</th>
                    <th>Explication</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>LIVRE - EMPRUNTE</td>
                    <td>1,n</td>
                    <td>Un livre peut etre emprunte plusieurs fois</td>
                </tr>
                <tr>
                    <td>UTILISATEUR - EMPRUNTE</td>
                    <td>0,n</td>
                    <td>Un utilisateur peut emprunter 0 a plusieurs livres</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="doc-block">
        <h3>2.3 Dictionnaire de donnees</h3>
        <table>
            <thead>
                <tr>
                    <th>Attribut</th>
                    <th>Type</th>
                    <th>Taille</th>
                    <th>Description</th>
                    <th>Contrainte</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>livre.id</td><td>INTEGER</td><td>-</td><td>Identifiant unique</td><td>PK, AUTO_INCREMENT</td></tr>
                <tr><td>livre.titre</td><td>VARCHAR</td><td>255</td><td>Titre du livre</td><td>NOT NULL</td></tr>
                <tr><td>livre.auteur</td><td>VARCHAR</td><td>255</td><td>Auteur du livre</td><td>NOT NULL</td></tr>
                <tr><td>livre.isbn</td><td>VARCHAR</td><td>20</td><td>Numero ISBN</td><td>UNIQUE</td></tr>
                <tr><td>livre.annee_publication</td><td>INTEGER</td><td>4</td><td>Annee de publication</td><td>-</td></tr>
                <tr><td>livre.genre</td><td>VARCHAR</td><td>100</td><td>Genre litteraire</td><td>-</td></tr>
                <tr><td>livre.quantite</td><td>INTEGER</td><td>-</td><td>Nombre d'exemplaires</td><td>DEFAULT 1</td></tr>
                <tr><td>livre.disponible</td><td>INTEGER</td><td>-</td><td>Exemplaires disponibles</td><td>DEFAULT 1</td></tr>
                <tr><td>utilisateur.id</td><td>INTEGER</td><td>-</td><td>Identifiant unique</td><td>PK, AUTO_INCREMENT</td></tr>
                <tr><td>utilisateur.nom</td><td>VARCHAR</td><td>100</td><td>Nom de famille</td><td>NOT NULL</td></tr>
                <tr><td>utilisateur.prenom</td><td>VARCHAR</td><td>100</td><td>Prenom</td><td>NOT NULL</td></tr>
                <tr><td>utilisateur.email</td><td>VARCHAR</td><td>255</td><td>Adresse email</td><td>UNIQUE, NOT NULL</td></tr>
                <tr><td>utilisateur.telephone</td><td>VARCHAR</td><td>20</td><td>Numero de telephone</td><td>-</td></tr>
                <tr><td>emprunt.id</td><td>INTEGER</td><td>-</td><td>Identifiant unique</td><td>PK, AUTO_INCREMENT</td></tr>
                <tr><td>emprunt.livre_id</td><td>INTEGER</td><td>-</td><td>Reference au livre</td><td>FK, NOT NULL</td></tr>
                <tr><td>emprunt.utilisateur_id</td><td>INTEGER</td><td>-</td><td>Reference a l'utilisateur</td><td>FK, NOT NULL</td></tr>
                <tr><td>emprunt.date_emprunt</td><td>DATETIME</td><td>-</td><td>Date de l'emprunt</td><td>DEFAULT NOW</td></tr>
                <tr><td>emprunt.date_retour_prevue</td><td>DATE</td><td>-</td><td>Date de retour prevue</td><td>NOT NULL</td></tr>
                <tr><td>emprunt.statut</td><td>VARCHAR</td><td>20</td><td>Statut de l'emprunt</td><td>DEFAULT 'en_cours'</td></tr>
            </tbody>
        </table>
    </div>
</section>

<!-- MLD -->
<section id="mld" class="card doc-section">
    <h2 class="card-title">3. Modele Logique de Donnees (MLD)</h2>
    
    <div class="doc-block">
        <h3>3.1 Schema relationnel</h3>
        <div class="code-block">
<strong>LIVRES</strong> (<u>id</u>, titre, auteur, isbn, annee_publication, genre, quantite, disponible, date_ajout)

<strong>UTILISATEURS</strong> (<u>id</u>, nom, prenom, email, telephone, adresse, date_inscription, actif)

<strong>EMPRUNTS</strong> (<u>id</u>, #livre_id, #utilisateur_id, date_emprunt, date_retour_prevue, date_retour_effective, statut)
        </div>
        <p><em>Legende : <u>Cle primaire</u>, #Cle etrangere</em></p>
    </div>
    
    <div class="doc-block">
        <h3>3.2 Script SQL de creation</h3>
        <div class="code-block">
-- Table LIVRES
CREATE TABLE IF NOT EXISTS livres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    annee_publication INTEGER,
    genre VARCHAR(100),
    quantite INTEGER DEFAULT 1,
    disponible INTEGER DEFAULT 1,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table UTILISATEURS
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    actif INTEGER DEFAULT 1
);

-- Table EMPRUNTS
CREATE TABLE IF NOT EXISTS emprunts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    livre_id INTEGER NOT NULL,
    utilisateur_id INTEGER NOT NULL,
    date_emprunt DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_retour_prevue DATE NOT NULL,
    date_retour_effective DATE,
    statut VARCHAR(20) DEFAULT 'en_cours',
    FOREIGN KEY (livre_id) REFERENCES livres(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);
        </div>
    </div>
</section>

<!-- Architecture -->
<section id="architecture" class="card doc-section">
    <h2 class="card-title">4. Architecture de l'Application</h2>
    
    <div class="doc-block">
        <h3>4.1 Structure des fichiers</h3>
        <div class="code-block">
php/
+-- config/
|   +-- database.php          # Connexion PDO + creation tables (Singleton)
+-- includes/
|   +-- header.php            # En-tete HTML + CSS + Navigation
|   +-- footer.php            # Pied de page HTML
+-- api/
|   +-- livres.php            # API REST pour les livres
|   +-- utilisateurs.php      # API REST pour les utilisateurs
|   +-- emprunts.php          # API REST pour les emprunts
+-- database/
|   +-- bibliotheque.db       # Base SQLite (creee automatiquement)
+-- index.php                 # Tableau de bord (statistiques)
+-- livres.php                # CRUD livres
+-- utilisateurs.php          # CRUD utilisateurs
+-- emprunts.php              # Gestion emprunts/retours
+-- recherche.php             # Recherche avancee
+-- documentation.php         # Cette page
+-- tests.php                 # Interface de tests
+-- rgpd.php                  # Conformite RGPD
+-- README.txt                # Documentation d'installation
        </div>
    </div>
    
    <div class="doc-block">
        <h3>4.2 Pattern Singleton (database.php)</h3>
        <p>La connexion a la base de donnees utilise le pattern Singleton pour garantir une instance unique:</p>
        <div class="code-block">
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $this->connection = new PDO("sqlite:" . $dbPath);
        // Configuration PDO...
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Utilisation :
$db = Database::getInstance()->getConnection();
        </div>
    </div>
    
    <div class="doc-block">
        <h3>4.3 Diagramme de composants</h3>
        <div class="architecture-diagram">
            <div class="arch-layer">
                <div class="arch-label">PRESENTATION</div>
                <div class="arch-components">
                    <div class="arch-comp">header.php</div>
                    <div class="arch-comp">footer.php</div>
                    <div class="arch-comp">CSS integre</div>
                </div>
            </div>
            <div class="arch-arrow">v</div>
            <div class="arch-layer">
                <div class="arch-label">PAGES (Controleurs)</div>
                <div class="arch-components">
                    <div class="arch-comp">index.php</div>
                    <div class="arch-comp">livres.php</div>
                    <div class="arch-comp">utilisateurs.php</div>
                    <div class="arch-comp">emprunts.php</div>
                </div>
            </div>
            <div class="arch-arrow">v</div>
            <div class="arch-layer">
                <div class="arch-label">API REST</div>
                <div class="arch-components">
                    <div class="arch-comp">api/livres.php</div>
                    <div class="arch-comp">api/utilisateurs.php</div>
                    <div class="arch-comp">api/emprunts.php</div>
                </div>
            </div>
            <div class="arch-arrow">v</div>
            <div class="arch-layer">
                <div class="arch-label">DONNEES</div>
                <div class="arch-components">
                    <div class="arch-comp">database.php (PDO)</div>
                    <div class="arch-comp">bibliotheque.db (SQLite)</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- API REST -->
<section id="api" class="card doc-section">
    <h2 class="card-title">5. Documentation API REST</h2>
    
    <div class="doc-block">
        <h3>5.1 Endpoints Livres</h3>
        <table>
            <thead>
                <tr>
                    <th>Methode</th>
                    <th>Endpoint</th>
                    <th>Description</th>
                    <th>Corps (JSON)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-success">GET</span></td>
                    <td>/api/livres.php</td>
                    <td>Liste tous les livres</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><span class="badge badge-success">GET</span></td>
                    <td>/api/livres.php?id=1</td>
                    <td>Recupere un livre par ID</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td><span class="badge badge-warning">POST</span></td>
                    <td>/api/livres.php</td>
                    <td>Ajoute un nouveau livre</td>
                    <td>{"titre": "...", "auteur": "..."}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-primary">PUT</span></td>
                    <td>/api/livres.php?id=1</td>
                    <td>Modifie un livre existant</td>
                    <td>{"titre": "...", "auteur": "..."}</td>
                </tr>
                <tr>
                    <td><span class="badge badge-danger">DELETE</span></td>
                    <td>/api/livres.php?id=1</td>
                    <td>Supprime un livre</td>
                    <td>-</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="doc-block">
        <h3>5.2 Exemple de reponse JSON</h3>
        <div class="code-block">
// GET /api/livres.php
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titre": "Le Petit Prince",
            "auteur": "Antoine de Saint-Exupery",
            "isbn": "978-2070612758",
            "annee_publication": 1943,
            "genre": "Conte",
            "quantite": 3,
            "disponible": 3
        }
    ]
}

// Erreur
{
    "success": false,
    "error": "Livre non trouve"
}
        </div>
    </div>
    
    <p style="margin-top: 1rem;"><a href="tests.php" class="btn btn-primary">Tester l'API en direct</a></p>
</section>

<!-- Securite -->
<section id="securite" class="card doc-section">
    <h2 class="card-title">6. Securite</h2>
    
    <div class="doc-block">
        <h3>6.1 Protection contre les injections SQL</h3>
        <p>Toutes les requetes utilisent des <strong>requetes preparees PDO</strong> :</p>
        <div class="code-block">
// MAUVAISE PRATIQUE (vulnerable)
$sql = "SELECT * FROM livres WHERE id = " . $_GET['id'];

// BONNE PRATIQUE (securise)
$stmt = $db->prepare("SELECT * FROM livres WHERE id = ?");
$stmt->execute([$_GET['id']]);
        </div>
    </div>
    
    <div class="doc-block">
        <h3>6.2 Protection contre les failles XSS</h3>
        <p>Toutes les sorties utilisateur sont echappees avec <strong>htmlspecialchars()</strong> :</p>
        <div class="code-block">
// MAUVAISE PRATIQUE (vulnerable)
echo $_POST['titre'];

// BONNE PRATIQUE (securise)
echo htmlspecialchars($livre['titre'], ENT_QUOTES, 'UTF-8');
        </div>
    </div>
    
    <div class="doc-block">
        <h3>6.3 Validation des donnees</h3>
        <ul>
            <li>Verification des types de donnees (intval pour les ID)</li>
            <li>Validation des emails avec filter_var()</li>
            <li>Verification de l'existence des enregistrements avant modification/suppression</li>
            <li>Contraintes UNIQUE sur email et ISBN</li>
        </ul>
    </div>
</section>

<style>
.doc-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.doc-nav-item {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    color: #64748b;
    font-weight: 500;
    transition: all 0.2s;
}

.doc-nav-item:hover,
.doc-nav-item.active {
    background: #2563eb;
    color: white;
}

.doc-section {
    scroll-margin-top: 2rem;
}

.doc-block {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.doc-block:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.doc-block h3 {
    color: #1e40af;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.doc-block ul {
    margin-left: 1.5rem;
}

.doc-block li {
    margin-bottom: 0.5rem;
}

.code-block {
    background: #1e293b;
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 0.5rem;
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 0.8rem;
    overflow-x: auto;
    white-space: pre;
    line-height: 1.5;
}

/* MCD Diagram */
.mcd-diagram {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    flex-wrap: wrap;
}

.mcd-entity {
    background: white;
    border: 2px solid #2563eb;
    border-radius: 0.5rem;
    min-width: 180px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mcd-entity-header {
    background: #2563eb;
    color: white;
    padding: 0.75rem;
    font-weight: 700;
    text-align: center;
}

.mcd-entity-body {
    padding: 0.75rem;
}

.mcd-attribute {
    padding: 0.25rem 0;
    font-size: 0.875rem;
    border-bottom: 1px dashed #e2e8f0;
}

.mcd-attribute:last-child {
    border-bottom: none;
}

.mcd-key {
    color: #dc2626;
    font-weight: 700;
}

.mcd-relation {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.mcd-relation-line {
    width: 80px;
    height: 2px;
    background: #64748b;
}

.mcd-relation-name {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-weight: 600;
    font-size: 0.75rem;
}

.mcd-cardinality {
    display: flex;
    justify-content: space-between;
    width: 100px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #dc2626;
}

/* Architecture Diagram */
.architecture-diagram {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 0.5rem;
}

.arch-layer {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 100%;
    max-width: 600px;
}

.arch-label {
    background: #1e40af;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    font-size: 0.75rem;
    min-width: 140px;
    text-align: center;
}

.arch-components {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    flex: 1;
}

.arch-comp {
    background: white;
    border: 1px solid #e2e8f0;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-family: monospace;
}

.arch-arrow {
    color: #64748b;
    font-size: 1.5rem;
    font-weight: bold;
}

.badge-primary {
    background-color: #dbeafe;
    color: #1e40af;
}

@media (max-width: 768px) {
    .mcd-diagram {
        flex-direction: column;
    }
    
    .mcd-relation {
        transform: rotate(90deg);
    }
    
    .arch-layer {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
