# Le fonctionnement théorique

Les habilitations prennent en compte deux notions :

* Les fonctionnalités,
* Les périmètres.


## Les fonctionnalités

Une fonctionnalité définit une autorisation sur une action de manière générale.

Par exemple :

* Pouvoir accéder aux dossiers d'agent ;
* Pouvoir supprimer des postes.


## Les périmètres

Un périmètre est une partie d'un ensemble d'entités.

Par exemple :

* Les dossiers des agents rattachés au ministère de la Défense ;
* Les postes du CISIRH.

Chaque périmètre, par défaut, est en lecture/visibilité seule. Il est possible d'ajouter une option sur le périmètre pour avoir un accès en modification.


## Le fonctionnement général

Une habilitation est donc l'association entre une fonctionnalité et des périmètres.

Pour pouvoir réaliser une action, il faut donc vérifier que :

* La fonctionnalité est activée ;
* L'entité modifiée fasse partie des périmètres autorisés et que l'écriture est autorisée si besoin.

Si par exemple, un utilisateur possède la fonctionnalité "Modifier un dossier agent" mais pas "Supprimer un dossier agent" et que son périmètre agent concerne le CISIRH et autorise l'écriture, il ne pourra modifier que les dossiers agents du CISIRH sans pouvoir les supprimer.

