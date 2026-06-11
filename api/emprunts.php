<?php
/**
 * API REST pour la gestion des emprunts
 * 
 * Endpoints:
 * GET    /api/emprunts.php              - Liste tous les emprunts
 * GET    /api/emprunts.php?id=1         - Récupère un emprunt par ID
 * POST   /api/emprunts.php              - Crée un nouvel emprunt
 * PUT    /api/emprunts.php?id=1&retour  - Enregistre le retour d'un livre
 * DELETE /api/emprunts.php?id=1         - Supprime un emprunt
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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
                $stmt = $db->prepare("
                    SELECT e.*, l.titre as livre_titre, l.auteur as livre_auteur, 
                           u.nom as utilisateur_nom, u.prenom as utilisateur_prenom
                    FROM emprunts e 
                    JOIN livres l ON e.livre_id = l.id 
                    JOIN utilisateurs u ON e.utilisateur_id = u.id 
                    WHERE e.id = ?
                ");
                $stmt->execute([$id]);
                $emprunt = $stmt->fetch();
                
                if ($emprunt) {
                    echo json_encode([
                        'success' => true,
                        'data' => $emprunt
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Emprunt non trouvé'
                    ]);
                }
            } else {
                $filtre = isset($_GET['statut']) ? $_GET['statut'] : '';
                
                if ($filtre) {
                    $stmt = $db->prepare("
                        SELECT e.*, l.titre as livre_titre, l.auteur as livre_auteur, 
                               u.nom as utilisateur_nom, u.prenom as utilisateur_prenom
                        FROM emprunts e 
                        JOIN livres l ON e.livre_id = l.id 
                        JOIN utilisateurs u ON e.utilisateur_id = u.id 
                        WHERE e.statut = ?
                        ORDER BY e.date_emprunt DESC
                    ");
                    $stmt->execute([$filtre]);
                } else {
                    $stmt = $db->query("
                        SELECT e.*, l.titre as livre_titre, l.auteur as livre_auteur, 
                               u.nom as utilisateur_nom, u.prenom as utilisateur_prenom
                        FROM emprunts e 
                        JOIN livres l ON e.livre_id = l.id 
                        JOIN utilisateurs u ON e.utilisateur_id = u.id 
                        ORDER BY e.date_emprunt DESC
                    ");
                }
                
                $emprunts = $stmt->fetchAll();
                echo json_encode([
                    'success' => true,
                    'count' => count($emprunts),
                    'data' => $emprunts
                ]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['livre_id']) || !isset($input['utilisateur_id']) || !isset($input['date_retour_prevue'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Les champs livre_id, utilisateur_id et date_retour_prevue sont obligatoires'
                ]);
                exit;
            }
            
            $livreId = intval($input['livre_id']);
            $utilisateurId = intval($input['utilisateur_id']);
            
            // Vérifier la disponibilité
            $livre = $db->prepare("SELECT disponible FROM livres WHERE id = ?");
            $livre->execute([$livreId]);
            $livreData = $livre->fetch();
            
            if (!$livreData) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Livre non trouvé'
                ]);
                exit;
            }
            
            if ($livreData['disponible'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Ce livre n\'est pas disponible'
                ]);
                exit;
            }
            
            // Créer l'emprunt
            $stmt = $db->prepare("
                INSERT INTO emprunts (livre_id, utilisateur_id, date_retour_prevue, statut) 
                VALUES (?, ?, ?, 'en_cours')
            ");
            $stmt->execute([$livreId, $utilisateurId, $input['date_retour_prevue']]);
            
            // Mettre à jour la disponibilité
            $db->prepare("UPDATE livres SET disponible = disponible - 1 WHERE id = ?")->execute([$livreId]);
            
            $newId = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Emprunt enregistré avec succès',
                'id' => $newId
            ]);
            break;
            
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de l\'emprunt requis'
                ]);
                exit;
            }
            
            // Enregistrer le retour
            if (isset($_GET['retour'])) {
                $emprunt = $db->prepare("SELECT livre_id, statut FROM emprunts WHERE id = ?");
                $emprunt->execute([$id]);
                $empruntData = $emprunt->fetch();
                
                if (!$empruntData) {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Emprunt non trouvé'
                    ]);
                    exit;
                }
                
                if ($empruntData['statut'] !== 'en_cours') {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Ce livre a déjà été retourné'
                    ]);
                    exit;
                }
                
                // Marquer comme retourné
                $stmt = $db->prepare("UPDATE emprunts SET statut = 'retourne', date_retour_effective = DATE('now') WHERE id = ?");
                $stmt->execute([$id]);
                
                // Remettre le livre disponible
                $db->prepare("UPDATE livres SET disponible = disponible + 1 WHERE id = ?")->execute([$empruntData['livre_id']]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Retour enregistré avec succès'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Action non reconnue. Utilisez ?retour pour enregistrer un retour.'
                ]);
            }
            break;
            
        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de l\'emprunt requis'
                ]);
                exit;
            }
            
            // Récupérer l'emprunt
            $emprunt = $db->prepare("SELECT livre_id, statut FROM emprunts WHERE id = ?");
            $emprunt->execute([$id]);
            $empruntData = $emprunt->fetch();
            
            if (!$empruntData) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Emprunt non trouvé'
                ]);
                exit;
            }
            
            // Si emprunt en cours, remettre le livre disponible
            if ($empruntData['statut'] === 'en_cours') {
                $db->prepare("UPDATE livres SET disponible = disponible + 1 WHERE id = ?")->execute([$empruntData['livre_id']]);
            }
            
            $stmt = $db->prepare("DELETE FROM emprunts WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Emprunt supprimé avec succès'
            ]);
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
