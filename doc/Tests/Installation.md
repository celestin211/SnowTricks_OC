# Tests fonctionnels : Installation et utilisation

## Préparation de l'environnement locale de test

### Proxy

Ajouter la variable d'environnement système :

```
Nom de la variable : NO_PROXY
Valeur de la variable : localhost,127.0.0.0/8,docker-registry.somecorporation.com,127.0.0.1
```

Note : `NO_PROXY` peut perturber l'accès au Git du CISIRH.

### Base de données

Créer une nouvelle base de données et définir `DATABASE_URL` dans `.env.test.local`.

Cette base de données de test sera réinitialisée à chaque lancement d'un test suite.

Avec Oracle :

```
DATABASE_URL=oci8://sicarditest:sicarditest@127.0.0.1:1521/xe
```

Avec SQLite (pas encore fonctionnel sur tous les tests) :

```
DATABASE_URL=sqlite:///%kernel.project_dir%/var/tests.db
```

## Installation

* Installer le driver de pilotage du navigateur

```
vendor/bin/bdi detect drivers
```
Un nouveau dossier `drivers` sera créé à la racine du projet contenant le web driver à utiliser pour lancer les tests.

## Utilisation

Les commandes sont dans `tests.sh`.

Pour tous les lancer :

```
./tests.sh tous
```

Pour lancer uniquement les tests unitaires :

```
./tests.sh unitaires
```

L'ensemble des tests peuvent être lancés et répartis ainsi :

```shell script
./tests.sh connexion
./tests.sh utilisateurs
./tests.sh agents
./tests.sh formations
./tests.sh postes
./tests.sh metiers
./tests.sh referentiels
./tests.sh requetes
```

Note importante :
Dans le fichier `tests\Functional\bootstrap.php`, une fois que la base de données est à jour, il est possible de commenter la ligne :

```php
$application->run(new StringInput('doctrine:schema:update --force -q'));
```

Cela permet de réduire fortement le démarrage de chaque test fonctionnel.

## Couverture

Pour générer la couverture, lancer :

```shell script
./tests.sh genere-couverture
```

La couverture sera générée dans le dossier `phpunit-couverture`.

## Remarques

* `PHP-Webdriver` retourne toujours une réponse 200 même si la route est `NOT FOUND`.
Il n'est donc pas possible de faire: 

```php
$this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
```

* Il nécessaire de faire appel à la méthode `waitThemeLoader()` pour attendre jusqu'à la disparition du `Loader` de page:

```
...
use App\Tests\Utils;
...
$client->request('GET', '/login');

Utils::waitThemeLoader($this->client);

$this->assertSelectorTextContains('h1', 'Connexion');
...
```

* Le web driver retourne parfois une erreur : `Test skipped because of an error in hook method`. Il faut juste relancer les tests.

* Il est parfois nécessaire d'ajouter `sleep(secondes);` pour attendre la réponse d'un traitement en `AJAX`.