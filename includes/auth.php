<?php
/**
 * Gestion de l'authentification et de la session utilisateur
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Retourne true si un utilisateur est connecté
 */
function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

/**
 * Retourne l'id de l'utilisateur connecté (ou null)
 */
function utilisateurConnecteId() {
    return estConnecte() ? intval($_SESSION['utilisateur_id']) : null;
}

/**
 * Retourne le nom complet de l'utilisateur connecté (ou null)
 */
function utilisateurConnecteNom() {
    return estConnecte() ? $_SESSION['utilisateur_nom'] : null;
}

/**
 * Connecte un utilisateur en stockant ses infos en session
 */
function connecterUtilisateur($utilisateur) {
    $_SESSION['utilisateur_id'] = $utilisateur['id'];
    $_SESSION['utilisateur_nom'] = $utilisateur['prenom'] . ' ' . $utilisateur['nom'];
}

/**
 * Déconnecte l'utilisateur courant
 */
function deconnecterUtilisateur() {
    unset($_SESSION['utilisateur_id'], $_SESSION['utilisateur_nom']);
}

/**
 * Force la connexion : redirige vers la page de connexion si non connecté
 */
function exigerConnexion() {
    if (!estConnecte()) {
        header('Location: connexion.php');
        exit;
    }
}
?>
