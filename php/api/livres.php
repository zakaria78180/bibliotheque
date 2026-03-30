<?php
/**
 * API REST pour la gestion des livres
 * 
 * Endpoints:
 * GET    /api/livres.php          - Liste tous les livres
 * GET    /api/livres.php?id=1     - Récupère un livre par ID
 * POST   /api/livres.php          - Crée un nouveau livre
 * PUT    /api/livres.php?id=1     - Modifie un livre
 * DELETE /api/livres.php?id=1     - Supprime un livre
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                // Récupérer un livre par ID
                $stmt = $db->prepare("SELECT * FROM livres WHERE id = ?");
                $stmt->execute([$id]);
                $livre = $stmt->fetch();
                
                if ($livre) {
                    echo json_encode([
                        'success' => true,
                        'data' => $livre
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Livre non trouvé'
                    ]);
                }
            } else {
                // Lister tous les livres
                $recherche = isset($_GET['recherche']) ? $_GET['recherche'] : '';
                
                if ($recherche) {
                    $stmt = $db->prepare("SELECT * FROM livres WHERE titre LIKE ? OR auteur LIKE ? OR isbn LIKE ? ORDER BY titre");
                    $searchTerm = '%' . $recherche . '%';
                    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
                } else {
                    $stmt = $db->query("SELECT * FROM livres ORDER BY titre");
                }
                
                $livres = $stmt->fetchAll();
                echo json_encode([
                    'success' => true,
                    'count' => count($livres),
                    'data' => $livres
                ]);
            }
            break;
            
        case 'POST':
            // Créer un nouveau livre
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['titre']) || !isset($input['auteur'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Les champs titre et auteur sont obligatoires'
                ]);
                exit;
            }
            
            $quantite = isset($input['quantite']) ? intval($input['quantite']) : 1;
            
            $stmt = $db->prepare("
                INSERT INTO livres (titre, auteur, isbn, annee_publication, genre, quantite, disponible) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['titre'],
                $input['auteur'],
                $input['isbn'] ?? null,
                $input['annee_publication'] ?? null,
                $input['genre'] ?? null,
                $quantite,
                $quantite
            ]);
            
            $newId = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Livre créé avec succès',
                'id' => $newId
            ]);
            break;
            
        case 'PUT':
            // Modifier un livre
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID du livre requis'
                ]);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Vérifier que le livre existe
            $check = $db->prepare("SELECT id FROM livres WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Livre non trouvé'
                ]);
                exit;
            }
            
            $stmt = $db->prepare("
                UPDATE livres 
                SET titre = COALESCE(?, titre),
                    auteur = COALESCE(?, auteur),
                    isbn = COALESCE(?, isbn),
                    annee_publication = COALESCE(?, annee_publication),
                    genre = COALESCE(?, genre),
                    quantite = COALESCE(?, quantite)
                WHERE id = ?
            ");
            $stmt->execute([
                $input['titre'] ?? null,
                $input['auteur'] ?? null,
                $input['isbn'] ?? null,
                $input['annee_publication'] ?? null,
                $input['genre'] ?? null,
                $input['quantite'] ?? null,
                $id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Livre modifié avec succès'
            ]);
            break;
            
        case 'DELETE':
            // Supprimer un livre
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID du livre requis'
                ]);
                exit;
            }
            
            // Vérifier si le livre est emprunté
            $emprunts = $db->prepare("SELECT COUNT(*) FROM emprunts WHERE livre_id = ? AND statut = 'en_cours'");
            $emprunts->execute([$id]);
            if ($emprunts->fetchColumn() > 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Impossible de supprimer: le livre est actuellement emprunté'
                ]);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM livres WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Livre supprimé avec succès'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Livre non trouvé'
                ]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Méthode non autorisée'
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>
