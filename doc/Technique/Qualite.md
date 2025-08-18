# Les outils qualité

PHPCSFixer est lancé automatiquement à chaque commit.
Il s'agit d’un hook Git exécuté côté client (et non côté GitLab).

Il doit donc être lancé manuellement par le développeur après chaque `git clone` via la commande :
```shell script
./make.sh install
```

Le hook bloque le commit tant que les remontées ne sont pas corrigées.

Pour corriger automatiquement les remontées, lancer la commande :
```shell script
./make.sh phpcs
```

⚠️ Après avoir corrigées les remontées, ne pas oublier des les ajouter au commit.

## Les outils de qualité frontaux

Lors de la compilation des `assets`, les outils suivants sont exécutés automatiquement :

* ESLint ;
* Prettier ;
* Styleint.

⚠️ Si une erreur est remontée, la compilation n'est pas bloquée, il convient donc de les corriger et de relancer la compilation.

La plupart des erreurs – mais pas toutes donc – peuvent automatiquement être corrigées via la commande :

```shell script
./make.sh fix-lint
```

## SonarQube

SonarQube est accessible à l'adresse suivante :

http://172.26.3.198:9000/dashboard?id=departement-innovation_si-competences_sicardi_AX4mExLV7YZ4l9KWvBzJ

Pour y accéder depuis Totem, il faut accéder à cette URL depuis le serveur de rebond.
