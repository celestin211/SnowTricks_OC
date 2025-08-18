# Quelques normes

## Général

De manière générale, utiliser les standards communs :

* Symfony : https://symfony.com/doc/current/best_practices.html ;
* Twig : https://twig.symfony.com/doc/2.x/coding_standards.html.

---

* Utiliser le français pour tout (commentaires, noms de classes, fonctions, variables, etc).

A l'exception des préfixes comme "get", "set" ou "is".

---

* Ne pas utiliser d'abréviation (pour les commentaires comme pour les variables, etc).

---

* Longueur maximale des lignes : 180.

Dans PhpStorm, cela se paramètre dans Settings > Editor > Code Style > Right margin (columns).


## Traductions

Les préconisations officielles sont respectées sauf le format YAML qui est utilisé.

* Tous les textes doivent utiliser les traductions (à l'exception des Exception) ;
* Pour le JS, le service Translator peut être utilisé ;
* Trier les clefs par ordre alphabétique pour les retrouver plus facilement ;
* Tout en français ;
* Aucune abréviation ;
* Les clefs sont en snake_case.

Exemple :

```yaml
# Actions
action:
    exporter: 
    importer:
    precedent:
    suivant:
    supprimer_selection:

# Libellés
libelle:
    agent:
    corps:
    entite:
    entite_de_gestion:

# Messages
message:
    element:
        confirmation_suppression: Êtes-vous sûr de vouloir supprimer cet élément ?
        creation: L'élément a été créé avec succès.
    import:
        trop_d_erreurs: "Le fichier comporte trop d'erreurs, merci de les corriger afin de réessayer un import."


# Spécifiques à Agent
agent:
    libelle:
        creer: Créer un agent
        rechercher : Rechercher un agent
        supprimer: Supprimer l'agent
    courriel:
        creation_agent:
            message_confirmation: Bla bla
            
# Spécifiques à Utilisateur
utilisateur:
    courriel:
        inscription:
            message_confirmation:
            etc
```

Pour certaines grosses parties, exceptionnellement, des domaines peuvent être créés :

```yaml
# requetes.fr.yaml
libelle:
    cartographie_population: Cartographie de population
    etc
```


## Twig

* Préfixer les fichiers inclus par "_".
