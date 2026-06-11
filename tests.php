<?php
$pageTitle = "Tests";
include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1>Tests de l'Application</h1>
        <p class="subtitle">Tests unitaires, fonctionnels et API - BTS SIO SLAM</p>
    </div>
</div>

<div class="tests-summary card">
    <h2 class="card-title">Resume des tests</h2>
    <div class="tests-stats">
        <div class="test-stat">
            <span class="test-stat-value" id="total-tests">0</span>
            <span class="test-stat-label">Tests executes</span>
        </div>
        <div class="test-stat test-stat-success">
            <span class="test-stat-value" id="passed-tests">0</span>
            <span class="test-stat-label">Reussis</span>
        </div>
        <div class="test-stat test-stat-error">
            <span class="test-stat-value" id="failed-tests">0</span>
            <span class="test-stat-label">Echoues</span>
        </div>
    </div>
    <button onclick="runAllTests()" class="btn btn-primary" style="margin-top: 1rem;">Lancer tous les tests</button>
</div>

<!-- Tests Unitaires -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">1. Tests Unitaires - Base de donnees</h2>
        <button onclick="runDatabaseTests()" class="btn btn-secondary btn-sm">Executer</button>
    </div>
    <div id="db-tests-results" class="tests-results">
        <p class="test-pending">Cliquez sur "Executer" pour lancer les tests</p>
    </div>
</div>

<!-- Tests Fonctionnels -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">2. Tests Fonctionnels - CRUD</h2>
        <button onclick="runCrudTests()" class="btn btn-secondary btn-sm">Executer</button>
    </div>
    <div id="crud-tests-results" class="tests-results">
        <p class="test-pending">Cliquez sur "Executer" pour lancer les tests</p>
    </div>
</div>

<!-- Tests API -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">3. Tests API REST</h2>
        <button onclick="runApiTests()" class="btn btn-secondary btn-sm">Executer</button>
    </div>
    <div id="api-tests-results" class="tests-results">
        <p class="test-pending">Cliquez sur "Executer" pour lancer les tests</p>
    </div>
</div>

<!-- Interface de test API interactive -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">4. Console API Interactive</h2>
    </div>
    
    <div class="api-console">
        <div class="api-form">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Methode HTTP</label>
                    <select id="api-method" class="form-control">
                        <option value="GET">GET</option>
                        <option value="POST">POST</option>
                        <option value="PUT">PUT</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 2;">
                    <label class="form-label">Endpoint</label>
                    <select id="api-endpoint" class="form-control">
                        <option value="api/livres.php">api/livres.php</option>
                        <option value="api/livres.php?id=1">api/livres.php?id=1</option>
                        <option value="api/utilisateurs.php">api/utilisateurs.php</option>
                        <option value="api/utilisateurs.php?id=1">api/utilisateurs.php?id=1</option>
                        <option value="api/emprunts.php">api/emprunts.php</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Corps de la requete (JSON)</label>
                <textarea id="api-body" class="form-control" rows="4" placeholder='{
    "titre": "Test Livre",
    "auteur": "Test Auteur"
}'></textarea>
            </div>
            
            <button onclick="executeApiCall()" class="btn btn-primary">Envoyer la requete</button>
        </div>
        
        <div class="api-response">
            <h4>Reponse:</h4>
            <div class="response-info">
                <span id="response-status">-</span>
                <span id="response-time">-</span>
            </div>
            <pre id="api-response-body">Aucune requete envoyee</pre>
        </div>
    </div>
</div>

