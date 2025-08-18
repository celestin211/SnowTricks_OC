# Le déploiement sur DEV

Le serveur de DEV permet un déploiement rapide sur un serveur dont l'équipe de développement a le contrôle complet.

Le déploiement sur DEV ne nécessite pas de création de paquet et par conséquent il ne doit pas être utilisé comme environnement de recette.

## La connexion au serveur DEV

* Se connecter avec son compte nominatif ;
* Se connecter en `httadmin` :

```shell script
sudo su - httadmin
```

## Le déploiement

Lancer la commande :

```shell script
./deploiement.sh
```

Il est possible de définir une autre branche que `develop` par defaut ainsi :

```shell script
./deploiement.sh ma-branche
```

## Le script

La variable `BRANCHE` permet de définir la branche GIT à utiliser par défaut.

Afin de gagner du temps - et comme les déploiements ne mettent pas à jour le dossier du projet mais en créent un nouveau – le dossier `node_modules`, avant d'être mis à jour, est créé à partir d'une archive `node_modules.tar`.
Il est possible de recréer l'archive lors d'un déploiement en décommentant temporairement la ligne :

```shell script
# rm ../node_modules.tar && tar -czf ../node_modules.tar node_modules
```
