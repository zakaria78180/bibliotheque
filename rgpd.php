<?php
$pageTitle = "Conformite RGPD";
include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Conformite RGPD</h1>
        <p class="subtitle">Reglement General sur la Protection des Donnees - BTS SIO SLAM</p>
    </div>
</div>

<div class="alert alert-info" style="margin-bottom: 2rem;">
    <strong>Information BTS SIO :</strong> Cette page documente les mesures prises pour assurer la conformite au RGPD (Reglement UE 2016/679), 
    une competence evaluee dans le referentiel BTS SIO SLAM (Bloc 2 - Conception et developpement d'applications).
</div>

<!-- Vue d'ensemble -->
<div class="card">
    <h2 class="card-title">1. Vue d'ensemble de la conformite</h2>
    
    <div class="rgpd-grid">
        <div class="rgpd-item rgpd-success">
            <div class="rgpd-icon">✓</div>
            <div class="rgpd-content">
                <h4>Finalite definie</h4>
                <p>Les donnees sont collectees uniquement pour la gestion des emprunts de livres</p>
            </div>
        </div>
        
        <div class="rgpd-item rgpd-success">
            <div class="rgpd-icon">✓</div>
            <div class="rgpd-content">
                <h4>Minimisation des donnees</h4>
                <p>Seules les donnees necessaires sont collectees (nom, email, telephone)</p>
            </div>
        </div>
        
        <div class="rgpd-item rgpd-success">
            <div class="rgpd-icon">✓</div>
            <div class="rgpd-content">
                <h4>Securisation</h4>
                <p>Requetes preparees PDO, echappement XSS, validation des entrees</p>
            </div>
        </div>
        
        <div class="rgpd-item rgpd-success">
            <div class="rgpd-icon">✓</div>
            <div class="rgpd-content">
                <h4>Droits des personnes</h4>
                <p>Acces, rectification et suppression des donnees possibles</p>
            </div>
        </div>
    </div>
</div>

<!-- Donnees collectees -->
<div class="card">
    <h2 class="card-title">2. Donnees personnelles collectees</h2>
    
    <table>
        <thead>
            <tr>
                <th>Donnee</th>
                <th>Finalite</th>
                <th>Base legale</th>
                <th>Duree de conservation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Nom et Prenom</strong></td>
                <td>Identification de l'adherent</td>
                <td>Execution du contrat (adhesion)</td>
                <td>Duree de l'adhesion + 1 an</td>
            </tr>
            <tr>
                <td><strong>Adresse email</strong></td>
                <td>Communication, rappels de retour</td>
                <td>Execution du contrat</td>
                <td>Duree de l'adhesion + 1 an</td>
            </tr>
            <tr>
                <td><strong>Telephone</strong></td>
                <td>Contact en cas d'urgence</td>
                <td>Interet legitime</td>
                <td>Duree de l'adhesion + 1 an</td>
            </tr>
            <tr>
                <td><strong>Adresse postale</strong></td>
                <td>Envoi de courriers si necessaire</td>
                <td>Interet legitime</td>
                <td>Duree de l'adhesion + 1 an</td>
            </tr>
            <tr>
                <td><strong>Historique des emprunts</strong></td>
                <td>Suivi des emprunts et retards</td>
                <td>Execution du contrat</td>
                <td>3 ans apres le retour</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Droits des utilisateurs -->
<div class="card">
    <h2 class="card-title">3. Droits des personnes concernees</h2>
    
    <div class="rights-grid">
        <div class="right-item">
            <h4>Droit d'acces (Art. 15)</h4>
            <p>L'adherent peut demander une copie de toutes ses donnees personnelles.</p>
            <div class="right-implementation">
                <strong>Implementation :</strong> Page "Utilisateurs" > Consulter son profil
            </div>
        </div>
        
        <div class="right-item">
            <h4>Droit de rectification (Art. 16)</h4>
            <p>L'adherent peut corriger ses donnees inexactes ou incompletes.</p>
            <div class="right-implementation">
                <strong>Implementation :</strong> Page "Utilisateurs" > Modifier ses informations
            </div>
        </div>
        
        <div class="right-item">
            <h4>Droit a l'effacement (Art. 17)</h4>
            <p>L'adherent peut demander la suppression de ses donnees (si pas d'emprunt en cours).</p>
            <div class="right-implementation">
                <strong>Implementation :</strong> Page "Utilisateurs" > Supprimer le compte
            </div>
        </div>
        
        <div class="right-item">
            <h4>Droit a la portabilite (Art. 20)</h4>
            <p>L'adherent peut recevoir ses donnees dans un format structure (JSON).</p>
            <div class="right-implementation">
                <strong>Implementation :</strong> API REST /api/utilisateurs.php?id=X
            </div>
        </div>
    </div>
</div>

<!-- Mesures de securite -->
<div class="card">
    <h2 class="card-title">4. Mesures de securite techniques</h2>
    
    <div class="doc-block">
        <h3>4.1 Protection contre les injections SQL</h3>
        <p>Toutes les requetes utilisent des <strong>requetes preparees PDO</strong> :</p>
        <div class="code-block">
// Exemple de requete securisee
$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([intval($id)]);
$utilisateur = $stmt->fetch();
        </div>
    </div>
    
    <div class="doc-block">
        <h3>4.2 Protection contre les failles XSS</h3>
        <p>Toutes les donnees affichees sont echappees avec <strong>htmlspecialchars()</strong> :</p>
        <div class="code-block">
