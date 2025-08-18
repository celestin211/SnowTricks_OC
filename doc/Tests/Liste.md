# Liste des tests fonctionnels

Cette page liste l'ensemble des tests fonctionnels de Sicardi.

## Quand sont-ils lancés ?

Ces tests sont lancés :

* dans leur intégralité avant chaque déploiement d'une nouvelle version ;
* partiellement après un développement particulier et avant la création d'une demande de fusion.

Si un développement demande la vérification par un test, la demande GitLab doit être étiquetée avec le type de test correspondant puis mis en "validé" avec l'étiquette correspondante une fois les tests réussis.

Par exemple, un développement sur l'ajout d'un nouveau référentiel possèderait l'étiquette :

![A faire](etiquette_a_faire.png)

Et une fois le développement terminé et les tests passés avec succès :

![Fait](etiquette_fait.png)

## La conformité des tests

Si une erreur est remontée, elle doit être corrigée avant de poursuivre par :

* une correction du code ;
* ou une adaptation du test si la modification réalisée l'implique.

## Les tests unitaires en place

### 👔 Agent

* Vérification de sa fiche
  * Vérifie que le dossier est non vérifié si Affectations non vérifiées par l'agent
  * Vérifie que le dossier est vérifié si Identification + Situation administrative + Affectations vérifiées par l'agent
  * Vérifie si le dossier est archivé.
  * Vérifie si le dossier est actif.
  * Vérifie si le dossier est anonymisé.
* Sexe
  * Vérifie que l'agent est un homme si Civilité = Monsieur
  * Vérifie que l'agent est un femme si Civilité = Madame
* Date de naissance
  * Vérifie l'année de naissance
* Affectations
  * Vérifie une affectation actuelle
  * Vérifie une affectation ancienne
  * Vérifie une affectation future
* Projet professionnel
  * Vérifie si l'agent est ouvert à la mobilité
  * Vérifie la contrainte d'unicité sur la description du projet et la date de début

### 💼 Métier

* Vérifie qu'un métier RMFP ne soit pas directionnel
* Vérifie qu'un métier directionnel le soit

### Appel à contribution
* Vérifie l'ajout des compétences savoirs dans l'appel à contribution
* Vérifie la suppression des compétences d'un appel à contribution
* Vérifie le calcul de la durée d'intervention

### Service Date
* Vérifie la différence entre deux date
* Vérifie que la différence entre deux dates est inférieure à un mois.
* Vérifie le comportement de la fonction qui permet de récupérer l'année d'une date
* Vérifie le comportement de la fonction qui permet de récupérer le mois d'une date
* Vérifie l'égalité entre deux dates
* Vérifie le comportement de la fonction qui permet de récupérer la liste des jours, mois ou années entre deux dates

### Service Util
* Vérifie le comportement de la fonction qui permet de transformer une chaîne vers un titre
* Vérifie le comportement de la fonction qui permet de retourner l'identité d'un agent ou un utilisateur

### Service Signature de charte d'utilisation
* Vérifier que l'utilisateur doit signer la charte d'utilisation ou non

### Service Informations sur l'utilisateur
* Vérifier que le compte de l'utilisateur est actif dans une ressource globale
* Vérifier que le compte de l'utilisateur est visible dans l'annuaire des utilisateurs

### Service calcul âge
* Vérifier le calcul de l'âge d'un agent par rapport aujourd'hui ou une autre date

### Service Validation des domaines électroniques 
* Vérifier qu'un domaine électronique est autorisé dans une ressource globale

### Service Profil utilisateur choisi
* Vérifie le comportement de la fonction qui permet de retourner le profil de l'utilisateur
* Vérifie le comportement de la fonction qui permet de retourner le libellé du profil de l'utilisateur
* Vérifie que l'utilisateur connecté est un admin ou super admin
* Vérifie que l'utilisateur connecté est un agent
* Vérifie que l'utilisateur connecté est un admin
* Vérifie que l'accés des agents est désactivé dans une ressource globale
* Vérifie que l'accés des utilisateurs est désactivé dans une ressource globale
* Vérifier les fonctionnalités autorisées pour un profil
* Vérifie le comportement de la fonction qui permet de retourner l'utilisateur
* Vérifie le comportement de la fonction qui permet de retourner la ressource globale
* Vérifier que le module appel à contribution est activé dans une ressource globale
* Vérifier que le module métier est activé dans une ressource globale
* Vérifier que la compétence savoir est activée dans une ressource globale
* Vérifier que la compétence savoir faire est activée dans une ressource globale
* Vérifier que la compétence savoir être est activée dans une ressource globale
* Vérifier que la compétence managériale est activée dans une ressource globale

## Les tests fonctionnels en place

### 💻 Connexion

#### Connexion utilisateur

* Échec de l'authentication avec un accès inexistant
* Connexion avec succès avec un profil super-administrateur
* Ajout et affichage d'une trace de connexion

