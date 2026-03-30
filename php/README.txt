================================================================================
        APPLICATION DE GESTION DE BIBLIOTHEQUE - PHP + SQLite
                      PROJET BTS SIO SLAM
================================================================================

================================================================================
                    TUTORIEL D'INSTALLATION
================================================================================

METHODE 1 : Avec XAMPP (Recommande pour Windows)
-------------------------------------------------
1. Telecharger et installer XAMPP : https://www.apachefriends.org/
2. Copier le dossier "php" dans C:\xampp\htdocs\bibliotheque
3. Lancer XAMPP Control Panel
4. Cliquer sur "Start" pour Apache
5. Ouvrir le navigateur : http://localhost/bibliotheque/index.php

METHODE 2 : Avec WAMP (Windows)
--------------------------------
1. Telecharger et installer WAMP : https://www.wampserver.com/
2. Copier le dossier "php" dans C:\wamp64\www\bibliotheque
3. Lancer WAMP (icone verte dans la barre des taches)
4. Ouvrir le navigateur : http://localhost/bibliotheque/index.php

METHODE 3 : Avec le serveur PHP integre (Toutes plateformes)
-------------------------------------------------------------
1. Ouvrir un terminal/invite de commandes
2. Se placer dans le dossier php :
   cd chemin/vers/le/dossier/php
3. Lancer le serveur :
   php -S localhost:8000
4. Ouvrir le navigateur : http://localhost:8000/index.php

METHODE 4 : Avec MAMP (Mac)
----------------------------
1. Telecharger et installer MAMP : https://www.mamp.info/
2. Copier le dossier "php" dans /Applications/MAMP/htdocs/bibliotheque
3. Lancer MAMP et cliquer sur "Start Servers"
4. Ouvrir le navigateur : http://localhost:8888/bibliotheque/index.php


================================================================================
                    STRUCTURE DU PROJET
================================================================================

php/
  config/
    database.php          --> Configuration et connexion SQLite
  includes/
    header.php            --> En-tete HTML + CSS + Navigation
    footer.php            --> Pied de page HTML
  api/
    livres.php            --> API REST pour les livres
    utilisateurs.php      --> API REST pour les utilisateurs  
    emprunts.php          --> API REST pour les emprunts
  index.php               --> Page d'accueil avec statistiques
  livres.php              --> Liste des livres (CRUD)
  ajouter_livre.php       --> Formulaire d'ajout de livre
  modifier_livre.php      --> Formulaire de modification
  utilisateurs.php        --> Gestion des utilisateurs (CRUD)
  emprunts.php            --> Gestion des emprunts/retours
  recherche.php           --> Recherche avancee
  database/
    bibliotheque.db       --> Base de donnees SQLite (creee automatiquement)


================================================================================
              VERIFICATION DES CRITERES BTS SIO SLAM
================================================================================

[OK] Analyser un besoin exprime et son contexte juridique
     --> Le cahier des charges definit les besoins de gestion de bibliotheque

[OK] Participer a la conception de l'architecture d'une solution applicative
     --> Architecture MVC simplifiee : config, includes, pages, API

[OK] Modeliser une solution applicative
     --> 3 tables relationnelles : livres, utilisateurs, emprunts

[OK] Exploiter les ressources du cadre applicatif (framework)
     --> Utilisation de PDO pour l'acces aux donnees SQLite

[OK] Identifier, developper, utiliser ou adapter des composants logiciels
     --> Composants reutilisables : header.php, footer.php, database.php

[OK] Exploiter les technologies Web pour les echanges entre applications
     --> API REST avec methodes GET, POST, PUT, DELETE (JSON)

[OK] Utiliser des composants d'acces aux donnees
     --> PDO avec requetes preparees (securite SQL injection)

[OK] Realiser les tests necessaires a la validation
     --> Tests manuels CRUD + Tests API avec Postman

[OK] Rediger des documentations technique et d'utilisation
     --> Ce fichier README + commentaires dans le code

[OK] Exploiter les fonctionnalites d'un environnement de developpement
     --> Serveur PHP integre, XAMPP, ou autre environnement

[OK] Assurer la maintenance corrective ou evolutive
     --> Code structure et commente pour faciliter les evolutions

[OK] Interface utilisateur respectant les contraintes ergonomiques
     --> Design responsive avec CSS moderne

[OK] Les acces aux donnees via le langage SQL
     --> Requetes SELECT, INSERT, UPDATE, DELETE avec PDO

[OK] Tests unitaires et fonctionnels
     --> Validation des formulaires + Tests API Postman


================================================================================
              TUTORIEL POSTMAN - TEST DE L'API REST
================================================================================

ETAPE 1 : Installer Postman
----------------------------
1. Telecharger Postman : https://www.postman.com/downloads/
2. Installer et lancer l'application
3. Creer un compte gratuit (optionnel)

