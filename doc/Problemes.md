# Résolution des problèmes

## Oracle

### Mot de passe expiré

**Problème** : À la connexion Oracle, vous obtenez l'erreur :

```
ORA-28001: The password has expired
```

**Résolution** :

* Lancer `SQL Plus` ;

* Saisir comme nom d'utilisateur celui de la connexion Oracle ;

* Saisir le mot de passe actuel ;

* Saisir ensuite deux fois le nouveau mot de passe (qui peut être identique).


## Composer

### Échec de Composer

Si une erreur intervient pendant un `composer`, par exemple :

```sh
array_keys() expects parameter 1 to be array, null given
```

il peut s'agir d'un problème de proxy.

Tester par exemple en remplaçant la variable d'environnement `https_proxy` par `http_proxy`.

## GIT

### Problème de droit (erreur 403)

Dans le cas où un nouveau compte gitlab est créé pour un même développeur, il peut arriver que l'on ait une 403 au moment du push avec le nouveau compte.
Dans ce cas, sous windows, bien vérifier dans le gestionnaire d'identification que ce n'est plus l'ancien compte/mot de passe qui est enregistré mais bien le nouveau.


## Problème de droit (erreur 407)
Dans le cas où vous êtes face à une erreur 407, il est important de bien vérifier ses variables d'environnements 'http_proxy'et 'https_proxy', ajouter une adresse IP comme ceci 'http://mon-identifiant-adc:MotDePasse@172.24.13.36:8080'.
L'adresse IP va servir de passerelle pour avoir accès au compte Gitlab

## BDD erBDD erreur doublon de tables
Il faut absolument avoir une base de données cohérentes avec le projet Cf: doc/installation.mdreur doublon de tables
