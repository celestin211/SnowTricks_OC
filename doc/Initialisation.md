# Initialisation de Sicardi

## Commandes à exécuter

```shell script
# Création de la base
bin/console doctrine:schema:update --force
# Création d'un administrateur CISIRH
bin/console app:utilisateur:creation super.admin@finances.gouv.fr sicd --super-admin
```

## Tâches planifiées

### Purge des traces

Tous les 19 du mois à 06h00

```shell
install/scripts/traces.sh
```

### Calcul des expériences des agents

Tous les jours à 01h00

```shell
install/scripts/agents.sh
```

### Synchronisation des référentiels INGRES

Tous les jours à 02h00

```shell
install/scripts/ingres.sh
```
