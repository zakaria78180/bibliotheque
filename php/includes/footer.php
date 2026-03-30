    </div>
    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h4>Application</h4>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="livres.php">Livres</a></li>
                    <li><a href="utilisateurs.php">Utilisateurs</a></li>
                    <li><a href="emprunts.php">Emprunts</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Documentation BTS SIO</h4>
                <ul>
                    <li><a href="tests.php">Tests</a></li>
                    <li><a href="rgpd.php">Conformite RGPD</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Technologies</h4>
                <ul>
                    <li>PHP 8 + PDO</li>
                    <li>SQLite</li>
                    <li>API REST / JSON</li>
                    <li>HTML5 / CSS3</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Gestion de Bibliotheque - Projet BTS SIO SLAM</p>
            <p class="footer-credits">Competences : Conception et developpement d'applications | Gestion des donnees</p>
        </div>
    </footer>
    
    <style>
    .site-footer {
        background: #1e293b;
        color: #94a3b8;
        margin-top: 3rem;
    }
    
    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        padding: 2rem;
    }
    
    .footer-section h4 {
        color: white;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .footer-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-section li {
        margin-bottom: 0.5rem;
    }
    
    .footer-section a {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.2s;
    }
    
    .footer-section a:hover {
        color: #60a5fa;
    }
    
    .footer-bottom {
        border-top: 1px solid #334155;
        padding: 1.5rem 2rem;
        text-align: center;
    }
    
    .footer-bottom p {
        margin: 0;
        font-size: 0.875rem;
    }
    
    .footer-credits {
        margin-top: 0.5rem !important;
        font-size: 0.75rem !important;
        color: #64748b;
    }
    </style>
</body>
</html>
