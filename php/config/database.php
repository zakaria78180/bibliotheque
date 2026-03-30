<?php
/**
 * Configuration de la base de données SQLite
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dbPath = __DIR__ . '/../database/bibliotheque.db';
            
            // Créer le dossier database s'il n'existe pas
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $this->connection = new PDO("sqlite:" . $dbPath);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Créer les tables si elles n'existent pas
            $this->createTables();
            
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
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
    
    private function createTables() {
        // Table des livres
        $this->connection->exec("
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
            )
        ");
        
        // Table des utilisateurs
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS utilisateurs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                telephone VARCHAR(20),
                adresse TEXT,
                date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
                actif INTEGER DEFAULT 1
            )
        ");
        
        // Table des emprunts
        $this->connection->exec("
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
            )
        ");
        
        // Insérer des données de démonstration si les tables sont vides
        $this->insertDemoData();
    }
    
    private function insertDemoData() {
        // Vérifier si des données existent déjà
        $stmt = $this->connection->query("SELECT COUNT(*) as count FROM livres");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            // Insérer des livres de démonstration
            $this->connection->exec("
                INSERT INTO livres (titre, auteur, isbn, annee_publication, genre, quantite, disponible) VALUES
                ('Le Petit Prince', 'Antoine de Saint-Exupéry', '978-2070612758', 1943, 'Conte', 3, 3),
                ('Les Misérables', 'Victor Hugo', '978-2070409228', 1862, 'Roman', 2, 2),
                ('L''Étranger', 'Albert Camus', '978-2070360024', 1942, 'Roman', 2, 2),
                ('Germinal', 'Émile Zola', '978-2070413836', 1885, 'Roman', 1, 1),
                ('Madame Bovary', 'Gustave Flaubert', '978-2070413119', 1857, 'Roman', 2, 2)
            ");
            
            // Insérer des utilisateurs de démonstration
            $this->connection->exec("
                INSERT INTO utilisateurs (nom, prenom, email, telephone) VALUES
                ('Dupont', 'Marie', 'marie.dupont@email.com', '0601020304'),
                ('Martin', 'Jean', 'jean.martin@email.com', '0605060708'),
                ('Bernard', 'Sophie', 'sophie.bernard@email.com', '0609101112')
            ");
        }
    }
}
?>