#### Choix ressource globale / profil

* Connexion avec succès à une ressource globale autorisée
* Échec connexion gestionnaire à une ressource globale non autorisée
* Échec connexion à une ressource globale sans spécifier de profil (seulement autorisé par le super-administrateur)

### 👥‍ Utilisateurs

#### Création d'un utilisateur

* Informations générales :
  * Affichage avec succès
  * Vérification des champs obligatoires
  * Création avec succès
* Habilitations :
  * Affichage avec succès
  * Ajout de périmètres avec succès

### Ressources globales

#### Affichage d'une ressource globale

* Section courriel
  * Affichage du champ "Mail From" uniquement pour les super-admin
* Section Agents
  * Affichage du champ "Utiliser vivier dans le CV si non cadre dirigeant" uniquement pour les super-admin

#### Edition d'une ressource globale
* Section courriel
  * Affichage du champ "Mail From" uniquement pour les super-admin
* Section Agents
  * Affichage du champ "Utiliser vivier dans le CV si non cadre dirigeant" uniquement pour les super-admin
  
### 👔 Agents

#### Création d'un agent

* Identification :
  * Affichage avec succès
  * Vérification des champs obligatoires
  * Création avec succès
* Situation administrative :
  * Affichage avec succès
  * Vérification des champs obligatoires
  * Création avec succès
* Formations et diplômes :
  * Affichage avec succès
  * Création avec succès
* Affectations :
  * Affichage avec succès
  * Vérification des types d'affectation
  * Vérification des champs obligatoires d'une affectation du secteur public

#### Affichage agent

* Vue du CV PDF
* Vue du CV Word
* Affichage de la synthèse avec succès
* Correspondances
  * Métiers correspondants
    * Affichage avec succès
    * Présence du métier correspondant
  * Postes correspondants
    * Affichage avec succès
    * Présence du poste correspondant
  * Appels à contribution correspondants
    * Affichage avec succès
    * Présence de l'appel à contribution correspondant

### ⌨️ Métiers

#### Création d'un métier

* Identification :
  * Affichage avec succès
  * Vérification des champs obligatoires
  * Vérification des métiers en doublon
  * Création avec succès
* Compétences requises :
  * Affichage avec succès
  * Création avec succès
* Autres informations :
  * Affichage avec succès
  * Création avec succès

### 💼 Postes

#### Création d'un poste

* Identification :
  * Affichage avec succès
  * Vérification des champs obligatoires
  * Vérification des règles de saisie des dates
  * Vérification des postes en doublon
  * Création avec succès
* Compétences :
  * Affichage avec succès

### 🎓 Formations

#### Liste des formations

* Tableau
  * Affichage avec succès.
  * Présence de la formation avec session dans la liste.
* Filtres
  * Fonctionnement de l'affichage des sessions fermées.

#### Création d'une formation

* Identification :
  * Affichage avec succès
  * Vérification des champs obligatoires
  * Vérification des règles de saisie des dates
  * Création avec succès
* Compétences :
  * Affichage avec succès
  * Validation d'ajout de compétences
* Autres informations :
  * Affichage avec succès
  * Saisie des observations avec succès
* Sessions :
  * Affichage avec succès
  * Création sans session avec succès
  * Création avec session avec succès

### 🌐 Référentiels

* Affichage de la liste avec succès
* Vérification du nombre de référentiels

* Pour chaque référentiel (un peu plus d'une cinquantaine) :
  * Vérification de sa présence dans la liste
  * Affichage de la fiche avec succès
  * Affichage de la page de création avec succès
  * Création avec succès et présence dans la liste
  * Affichage de la page de modification avec succès
  * Modification avec succès et présence dans la liste
  * Affichage de la fiche via depuis la liste avec succès
  * Vérification de la suppression
  * Vérification de la suppression en masse.
  * Vérification de sa présence dans l'import
  
### 📒 Requêtes préprogrammées

#### Cartographie des postes

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

#### Cartographie d'une population de cadres

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

#### Cartographie des compétences

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

#### Analyse d'écart des compétences

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

#### Cartographie des formations

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

#### Nominations et primo-nominations ministérielles

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

#### Suivi des nominations issues du vivier

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés
  
#### Cartographie des métiers

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
  * Vérification de la correspondance d'un total avec le nombre de postes trouvés

### 📊 Statistiques

#### Statistiques par Poste

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique

#### Statistiques Nominations et primo-nominations ministérielles

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique

#### Statistiques par situation administrative

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique

#### Statistiques stocks agents

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique

#### Statistiques des compétences par poste

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique

#### Statistiques compétences par agent

* Affichage de la page avec succès
* Lancement de la recherche avec succès
* Vérification du nombre de tableaux

Pour chaque catégorie de résultat :
  * Vérification de la présence du tableau
  * Vérification de la présence du graphique
