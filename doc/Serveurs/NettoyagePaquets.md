# Le nettoyage des paquets

Il convient de faire régulièrement le ménage après les livraisons.
Sur Venezio, un dossier `incoming` reçoit les paquets que l'on peut référencer (donc les paquets que l'on a créés depuis DEV).
Une fois référencés sur Venezio, ces paquets deviennent donc inutiles et peuvent être supprimés.

S'ils ne sont pas supprimés régulièrement, ils risquent de saturer le serveur et la liste des versions disponibles lorsque l'on référence un nouveau paquet s'allongera (et cette liste n'est triée ni par ordre alphabétique ni par date de création) et il deviendra alors plus compliqué de rechercher un paquet.

Pour supprimer ces paquets, il faut se connecter par FTP au serveur Venezio :

* Se connecter au [serveur DEV](./Serveurs.md#serveur-dev) en tant que `pacadmin` ;
* Se connecter au serveur FTP Venezio ;
```shell script
sftp pacadmin@venezio.onp:/data/postgresql/venezio/gforge/chroot/home/groups/sicardi/incoming
```
* Supprimer le ou les fichiers désirés ;
```shell script
ls -l
rm nom_du_fichier
```
Les fichiers semblent ne pouvoir être supprimés qu'un par un.
* Se déconnecter.
```shell script
exit
```

Une fois référencés sur Venezio, les paquets sont dupliqués sur des serveurs de dépôts, il n'y a donc aucun risque à les supprimer.
