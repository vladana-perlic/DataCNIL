# DataCNIL
développée pour répondre aux besoins des experts en protection des données en matière de recherche et de filtrage des délibérations de la CNIL.

La plateforme est hébergée sur <a href="http://i3l.univ-grenoble-alpes.fr/~perlicv/">le serveur i3l.</a>
Le design de la plateforme est choisi par le client (DPO Adjointe @ Inria).

Le rapport du projet est <a href="/Rapport%20du%20projet.pdf">ici.</a>

## Documentation succincte Data CNIL :

- les fichiers XML de délibérations de la CNIL sont stockés en vrac dans le dossier dataxml
- le script index.html
	- script initial d'accueil
	- vérifie les fichiers de délibérations 
	- vérifie le nombre de délibérations indexées dans la bd
	- affiche la date de mise à jour de la bd
	- lance update_database.php chaque semaine pour mettre à jour la bd
	--> propose de mettre à jour la bd en cliquant sur le bouton "METTRE A JOUR"
	--> propose de lancer le script "parcourir.php" pour intégrer les fichoers xml à la BD
	--> propose de lancer le script "parser.php" pour les décomposer en BD

	- si les champs du formulaire ont été utilisés, propose un affichage histogramme des fichiers correspondant à la requête
	- sinon affiche l'histogramme des fichiers
	
	- histogramme lie au script loaddelibs (pour afficher les délibérations corrspondant à la requête et l'année cliquée)

	- si requête avec mot clé propose lien vers concordancier
	
	- affiche "avertissement.php"  (explications de base) et une délibération au hasard (fonction cnil2html dans fonctions.php)

- le script parcourir :
	- parcourt le dossier dataxml, vérifie chaque fichier pour sa présence en BD et ajoute si nécessaire à nom de fichier DTC_fichiers_open
	
- le script parser :
	- parcours la table DTC_fichier_open
	- ouvre chaque fichier xml
	- récupère chaque noeud (utile) du fichier
	- les intègre dans une table Deliberation : 
		    IDCNIL, 
                    IDFichier, 
                    NatureDocument, 
                    Titre, 
                    TitreLong, 
                    Numero, 
                    NatureDeliberation, 
                    DateTexte,
                    AnneeTexte, 
                    MoisTexte,
                    DatePublication, 
                    AnneePublication,
                    MoisPublication,
                    Contenu, 
                    NomFichier
        pour des raisons de temps de calcul, le fichier se relance (meta refresh) tous les 10 fichiers

- le script loaddelibs
	- utilise les données $post et $session pour affichier les délibérations provenant d'une requête

- le script concord
	- affiche en concordancier les contextes gauche droite (à 75 signes) du terme choisi
	- le numéro de délibération est le lien vers le texte complet de la délib (showcnil.php)

- le script makelexique
	- fichier autonome d'indexation des contenus textuels - non utilisé dans le reste du système


- le script fonctions 
	- contient cnil3html pour afficher un fichier délibération en html (remplacement de balises par PREG)
	- contient endsWith et StartWith
	- contient $motsvides en array

- le script download.py
	- vérifie si les fichiers existent dans file_items.txt. S'ils existent, il ne télécharge pas les fichiers, sinon :
		* télécharge les nouveaux fichiers depuis https://echanges.dila.gouv.fr/OPENDATA/CNIL/
		* extrait les fichiers .tar.gz téléchargés
		* déplace uniquement les fichiers des sous-répertoires du dossier extrait vers ./dataxml/cnil/
	- met à jour file_items.txt avec les nouveaux éléments de fichier
	- nettoie les fichiers téléchargés et le dossier extrait après le processus

- le script update_database.php
	- exécute le script Python download.py pour télécharger les nouveaux fichiers de délibérations de la CNIL
	- affiche la sortie de l'exécution du script Python
	- supprime le dossier 'extracted' ainsi que tous les sous-répertoires à l'intérieur de ./dataxml/cnil/
	- exécute le script parcourir.php pour parcourir les fichiers téléchargés et les intégrer dans la base de données
	- affiche la sortie de l'exécution du script parcourir.php
	- exécute le script parser.php pour analyser les fichiers téléchargés et les intégrer dans la base de données
	- affiche la sortie de l'exécution du script parser.php

- le script wordcloud.php
	- récupère les données à partir d'une base de données MySQL
	- Calcul de la fréquence des mots
	- prépare pour la génération du nuage de mots

- le script classificateur.php
	- Classification des Délibérations
	- Navigation par Onglets
	-Chargement Dynamique des Données

- le script classifiy_helper.php
	- Traitement de la Catégorie et de la Référence
	- formate des données en HTML pour affichage dans l'interface utilisateur

- le script correction.php
	- exécute les requêtes de mise à jour
	- modifie la nature de la délibération dans la BD