// Affichage securise des donnees
echo htmlspecialchars($utilisateur['nom'], ENT_QUOTES, 'UTF-8');
        </div>
    </div>
    
    <div class="doc-block">
        <h3>4.3 Validation des donnees</h3>
        <ul>
            <li>Validation du format email avec <code>filter_var($email, FILTER_VALIDATE_EMAIL)</code></li>
            <li>Verification des types de donnees (intval() pour les ID)</li>
            <li>Contrainte UNIQUE sur les emails pour eviter les doublons</li>
            <li>Verification de l'existence des enregistrements avant modification/suppression</li>
        </ul>
    </div>
    
    <div class="doc-block">
        <h3>4.4 Chiffrement et stockage</h3>
        <ul>
            <li>Base de donnees SQLite stockee localement (pas de transmission reseau)</li>
            <li>Fichier de base de donnees dans un dossier non accessible publiquement</li>
            <li>En production : HTTPS obligatoire pour les communications</li>
        </ul>
    </div>
</div>

<!-- Registre des traitements -->
<div class="card">
    <h2 class="card-title">5. Registre des traitements (Art. 30)</h2>
    
    <table>
        <thead>
            <tr>
                <th>Traitement</th>
                <th>Finalite</th>
                <th>Donnees concernees</th>
                <th>Destinataires</th>
                <th>Transfert hors UE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gestion des adherents</td>
                <td>Inscription et suivi des adherents</td>
                <td>Nom, prenom, email, telephone</td>
                <td>Personnel bibliotheque</td>
                <td>Non</td>
            </tr>
            <tr>
                <td>Gestion des emprunts</td>
                <td>Suivi des emprunts et retours</td>
                <td>ID adherent, ID livre, dates</td>
                <td>Personnel bibliotheque</td>
                <td>Non</td>
            </tr>
            <tr>
                <td>Rappels de retour</td>
                <td>Notifier les retards</td>
                <td>Email, emprunts en retard</td>
                <td>Adherent concerne</td>
                <td>Non</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Contact DPO -->
<div class="card">
    <h2 class="card-title">6. Contact et exercice des droits</h2>
    
    <div class="contact-box">
        <div class="contact-info">
            <h4>Delegue a la Protection des Donnees (DPO)</h4>
            <p><strong>Bibliotheque Municipale</strong></p>
            <p>Email : dpo@bibliotheque.fr</p>
            <p>Adresse : 1 Place de la Mairie, 75001 Paris</p>
        </div>
        
        <div class="contact-info">
            <h4>Autorite de controle</h4>
            <p><strong>CNIL - Commission Nationale de l'Informatique et des Libertes</strong></p>
            <p>Site web : <a href="https://www.cnil.fr" target="_blank">www.cnil.fr</a></p>
            <p>Adresse : 3 Place de Fontenoy, TSA 80715, 75334 Paris Cedex 07</p>
        </div>
    </div>
    
    <div class="alert alert-info" style="margin-top: 1rem;">
        <strong>Pour exercer vos droits :</strong> Envoyez un email a dpo@bibliotheque.fr en precisant votre demande 
        (acces, rectification, suppression, portabilite) et en joignant une piece d'identite.
    </div>
</div>

<!-- Annexe pour BTS -->
<div class="card">
    <h2 class="card-title">7. Annexe BTS SIO - Competences RGPD</h2>
    
    <p>Cette section documente les competences RGPD demontrees dans ce projet, conformement au referentiel BTS SIO SLAM :</p>
    
    <table>
        <thead>
            <tr>
                <th>Competence</th>
                <th>Element du projet</th>
                <th>Validation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Identifier les donnees personnelles</td>
                <td>Section 2 - Donnees collectees</td>
                <td><span class="badge badge-success">Valide</span></td>
            </tr>
            <tr>
                <td>Definir les finalites de traitement</td>
                <td>Sections 2 et 5 - Registre des traitements</td>
                <td><span class="badge badge-success">Valide</span></td>
            </tr>
            <tr>
                <td>Appliquer le principe de minimisation</td>
                <td>Collecte limitee aux donnees necessaires</td>
                <td><span class="badge badge-success">Valide</span></td>
            </tr>
            <tr>
                <td>Garantir les droits des personnes</td>
                <td>Section 3 - Fonctionnalites CRUD</td>
                <td><span class="badge badge-success">Valide</span></td>
            </tr>
            <tr>
                <td>Securiser les donnees</td>
                <td>Section 4 - Mesures techniques</td>
                <td><span class="badge badge-success">Valide</span></td>
            </tr>
            <tr>
                <td>Documenter la conformite</td>
                <td>Cette page complete</td>
                <td><span class="badge badge-success">Valide</span></td>
            </tr>
        </tbody>
    </table>
</div>

<style>
.rgpd-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.rgpd-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-radius: 0.5rem;
    background: #f8fafc;
}

.rgpd-success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
}

.rgpd-icon {
    font-size: 1.5rem;
    color: #16a34a;
    font-weight: bold;
}

.rgpd-content h4 {
    color: #166534;
    margin-bottom: 0.25rem;
}

.rgpd-content p {
    color: #15803d;
    font-size: 0.875rem;
}

.rights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
}

.right-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1rem;
}

.right-item h4 {
    color: #1e40af;
    margin-bottom: 0.5rem;
}

.right-item p {
    color: #475569;
    font-size: 0.875rem;
    margin-bottom: 0.75rem;
}

.right-implementation {
    background: #dbeafe;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    color: #1e40af;
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
}

.contact-box {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.contact-info {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
}

.contact-info h4 {
    color: #1e40af;
    margin-bottom: 0.75rem;
}

.contact-info p {
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.contact-info a {
    color: #2563eb;
}
</style>

<?php include 'includes/footer.php'; ?>
