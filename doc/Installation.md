# Installation du projet

## Pré-requis

- PHP 7.3
- Oracle 18

## Installation de PHP

Il faut préalablement avoir installé `Redistribuable Visual C++` (demander aux grid si ce n'est pas le cas)

Il est possible de récupérer le zip de PHP 7.3 sur le site officiel, il faut prendre la version Non Thread Safe en x64 (pas la x86). 

* Dézipper PHP à l'endroit désiré (par exemple C:\php73)

* Renommer le fichier `php.ini-development` en `php.ini` et le modifier comme suit :

```
extension_dir = "ext"
date.timezone='Europe/Paris'
error_reporting=E_ALL
expose_php = Off
memory_limit=4024M
max_execution_time = 300
post_max_size=60M
upload_max_filesize=60M
session.use_only_cookies=0
session.cookie_httponly= 1

extension=bz2
extension=curl
extension=fileinfo
extension=gd2
extension=intl
extension=mbstring
extension=oci8_12c
extension=openssl
extension=xsl
```

* Ajouter le dossier php à votre path Windows (Panneau de configuration, Modifier les variables d'environnement pour votre compte)


## Installation du driver Oracle si non présent

Il est possible de récupérer le .dll depuis pecl.php.net/package/oci8 puis choisir la dernière version et télécharger l’archive correspondant à votre version de PHP (7.3 NTS x64) 

Dézippez et déplacer le .dll dans le dossier ext/ de php

Si cela ne fonctionne pas, tester après avoir redémarrer l'ordinateur.


## Installation de APCU

Il faut récupérer le .dll depuis pecl.php.net/package/apcu puis choisir la dernière version et télécharger l'archive correspondant à votre version de PHP (7.3 NTS x64)

Dézippez le .dll dans le dossier ext/ de php

Activer ensuite dans le `php.ini` :

```
[apcu]
extension=apcu
apc.enabled=1
apc.shm_size=32M
apc.ttl=7200
apc.enable_cli=1
apc.serializer=php
```


## Installation OPcache

Le .dll est normalement déjà présent dans le dossier /ext de php.

Il suffit de l'activer dans le `php.ini` :

```
[opcache]
zend_extension=opcache
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable=1
opcache.enable_cli=1
```


## Installation de NodeJS

* Récupérer le binaire Windows de Node version x64 sur https://nodejs.org/en/download/
* Dézipper nodejs à l'endroit désiré (par exemple `C:\nodejs`)
* Ajouter le dossier nodejs à votre path Windows (Panneau de configuration, Modifier les variables d'environnement pour votre compte)
* Lancer la commande :

```shell script
npm config set proxy "http://UTILISATEUR:MOT_DE_PASSE@http.proxyvip.alize:8080"
```


## Génération d'une clé SSH

Utilisation de Git Bash (fourni avec l'installation de Git windows) :

Si pas encore de clé SSH, il faut la générer :

```shell script
ssh-keygen -t rsa
eval `ssh-agent -s`
ssh-add ~/.ssh/id_rsa
```

Il faut ensuite ajouter sa clé SSH sur GitLab afin que celui-ci puisse vous identifier.


## Installation du projet

* Ajouter les variables d'environnement `http_proxy` et `https_proxy` à Windows (Panneau de configuration, Modifier les variables d'environnement pour votre compte).


Valeur des variables `http_proxy` et `https_proxy` :

```
http://mon-compte:mon-mot-de-passe@http.proxyvip.alize:8080
```

* Relancer le bash pour que la variable soit prise en compte.


Vous pouvez vérifier que les variables sont bien présentes :

```shell script
echo %http_proxy%
http://mon-compte:mon-mot-de-passe@http.proxyvip.alize:8080
echo %https_proxy%
http://mon-compte:mon-mot-de-passe@http.proxyvip.alize:8080
```

Utilisation de Git Bash (fourni avec l'installation de Git Windows) pour configurer Git :

```shell script
git config --global user.name 'Prénom Nom'
git config --global user.email 'Mon adresse électronique'
git config --global http.sslVerify false
git config --global http.proxy 'http://mon-identifiant-adc:MotDePasse@http.proxyvip.alize:8080'
git clone git@dev.gitlab.cisirh:departement-innovation/si-competences/sicardi.git
```

Vous pouvez maintenant lancer l'installation des dépendances :

```shell script
php composer.phar install
```

Vous pouvez maintenant lancer le serveur :

```shell script
php composer.phar server:run
```

Puis aller sur http://127.0.0.1:8000


## Installation de la base de données

* Installer le SGBDR Oracle avec le mot de passe "password"
* Installer SQL Developer
* Lancer SQL Plus :

```shell script
sqlplus
```

**Se connecter avec l'utilisateur "system" et le mot de passe "password"**

* Lancer les commandes :

```sql
alter session set "_oracle_script"=true;
create user sicardi identified by sicardi;
grant dba to sicardi;
```

* Lancer SQL Developer et se connecter à la base de données
* Importer la base à partir de l'export fourni

**Lors de l’import du fichier, si une erreur de “OutOfMemory” apparait,
  il faut modifier le fichier `/dossier_sql_developer/ide/bin/ide.conf` :**

```
# The options for 64-bit Java VM's
Add64VMOption  -Xms20000M
Add64VMOption  -Xmx30G
```

* Modifier le `.env.local` et mettre le bon `DATABASE_URL`
* Lancer les migrations :

```shell script
php bin/console doctrine:migration:migrate
```

## Installation de wkhtmltopdf

* Copier l'executable quelque part sur votre ordinateur
* Modifier le `.env.local` et mettre le bon chemin `WKHTMLTOPDF_PATH`

## PATH LibreOffice

* Modifier le `.env.local` et mettre le bon chemin `LIBREOFFICE_PATH` (généralement "C:/Program Files (x86)/LibreOffice/program/soffice.exe")