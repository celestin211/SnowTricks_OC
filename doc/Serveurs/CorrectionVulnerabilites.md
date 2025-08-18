# Correction des vulnérabilités remontées par Cyberwatch

## Demande d'accès à Cyberwatch

Il faut demander un accès à l'outil `Cyberwatch` accessible depuis le portail, ou depuis l'URL
https://cyberwatch.cisirh.rie.gouv.fr/signin

Votre compte doit avoir accès aux groupes suivants :

* 1_SICARDI_PROD
* 1_SICDNEO_PROD
* SICARDI
* SICD
* VINCI

**Contact** : Matthieu Devalle

## Liste des serveurs

La liste des serveurs est accessible via l'entrée `Inventaire` dans le menu.
En cliquant sur chaque serveur, on a un détail des vulnérabilités détectées et des paquets concernés.

### SICARDI

* DEV : `cardjasl01`
* INT : `carijasl01`
* FOR : `asofdasl01` (partagé avec ESTEVE et SICDNEO)
* REC : `carrjasl01`
* PROD : `carpdasl01`

### SICDNEO

* DEV : `vindvasl01`
* INT : `vinitasl01`
* FOR : `asofdasl01` (partagé avec ESTEVE et SICARDI)
* REC : `vinrcasl01`
* PROD : `vinpdasl01`

## Correction sur les serveurs DEV et INT

Se connecter en ssh sur le serveur avec son compte nominatif.
Passer en root :

```text
sudo su -
```

Pour une mise à jour de sécurité automatique de tous les paquets et un redémarrage du serveur dans la foulée, lancer les commandes :

```text
yum --security update
reboot
```

Pour une mise à jour paquet par paquet :

```text
yum update NOM_DU_PAQUET
```

Sur les serveurs de DEV, la mise à jour automatique a été ajoutée dans la `crontab` :

```text
30 0 * * * yum --security update -y >/dev/null 2>&1
```


## Correction sur les serveurs FOR et REC

Il faut créer un ticket `iTop` pour demander la mise à jour sur chacun des serveurs.

## Correction sur les serveurs de PROD

Il faut faire une demande de changement au CAB en se basant sur le ticket iTop d'un autre environnement.

**Contact** : Idir Fergani