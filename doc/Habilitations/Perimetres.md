# Les périmètres

Les périmètres se définissent sur les utilisateurs.

Certains profils, comme les administrateurs, ne nécessitent pas de définir des périmètres.
cf. `Profil.restrictionPerimetres`.

Les restrictions sur les périmètres se font automatiquement par le biais des filtres Doctrine.
Les différentes valeurs des périmètres de l'utilisateur sont envoyés au filtre en paramètre.

L'appel aux filtres est réalisé dans les `Repository` des entités concernées.
