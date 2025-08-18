# Introduction aux serveurs et environnements

## Les environnements

### L'environnement de développement

* DEV

Il s'agit de l'environnement de test de l'équipe de développement.

### Les environnements de recette interne

* INT1
* INT2
* INT3
* REC1

Les environnements `INT` peuvent également être utilisés par l'équipe de développement pour des tests.

L'environnement `REC` est en théorie iso-fonctionnel à la `PROD`.


### Les environnements de recette métier et de formation

* FOR1
* FOR2


### L'environnement de production

* PRD1


## Les serveurs

### Informations générales et communes

* Version PHP : 7.3.11
* Version Oracle : 19
* Nom DNS du serveur Oracle : exax7-scan

Putty est recommandé pour accéder aux serveurs.


### Les accès

| Nom | Hôte                     |
|-----|--------------------------|
| DEV | cardjasl01-a.dev.cisirh  |
| INT | carijasl01-a.int.cisirh  |
| REC | carrjasl01-a.rec.cisirh  |
| FOR | asofdasl01-a.form.cisirh |
| PRD | carpdasl01-a.prod.cisirh |

L'équipe de développement n'a accès qu'aux serveurs `DEV` et `INT`.

L'accès se fait depuis le serveur de rebond ou depuis son poste au CISIRH.


### Les utilisateurs

La connexion se fait avec son compte nominatif (ex. : `rleclerc-ext`).
Ce compte n'est utilisé que pour la connexion.

Les utilisateurs sont :

* **httadmin** : Compte `httpd`, utilisé pour :
  * lire le code,
  * modifier le code,
  * redémarrer le serveur ;
* **pacadmin** : Utilisé pour la génération du paquet de livraison depuis `DEV` ;
* **dplexploit** : Utilisé pour accéder aux scripts ou aux registres d'erreur (hors applicatif).
* **root** : Tout-Puissant

Une fois connecté avec son compte nominatif, se connecter via une de ces commandes :

```shell script
sudo su - httadmin
sudo su - pacadmin
sudo su - dplexploit
sudo su -
```