<!-- Documentation des tests -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">5. Rapport de Tests pour le BTS</h2>
    </div>
    
    <div class="doc-block">
        <h3>5.1 Strategie de tests</h3>
        <table>
            <thead>
                <tr>
                    <th>Type de test</th>
                    <th>Description</th>
                    <th>Outils</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Tests unitaires</td>
                    <td>Verification des composants individuels (connexion BDD, requetes)</td>
                    <td>PHP natif, assertions</td>
                </tr>
                <tr>
                    <td>Tests fonctionnels</td>
                    <td>Verification des fonctionnalites CRUD completes</td>
                    <td>Scenarios de test manuels</td>
                </tr>
                <tr>
                    <td>Tests d'integration</td>
                    <td>Verification des echanges API REST</td>
                    <td>Fetch API, Postman</td>
                </tr>
                <tr>
                    <td>Tests de validation</td>
                    <td>Verification des regles metier et contraintes</td>
                    <td>Tests de formulaires</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="doc-block">
        <h3>5.2 Cas de test CRUD Livres</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cas de test</th>
                    <th>Donnees d'entree</th>
                    <th>Resultat attendu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>T01</td>
                    <td>Ajouter un livre valide</td>
                    <td>titre="Test", auteur="Auteur"</td>
                    <td>Livre cree, ID retourne</td>
                </tr>
                <tr>
                    <td>T02</td>
                    <td>Ajouter un livre sans titre</td>
                    <td>titre="", auteur="Auteur"</td>
                    <td>Erreur validation</td>
                </tr>
                <tr>
                    <td>T03</td>
                    <td>Modifier un livre existant</td>
                    <td>id=1, titre="Nouveau"</td>
                    <td>Livre modifie</td>
                </tr>
                <tr>
                    <td>T04</td>
                    <td>Supprimer un livre non emprunte</td>
                    <td>id=5</td>
                    <td>Livre supprime</td>
                </tr>
                <tr>
                    <td>T05</td>
                    <td>Supprimer un livre emprunte</td>
                    <td>id=1 (emprunte)</td>
                    <td>Erreur: livre en cours d'emprunt</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="doc-block">
        <h3>5.3 Cas de test API REST</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Methode</th>
                    <th>Endpoint</th>
                    <th>Code HTTP attendu</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>A01</td>
                    <td>GET</td>
                    <td>/api/livres.php</td>
                    <td>200 OK</td>
                </tr>
                <tr>
                    <td>A02</td>
                    <td>GET</td>
                    <td>/api/livres.php?id=999</td>
                    <td>404 Not Found</td>
                </tr>
                <tr>
                    <td>A03</td>
                    <td>POST</td>
                    <td>/api/livres.php (JSON valide)</td>
                    <td>201 Created</td>
                </tr>
                <tr>
                    <td>A04</td>
                    <td>PUT</td>
                    <td>/api/livres.php?id=1</td>
                    <td>200 OK</td>
                </tr>
                <tr>
                    <td>A05</td>
                    <td>DELETE</td>
                    <td>/api/livres.php?id=1</td>
                    <td>200 OK</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.tests-summary {
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    color: white;
}

.tests-summary .card-title {
    color: white;
}

.tests-stats {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.test-stat {
    text-align: center;
    padding: 1rem 2rem;
    background: rgba(255,255,255,0.1);
    border-radius: 0.5rem;
}

.test-stat-value {
    display: block;
    font-size: 2rem;
    font-weight: 700;
}

.test-stat-label {
    font-size: 0.875rem;
    opacity: 0.9;
}

.test-stat-success {
    background: rgba(34, 197, 94, 0.2);
}

.test-stat-error {
    background: rgba(239, 68, 68, 0.2);
}

.tests-results {
    padding: 1rem 0;
}

.test-pending {
    color: #64748b;
    font-style: italic;
}

.test-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    border-radius: 0.375rem;
    margin-bottom: 0.5rem;
}

.test-item.success {
    background: #dcfce7;
    color: #166534;
}

.test-item.error {
    background: #fee2e2;
    color: #991b1b;
}

.test-item.pending {
    background: #fef3c7;
    color: #92400e;
}

.test-icon {
    font-weight: bold;
    font-size: 1.25rem;
}

.test-name {
    flex: 1;
    font-weight: 500;
}

.test-time {
    font-size: 0.75rem;
    opacity: 0.7;
}

.api-console {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.api-form {
    padding-right: 1.5rem;
    border-right: 1px solid #e2e8f0;
}

.api-response h4 {
    margin-bottom: 0.5rem;
    color: #475569;
}

.response-info {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}

.response-info span {
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

#response-status {
    background: #e2e8f0;
    color: #475569;
}

#response-status.success {
    background: #dcfce7;
    color: #166534;
}

#response-status.error {
    background: #fee2e2;
    color: #991b1b;
}

#response-time {
    background: #dbeafe;
    color: #1e40af;
}

#api-response-body {
    background: #1e293b;
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 0.5rem;
    font-family: monospace;
    font-size: 0.8rem;
    overflow-x: auto;
    max-height: 300px;
    white-space: pre-wrap;
}

.doc-block {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}

.doc-block:last-child {
    border-bottom: none;
}

