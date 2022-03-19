#BOSONGO CELESTIN

Le deploiment de l'application

Projet OpenClassrooms site communautaire SnowTricks

## Informations du projet

Projet de la formation ***Développeur d'application - PHP / Symfony***.

**Développez de A à Z le site communautaire SnowTricks**

## Badges du projet

[![Codacy Badge]

## Descriptif du besoin

    Vous êtes chargé de développer le site répondant aux besoins de Jimmy.
    Vous devez ainsi implémenter les fonctionnalités suivantes :
    un annuaire des figures de snowboard.
    Vous pouvez vous inspirer de la liste des figures sur Wikipédia. Contentez-vous d'intégrer 10 figures, le reste sera saisi par les internautes ;
    la gestion des figures (création, modification, consultation) ;
    un espace de discussion commun à toutes les figures.

Pour implémenter ces fonctionnalités, vous devez créer les pages suivantes :

    la page d’accueil où figurera la liste des figures ;
    la page de création d'une nouvelle figure ;
    la page de modification d'une figure ;
    la page de présentation d’une figure (contenant l’espace de discussion commun autour d’une figure).

## Installation

1. Clonez le dépot dans votre ordinateur :
```
    git clone https://github.com/celestin211/SnowTricks.git
```

2. Modifier le .env selon la base de données que vous avez l'habitude d'utiliser (MYSQL, postgresql, sqlite)

3. Installez les dependances :
```
    composer install ou update
    ```
4. Mettez en place la BDD :
```
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
`
