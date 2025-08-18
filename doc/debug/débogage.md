# Débogage avec Xdebug

### Avertissement

L'utilisation de Xdebug décrite ici ralentit le chargement des pages.  
Cela vient du fait qu'Xdebug essaye de contacter PhpStorm qui écoute sur le port 9000.  
Pour revenir comme avant, veuillez définir : `xdebug.remote_enable=0`.

## Installation

Si vous avez déjà [Xdebug d'installé](#v%C3%A9rifier-si-xdebug-est-pr%C3%A9sent) vous pouvez directement passer à sa [configuration](#zero-configuration).

 1. Téléchargez https://xdebug.org/files/php_xdebug-2.7.1-7.1-vc14-nts.dll
 2. Renommez le fichier téléchargé `php_xdebug[…].dll` en `php_xdebug.dll`
 3. Déterminez votre répertoire d'extensions PHP en utilisant dans la CLI : 
 
```sh
php -i | grep 'extension_dir'
```

 4. Déplacez le fichier `php_xdebug.dll` dans votre répertoire d'extensions PHP
 5. [Vérifier l'installation de Xdebug](#v%C3%A9rifier-si-xdebug-est-pr%C3%A9sent), puis passez à la suite.

## Intégration de Xdebug avec l'interpréteur PHP 

 1. Ouvrez le fichier `php.ini` actif dans l'éditeur
 2. Il faut désactiver les outils Zend Debugger et Zend Optimizer, qui bloquent Xdebug, supprimez ou commentez les lignes suivantes dans le fichier php.ini :

```
zend_extension=<path_to_zend_debugger>
zend_extension=<path_to_zend_optimizer>
```

 3. Pour activer Xdebug, localisez/créez la section `[Xdebug]` dans le fichier `php.ini` et mettez-la à jour comme suit :

```
[Xdebug]
zend_extension=php_xdebug.dll
xdebug.remote_enable=1
xdebug.remote_port=9000
```

 4. [Vérifier à nouveau qu'Xdebug est toujours bien installé](#v%C3%A9rifier-si-xdebug-est-pr%C3%A9sent)

## Zero-Configuration

Pour aller au plus simple et rapide, il existe une "configuration zéro".  
Vous n'avez pas besoin de créer de configuration de débogage.  
Au lieu de cela, vous ouvrez manuellement la page de démarrage de votre application PHP dans le navigateur, puis activez le moteur de débogage depuis le navigateur, tandis que PhpStorm écoute les connexions entrantes du débogueur.

### Activer l'écoute des connexions de débogage entrantes

Activez le bouton `Start Listen PHP Debug Connections` ![commencer à écouter les connexions de débogage php ](https://www.jetbrains.com/help/img/idea/2019.1/php.icons.debug_listen_off@2x.png)  dans la barre d'outils PhpStorm pour qu'il change en ![arrêter d'écouter les connexions de débogage php](https://www.jetbrains.com/help/img/idea/2019.1/php.icons.debug_listen_on@2x.png).  
![Commencez à écouter le débogage des connexions](https://www.jetbrains.com/help/img/idea/2019.1/ps_quick_start_zero_debug_stop_listening_debug_connections_toolbar.png)

### Définir des points d'arrêt dans votre code

Définissez un point d'arrêt dans votre code en procédant de l'une des façons suivantes :

 - Cliquez sur la zone de la gouttière gauche à une ligne où vous voulez mettre un point d'arrêt.
 - Dans le menu principal, choisissez **Run | Toggle Line Breakpoint**.
 - Appuyez sur `Ctrl+F8`.

![Définir un point d'arrêt](https://www.jetbrains.com/help/img/idea/2019.1/ps_quick_start_set_breakpoints_mac.png)

### Activer le débogueur sur le serveur 

Pour activer le démarrage et l'arrêt du moteur de débogage à partir du navigateur, vous devez définir un paramètre `COOKIE` spécial.  
Nous utilisons un navigateur pour lequel aucune extension ne peut-être installé, il faut [générer](https://www.jetbrains.com/phpstorm/marklets/) les bookmarklets _Start Debugger/Stop Debugger_ et les ajouter à la barre d'outils de votre navigateur.
Une fois ajouté, cliquez sur le lien _Start debugger_.

### Start the debugging session

 1. Rechargez la page dans le navigateur et revenez à PhpStorm, une nouvelle fenêtre à fait son apparition
 2. Après avoir atteint le point d'arrêt, le débogueur est suspendu, une nouvelle fenête fait son apparition, cliquez sur _Accept_ : 
 
![image](/uploads/035f98880626776df9b380ff96f60f9e/image.png)
 
 3. Vous pouvez maintenant examiner l'application. Continuez à exécuter le programme et examinez ses trames dès qu'il est à nouveau suspendu. 

## Vérifier si Xdebug est présent

Dans la ligne de commande, exécutez la commande suivante :

```sh
php --version
```

La sortie doit lister Xdebug parmi les extensions installées :

```sh
PHP 7.1.26 (cli) (built: Jan  9 2019 22:04:54) ( NTS MSVC14 (Visual C++ 2015) x86 )
Copyright (c) 1997-2018 The PHP Group
Zend Engine v3.1.0, Copyright (c) 1998-2018 Zend Technologies
    with Xdebug v2.7.1, Copyright (c) 2002-2019, by Derick Rethans
```