.doc-block h3 {
    color: #1e40af;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

@media (max-width: 768px) {
    .tests-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .api-console {
        grid-template-columns: 1fr;
    }
    
    .api-form {
        padding-right: 0;
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1.5rem;
    }
}
</style>

<script>
let totalTests = 0;
let passedTests = 0;
let failedTests = 0;

function updateStats() {
    document.getElementById('total-tests').textContent = totalTests;
    document.getElementById('passed-tests').textContent = passedTests;
    document.getElementById('failed-tests').textContent = failedTests;
}

function addTestResult(containerId, name, success, time) {
    const container = document.getElementById(containerId);
    if (container.querySelector('.test-pending')) {
        container.innerHTML = '';
    }
    
    const div = document.createElement('div');
    div.className = `test-item ${success ? 'success' : 'error'}`;
    div.innerHTML = `
        <span class="test-icon">${success ? '✓' : '✗'}</span>
        <span class="test-name">${name}</span>
        <span class="test-time">${time}ms</span>
    `;
    container.appendChild(div);
    
    totalTests++;
    if (success) passedTests++;
    else failedTests++;
    updateStats();
}

async function runDatabaseTests() {
    const container = document.getElementById('db-tests-results');
    container.innerHTML = '<p class="test-pending">Execution en cours...</p>';
    
    // Test 1: Connexion a la base
    const start1 = performance.now();
    try {
        const response = await fetch('api/livres.php');
        const data = await response.json();
        addTestResult('db-tests-results', 'Connexion a la base de donnees', data.success !== false, Math.round(performance.now() - start1));
    } catch (e) {
        addTestResult('db-tests-results', 'Connexion a la base de donnees', false, Math.round(performance.now() - start1));
    }
    
    // Test 2: Table livres existe
    const start2 = performance.now();
    try {
        const response = await fetch('api/livres.php');
        const data = await response.json();
        addTestResult('db-tests-results', 'Table "livres" existe et accessible', Array.isArray(data.data), Math.round(performance.now() - start2));
    } catch (e) {
        addTestResult('db-tests-results', 'Table "livres" existe et accessible', false, Math.round(performance.now() - start2));
    }
    
    // Test 3: Table utilisateurs existe
    const start3 = performance.now();
    try {
        const response = await fetch('api/utilisateurs.php');
        const data = await response.json();
        addTestResult('db-tests-results', 'Table "utilisateurs" existe et accessible', Array.isArray(data.data), Math.round(performance.now() - start3));
    } catch (e) {
        addTestResult('db-tests-results', 'Table "utilisateurs" existe et accessible', false, Math.round(performance.now() - start3));
    }
    
    // Test 4: Table emprunts existe
    const start4 = performance.now();
    try {
        const response = await fetch('api/emprunts.php');
        const data = await response.json();
        addTestResult('db-tests-results', 'Table "emprunts" existe et accessible', data.success !== false, Math.round(performance.now() - start4));
    } catch (e) {
        addTestResult('db-tests-results', 'Table "emprunts" existe et accessible', false, Math.round(performance.now() - start4));
    }
}

async function runCrudTests() {
    const container = document.getElementById('crud-tests-results');
    container.innerHTML = '<p class="test-pending">Execution en cours...</p>';
    
    // Test CREATE
    const start1 = performance.now();
    let createdId = null;
    try {
        const response = await fetch('api/livres.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                titre: 'Test Automatique ' + Date.now(),
                auteur: 'Robot Test',
                genre: 'Test',
                annee_publication: 2024
            })
        });
        const data = await response.json();
        createdId = data.id;
        addTestResult('crud-tests-results', 'CREATE - Ajouter un livre', data.success && data.id, Math.round(performance.now() - start1));
    } catch (e) {
        addTestResult('crud-tests-results', 'CREATE - Ajouter un livre', false, Math.round(performance.now() - start1));
    }
    
    // Test READ
    const start2 = performance.now();
    try {
        const response = await fetch('api/livres.php');
        const data = await response.json();
        addTestResult('crud-tests-results', 'READ - Lister tous les livres', data.success && Array.isArray(data.data), Math.round(performance.now() - start2));
    } catch (e) {
        addTestResult('crud-tests-results', 'READ - Lister tous les livres', false, Math.round(performance.now() - start2));
    }
    
    // Test READ by ID
    if (createdId) {
        const start3 = performance.now();
        try {
            const response = await fetch(`api/livres.php?id=${createdId}`);
            const data = await response.json();
            addTestResult('crud-tests-results', 'READ - Recuperer un livre par ID', data.success && data.data, Math.round(performance.now() - start3));
        } catch (e) {
            addTestResult('crud-tests-results', 'READ - Recuperer un livre par ID', false, Math.round(performance.now() - start3));
        }
    }
    
    // Test UPDATE
    if (createdId) {
        const start4 = performance.now();
        try {
            const response = await fetch(`api/livres.php?id=${createdId}`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    titre: 'Test Modifie ' + Date.now(),
                    auteur: 'Robot Test Modifie'
                })
            });
            const data = await response.json();
            addTestResult('crud-tests-results', 'UPDATE - Modifier un livre', data.success, Math.round(performance.now() - start4));
        } catch (e) {
            addTestResult('crud-tests-results', 'UPDATE - Modifier un livre', false, Math.round(performance.now() - start4));
        }
    }
    
    // Test DELETE
    if (createdId) {
        const start5 = performance.now();
        try {
            const response = await fetch(`api/livres.php?id=${createdId}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            addTestResult('crud-tests-results', 'DELETE - Supprimer un livre', data.success, Math.round(performance.now() - start5));
        } catch (e) {
            addTestResult('crud-tests-results', 'DELETE - Supprimer un livre', false, Math.round(performance.now() - start5));
        }
    }
}

async function runApiTests() {
    const container = document.getElementById('api-tests-results');
    container.innerHTML = '<p class="test-pending">Execution en cours...</p>';
    
    // Test GET /api/livres.php
    const start1 = performance.now();
    try {
        const response = await fetch('api/livres.php');
        addTestResult('api-tests-results', 'GET /api/livres.php - Status 200', response.ok, Math.round(performance.now() - start1));
    } catch (e) {
        addTestResult('api-tests-results', 'GET /api/livres.php - Status 200', false, Math.round(performance.now() - start1));
    }
    
    // Test GET /api/utilisateurs.php
    const start2 = performance.now();
    try {
        const response = await fetch('api/utilisateurs.php');
        addTestResult('api-tests-results', 'GET /api/utilisateurs.php - Status 200', response.ok, Math.round(performance.now() - start2));
    } catch (e) {
        addTestResult('api-tests-results', 'GET /api/utilisateurs.php - Status 200', false, Math.round(performance.now() - start2));
    }
    
    // Test GET /api/emprunts.php
    const start3 = performance.now();
    try {
        const response = await fetch('api/emprunts.php');
        addTestResult('api-tests-results', 'GET /api/emprunts.php - Status 200', response.ok, Math.round(performance.now() - start3));
    } catch (e) {
        addTestResult('api-tests-results', 'GET /api/emprunts.php - Status 200', false, Math.round(performance.now() - start3));
    }
    
    // Test JSON format
    const start4 = performance.now();
    try {
        const response = await fetch('api/livres.php');
        const data = await response.json();
        addTestResult('api-tests-results', 'Response format JSON valide', typeof data === 'object', Math.round(performance.now() - start4));
    } catch (e) {
        addTestResult('api-tests-results', 'Response format JSON valide', false, Math.round(performance.now() - start4));
    }
    
    // Test structure response
    const start5 = performance.now();
    try {
        const response = await fetch('api/livres.php');
        const data = await response.json();
        addTestResult('api-tests-results', 'Structure reponse (success, data)', 'success' in data && 'data' in data, Math.round(performance.now() - start5));
    } catch (e) {
        addTestResult('api-tests-results', 'Structure reponse (success, data)', false, Math.round(performance.now() - start5));
    }
}

function runAllTests() {
    totalTests = 0;
    passedTests = 0;
    failedTests = 0;
    updateStats();
    
    runDatabaseTests();
    setTimeout(runCrudTests, 500);
    setTimeout(runApiTests, 1000);
}

async function executeApiCall() {
    const method = document.getElementById('api-method').value;
    const endpoint = document.getElementById('api-endpoint').value;
    const body = document.getElementById('api-body').value;
    
    const statusEl = document.getElementById('response-status');
    const timeEl = document.getElementById('response-time');
    const bodyEl = document.getElementById('api-response-body');
    
    statusEl.textContent = 'Chargement...';
    statusEl.className = '';
    
    const startTime = performance.now();
    
    try {
        const options = {
            method: method,
            headers: {'Content-Type': 'application/json'}
        };
        
        if ((method === 'POST' || method === 'PUT') && body.trim()) {
            options.body = body;
        }
        
        const response = await fetch(endpoint, options);
        const data = await response.json();
        
        const endTime = performance.now();
        
        statusEl.textContent = `${response.status} ${response.statusText}`;
        statusEl.className = response.ok ? 'success' : 'error';
        timeEl.textContent = `${Math.round(endTime - startTime)}ms`;
        bodyEl.textContent = JSON.stringify(data, null, 2);
        
    } catch (error) {
        const endTime = performance.now();
        statusEl.textContent = 'Erreur';
        statusEl.className = 'error';
        timeEl.textContent = `${Math.round(endTime - startTime)}ms`;
        bodyEl.textContent = `Erreur: ${error.message}`;
    }
}
</script>

<?php include 'includes/footer.php'; ?>
