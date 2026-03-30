<?php
/**
 * API REST pour la gestion des utilisateurs
 * 
 * Endpoints:
 * GET    /api/utilisateurs.php          - Liste tous les utilisateurs
 * GET    /api/utilisateurs.php?id=1     - Récupère un utilisateur par ID
 * POST   /api/utilisateurs.php          - Crée un nouvel utilisateur
 * PUT    /api/utilisateurs.php?id=1     - Modifie un utilisateur
 * DELETE /api/utilisateurs.php?id=1     - Supprime un utilisateur
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
                $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
                $stmt->execute([$id]);
                $utilisateur = $stmt->fetch();
                
                if ($utilisateur) {
                    echo json_encode([
                        'success' => true,
                        'data' => $utilisateur
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Utilisateur non trouvé'
                    ]);
                }
            } else {
                $stmt = $db->query("SELECT * FROM utilisateurs ORDER BY nom, prenom");
                $utilisateurs = $stmt->fetchAll();
                echo json_encode([
                    'success' => true,
                    'count' => count($utilisateurs),
                    'data' => $utilisateurs
                ]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['nom']) || !isset($input['prenom']) || !isset($input['email'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Les champs nom, prenom et email sont obligatoires'
                ]);
                exit;
            }
            
            $stmt = $db->prepare("
                INSERT INTO utilisateurs (nom, prenom, email, telephone, adresse) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['nom'],
                $input['prenom'],
                $input['email'],
                $input['telephone'] ?? null,
                $input['adresse'] ?? null
            ]);
            
            $newId = $db->lastInsertId();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'id' => $newId
            ]);
            break;
            
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de l\'utilisateur requis'
                ]);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $check = $db->prepare("SELECT id FROM utilisateurs WHERE id = ?");
            $check->execute([$id]);
            if (!$check->fetch()) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
                ]);
                exit;
            }
            
            $stmt = $db->prepare("
                UPDATE utilisateurs 
                SET nom = COALESCE(?, nom),
                    prenom = COALESCE(?, prenom),
                    email = COALESCE(?, email),
                    telephone = COALESCE(?, telephone),
                    adresse = COALESCE(?, adresse)
                WHERE id = ?
            ");
            $stmt->execute([
                $input['nom'] ?? null,
                $input['prenom'] ?? null,
                $input['email'] ?? null,
                $input['telephone'] ?? null,
                $input['adresse'] ?? null,
                $id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur modifié avec succès'
            ]);
            break;
            
        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'ID de l\'utilisateur requis'
                ]);
                exit;
            }
            
            $emprunts = $db->prepare("SELECT COUNT(*) FROM emprunts WHERE utilisateur_id = ? AND statut = 'en_cours'");
            $emprunts->execute([$id]);
            if ($emprunts->fetchColumn() > 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Impossible de supprimer: l\'utilisateur a des emprunts en cours'
                ]);
                exit;
            }
            
            $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Utilisateur supprimé avec succès'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Utilisateur non trouvé'
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
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'UNIQUE') !== false) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Cet email est déjà utilisé'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erreur serveur: ' . $e->getMessage()
        ]);
    }
}
?>
