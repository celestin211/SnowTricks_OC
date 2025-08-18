# La création du paquet de livraison

## La génération du paquet

Le paquet est créé depuis le serveur DEV :

* Se connecter au [serveur DEV](./Serveurs.md#serveur-dev) en tant que `pacadmin` ;
* Lancer la création du parquet via le script `livraison.sh` :

```shell script
./livraison/livraison.sh X.Y.Z ma-branche
```

Le premier paramètre est la version du paquet et le second qui est optionnel est la branche à utiliser (par défaut `develop` sera utilisée).

Par exemple :

```shell script
./livraison/livraison.sh 1.2.1 preprod
```

Le script `/home/pacadmin/livraison/livraison.sh` va générer le paquet via le dépôt GIT et le transmettre à Nexus.

Note : Si un problème intervient durant le `yarn install`, essayer en le remplaçant par `npm install` dans le script de création de paquet.

Une étiquette Git avec le numéro de version sera automatiquement créé.

Dès le processus terminé, le paquet est immédiatement disponible dans Nexus. Pour le visualiser :

* Se connecter à Nexus avec le compte `sicardi` ;
* Cliquer sur `Browse` ;
* Cliquer sur `sicardi-raw-deploy` ;
* Déplier `SICARDI`.

Les paquets dans `sicardi-raw-deploy` ne peuvent être utilisés que pour la recette sur les environnements INT.

Un paquet est automatiquement supprimé 15 jours après sa création.