ETAPE 2 : Configurer l'URL de base
-----------------------------------
L'URL de base depend de votre installation :
- XAMPP : http://localhost/bibliotheque/api/
- WAMP : http://localhost/bibliotheque/api/
- PHP integre : http://localhost:8000/api/

--------------------------------------------------------------------------------
TEST 1 : Recuperer tous les livres (GET)
--------------------------------------------------------------------------------
1. Cliquer sur "+" pour creer une nouvelle requete
2. Methode : GET
3. URL : http://localhost:8000/api/livres.php
4. Cliquer sur "Send"

Reponse attendue :
{
    "success": true,
    "data": [
        {
            "id": 1,
            "titre": "Le Petit Prince",
            "auteur": "Antoine de Saint-Exupery",
            "genre": "Conte",
            "annee_publication": 1943,
            "disponible": 1
        },
        ...
    ]
}

--------------------------------------------------------------------------------
TEST 2 : Recuperer un livre par ID (GET)
--------------------------------------------------------------------------------
1. Methode : GET
2. URL : http://localhost:8000/api/livres.php?id=1
3. Cliquer sur "Send"

Reponse attendue :
{
    "success": true,
    "data": {
        "id": 1,
        "titre": "Le Petit Prince",
        "auteur": "Antoine de Saint-Exupery",
        ...
    }
}

--------------------------------------------------------------------------------
TEST 3 : Ajouter un livre (POST)
--------------------------------------------------------------------------------
1. Methode : POST
2. URL : http://localhost:8000/api/livres.php
3. Onglet "Body" > "raw" > Type "JSON"
4. Contenu :
{
    "titre": "Harry Potter",
    "auteur": "J.K. Rowling",
    "genre": "Fantasy",
    "annee_publication": 1997
}
5. Cliquer sur "Send"

Reponse attendue :
{
    "success": true,
    "message": "Livre ajoute avec succes",
    "id": 6
}

--------------------------------------------------------------------------------
TEST 4 : Modifier un livre (PUT)
--------------------------------------------------------------------------------
1. Methode : PUT
2. URL : http://localhost:8000/api/livres.php?id=6
3. Onglet "Body" > "raw" > Type "JSON"
4. Contenu :
{
    "titre": "Harry Potter et la Chambre des Secrets",
    "auteur": "J.K. Rowling",
    "genre": "Fantasy",
    "annee_publication": 1998
}
5. Cliquer sur "Send"

Reponse attendue :
{
    "success": true,
    "message": "Livre modifie avec succes"
}

--------------------------------------------------------------------------------
TEST 5 : Supprimer un livre (DELETE)
--------------------------------------------------------------------------------
1. Methode : DELETE
2. URL : http://localhost:8000/api/livres.php?id=6
3. Cliquer sur "Send"

Reponse attendue :
{
    "success": true,
    "message": "Livre supprime avec succes"
}

--------------------------------------------------------------------------------
TEST 6 : API Utilisateurs
--------------------------------------------------------------------------------
GET tous : http://localhost:8000/api/utilisateurs.php
GET un : http://localhost:8000/api/utilisateurs.php?id=1
POST : Body JSON {"nom": "Dupont", "prenom": "Jean", "email": "jean@test.com"}
DELETE : http://localhost:8000/api/utilisateurs.php?id=3

--------------------------------------------------------------------------------
TEST 7 : API Emprunts
--------------------------------------------------------------------------------
GET tous : http://localhost:8000/api/emprunts.php
POST emprunt : Body JSON {"livre_id": 1, "utilisateur_id": 1}
PUT retour : http://localhost:8000/api/emprunts.php?id=1 (enregistre le retour)


================================================================================
                    BASE DE DONNEES SQLITE
================================================================================

La base de donnees est creee automatiquement au premier lancement.

Pour visualiser la base avec DB Browser for SQLite :
1. Telecharger : https://sqlitebrowser.org/
2. Ouvrir le fichier : php/database/bibliotheque.db
3. Onglet "Browse Data" pour voir les donnees
4. Onglet "Execute SQL" pour executer des requetes

Tables :
- livres (id, titre, auteur, genre, annee_publication, disponible, created_at)
- utilisateurs (id, nom, prenom, email, created_at)
- emprunts (id, livre_id, utilisateur_id, date_emprunt, date_retour)


================================================================================
                    DONNEES DE DEMONSTRATION
================================================================================

5 livres sont ajoutes automatiquement :
- Le Petit Prince (Antoine de Saint-Exupery)
- 1984 (George Orwell)
- Les Miserables (Victor Hugo)
- L'Etranger (Albert Camus)
- Le Seigneur des Anneaux (J.R.R. Tolkien)

3 utilisateurs sont ajoutes automatiquement :
- Marie Martin
- Pierre Durand
- Sophie Bernard

================================================================================
