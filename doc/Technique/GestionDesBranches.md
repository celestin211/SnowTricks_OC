# Gestion des branches


## Les branches principales

### prod

Cette branche représente l'image de production.

### preprod

Cette branche représente l'image de REC / FOR1.

### develop

Toutes les nouvelles fonctionnalités seront fusionnées dans cette branche.


## La demande de fusion

Si le ticket comporte l'étiquette `Prod`, la demande de fusion pointe `prod`, sinon `develop`, ou `preprod` si cela concerne un retour sur REC.

Dès que `prod` est mise à jour, elle est automatiquement fusionnée sur `preprod` et `develop` (pipeline).
Dès que `preprod` est mise à jour, elle est automatiquement fusionnée sur `develop`.


## Création du tag

Le tag est généré automatiquement lors de la création du paquet sur DEV.
