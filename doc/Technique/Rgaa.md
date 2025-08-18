# RGAA

## Images et icônes

Pour chaque icône, avoir un `title` et un `aria-label` avec un libellé précis (par exemple `Afficher le calendrier de la date de début` plutôt que `Calendrier`).

Si un graphisme concerne de la décoration (comme la photo de profil), avoir `alt=""` vide ou `aria-hidden="true"`.

## Contrastes

Le contraste doit être au minimum de 4,5 pour le texte (même les placeholder) et 3 pour les graphismes (comme les bordures).
Peu importe pour les éléments de décoration.

Pour les boutons désactivés, soit le contraste est suffisant soit il faut ajouter un `title` et un `aria-label`.

## Tableaux

Pour les colonnes avec des cases à cocher, il faut ajouter un titre d'en-tête comme un `<th>Sélectionner les postes</th>` soit `<th title="Sélectionner les postes" aria-label="Sélectionner les postes"><input type="checkbox"></th>`.
