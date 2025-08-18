# Recevoir les courriels avec faux serveur de mails

## Installation fakeSMTP

fakeSMTP est un binaire java qui permet de créer un serveur SMTP en local.
Il est simple d'utilisation et rapide à mettre en place.

```
http://nilhcem.com/FakeSMTP/downloads/fakeSMTP-latest.zip
```

Définissez un dossier pour enregistrer les emails et démarrez le serveur.

## Configuration fakeSMTP

Pour recevoir des emails via smtp il faut configurer votre fichier `.env` :

```
MAILER_DSN=smtp://localhost
```

## Installation Maildev
> Maildev est un autre outil different et independant de fakeSMTP.

Il est possible de créer un mini serveur smtp et une fausse messagerie pour recevoir 
consulter les emails en local.
L'outil est accessible sur le site: http://maildev.github.io/maildev/

On peut l'installer de deux façons soit avec docker , npm, en telechargent le binaire.

```shell script
npm install -g maildev
```

Pour lance le server il est preferable d'utiliser la commande suivante:

```shell script
maildev maildev --web 1080 --smtp 25 --hide-extensions STARTTLS
```

On peut acceder a la messagerie avec le lien `localhost:1080`

### Configuration dans le projet 
Il faut utliser le port 25

```
MAILER_DSN=smtp://localhost:25
```