## Installation des assets

cf. `package.json`.

```shell script
npm install
```

## Compilation

cf. `package.json`.

Environnement de production :

```shell script
./make.sh encore-prod
```

Environnement de développement :

```shell script
./make.sh encore-dev
```

Mode espion :

```shell script
./make.sh encore-watch
```

## Qualité du code

### ESLint

La configuration est située dans `.eslintrc`.
Une modification de ce fichier nécessite le vidage du cache (`node_modules/.cache`).

Pour désactiver (provisoirement) ESLint, il suffit de désactiver la ligne `.enableTypeScriptLoader()` dans le fichier `webpack.config.js`.

Il est possible de corriger automatiquement certaines remontées avec la commande :

```shell script
./make.sh fix-lint
```

### StyleLint

La configuration est située dans `.stylelintrc`.
