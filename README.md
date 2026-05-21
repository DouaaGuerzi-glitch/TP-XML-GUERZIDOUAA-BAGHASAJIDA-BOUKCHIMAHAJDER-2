# TP-XML-GUERZIDOUAA-BAGHASAJIDA-BOUKCHIMAHAJDER-2
Application web permettant de gérer les concours du Club Info_Tech. Elle permet de consulter les concours, inscrire des membres et afficher les résultats avec calcul des scores.
🏆 Club Info_Tech — TP1 XML/XQuery
👥 Membres du groupe
#Nom & PrénomRôle1Guerzi DouaaPHP, Connexion PHP-BaseX, Vidéo YouTube2Bagha SajidaXML, XSD, XQuery3Boukchima HajderBaseX, GitHub, README

📋 Description
Application web permettant de gérer les concours du Club Info_Tech.
Elle permet de consulter les concours, inscrire des membres et afficher les résultats avec calcul des scores.

🛠️ Technologies utilisées

XML / XSD — Structure et validation des données
XQuery — Requêtes et mises à jour des données
BaseX 12.3 — Base de données XML native
PHP 8.2 — Serveur web et interface utilisateur
HTML / CSS — Interface graphique


📁 Structure du projet
TP1_XML/
├── web/
│   ├── index.php          # Page principale — liste des concours
│   ├── inscription.php    # Formulaire d'inscription à un concours
│   ├── resultats.php      # Affichage des résultats et scores
│   └── style.css          # Mise en page
├── club.xml               # Base de données XML
├── club.xsd               # Schéma de validation XML
├── requies.xq             # Requêtes XQuery (Q1 à Q5)
└── updates.xq             # Mises à jour XQuery

👤 Répartition du travail
Guerzi Douaa — PHP & Connexion PHP-BaseX & Vidéo YouTube
Fichiers :

index.php — Page principale affichant la liste des concours
inscription.php — Formulaire d'inscription d'un membre à un concours
resultats.php — Affichage des résultats et calcul des scores
style.css — Design et mise en page
updates.xq — Requêtes XQuery de mise à jour

Rôle dans le projet :

Développement des pages PHP (index, inscription, résultats)
Connexion de PHP avec BaseX via l'API REST :

php$url = "http://localhost:8984/rest/club";

Envoi des requêtes XQuery depuis PHP avec cURL
Calcul du score de chaque participant :

Score = (Complexité + Temps d'exécution) × Coefficient

Réalisation et mise en ligne de la vidéo de démonstration sur YouTube


Bagha Sajida — XML, XSD & XQuery
Fichiers :

club.xml — Création et structuration de la base de données XML
club.xsd — Schéma de validation des données
requies.xq — Requêtes XQuery (Q1 à Q5)

Rôle dans le projet :

Conception de la structure XML (catégories, membres, concours)
Définition des règles de validation XSD
Écriture des requêtes XQuery :

Q1 : Liste des membres avec leurs catégories
Q2 : Liste des concours ordonnés par date
Q3 : Calcul des scores par concours
Q4 : Trouver le vainqueur de chaque concours
Q5 : Membres d'une catégorie spécifique




Boukchima Hajder — BaseX, GitHub & README
Fichiers :

README.md — Documentation complète du projet

Rôle dans le projet :

Installation et configuration du serveur BaseX :

cmdbasexhttp.bat -h 8984

Création de la base de données XML dans BaseX :

cmdCREATE DB club club.xml

Création du dépôt GitHub et mise en ligne de tous les fichiers du projet
Rédaction de la documentation README


⚙️ Installation et lancement
1. Démarrer BaseX HTTP Server
cmdcd "C:\Program Files (x86)\BaseX"
basexhttp.bat -h 8984
2. Créer la base de données
cmdbasex.bat -U admin -P admin "CREATE DB club club.xml"
3. Démarrer le serveur PHP
cmdcd C:\...\TP1_XML\web
php.exe -S localhost:8000
4. Ouvrir l'application
http://localhost:8000

🚀 Fonctionnalités

📋 Liste des concours — Affichage de tous les concours avec catégorie, date et coefficient
📝 Inscription — Ajouter un membre à un concours avec complexité et temps d'exécution
📊 Résultats — Calcul des scores et affichage du vainqueur 🏆


🗄️ Données XML

3 catégories : Intelligence Artificielle, Développement Web, Sécurité Informatique
8 membres
3 concours
