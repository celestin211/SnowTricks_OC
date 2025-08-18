# Mise en place des cases à cocher dans un tableau

## Ajout de la colonne

* Pour ajouter la colonne avec les cases à cocher, il faut ajouter une colonne `id` et spécifier l'attribut `checkboxes` :

Par exemple :

```javascript
$datatable.DataTable({
    // ...
    columns: [
        // La vraie colonne qui affiche l'ID
        {
            data: 'id',
            orderable: true,
        },
        {
            data: 'nom_complet',
            orderable: true,
        },
        // Case à cocher
        {
            data: 'id',
            orderable: false,
        },
    ],
    columnDefs: [
        {
            targets: 2,
            checkboxes: {
                // Dans le cas d'une mise à jour en AJAX, avoir `selectAll` à `true` 
                // permet juste de cocher les cases de la page courante
                selectAll: false,
            },
        },
    ],
});
```

## Actions

### Tout sélectionner / désélectionner

```twig
<nav class="datatable-actions" data-target="#mon-tableau">
    <ul>
        <li>
            <a class="datatable-tout-cocher" data-cible="#datatable-requete-libre-agents" data-url="{{ path('requete_utilisateur_dossier_agent_get_ids') }}" data-parametres='{{ requete_utilisateur_serialise|raw_securise }}'>
                {{ 'datatable.tout_cocher'|trans }}
            </a> /
            <a class="datatable-tout-decocher" data-cible="#datatable-requete-libre-agents">
                {{ 'datatable.tout_decocher'|trans }}
            </a>
        </li>
    </ul>
</nav>
```

Si `data-url` n'est pas défini, seules les cases connues du tableau seront sélectionnées ; c'est-à-dire toutes les cases de la page courante et dans l'ensemble des pages uniquement si les données ne sont pas récupérées via un appel AJAX.


### Supprimer

```twig
<nav class="datatable-actions" data-target="#mon-tableau">
    <ul>
        {% if is_granted('SUPPRIMER_EN_MASSE') %}
            <li>
                <a data-datatable-action="suppression-selection" data-cible="#datatable-referentiel" data-url="{{ path('referentiel_supprime_en_masse', { referentiel: referentiel }) }}">
                    <em class="fa fa-trash"></em> {{ 'datatable.supprimer_selection'|trans }}
                </a>
            </li>
        {% endif %}
    </ul>
</nav>
```


## Récupérer la sélection

Récupérer les IDs :

```javascript
const $datatable = $(document.getElementById('mon-tableau')),
    selection = $datatable.DataTable().column($datatable.DataTable().columns().count() - 1).checkboxes.selected();

// Liste des IDs
let ids = [];

for (let i = 0; i < selection.length; i++) {
    ids.push(parseInt(selection[i]));
}
```

## Autres méthodes

La librairie d'origine a été modifiée car certaines de ces méthodes étaient absentes.

### Sélectionner des lignes

```javascript
const $datatable = $('#mon-tableau'),
    ids = [1, 2, 3];

$datatable.DataTable().column($datatable.DataTable().columns().count() - 1).checkboxes.selectIds(ids);
$datatable.DataTable().clear().draw();
```

### Tout désélectionner

```javascript
$datatable.DataTable().column($datatable.DataTable().columns().count() - 1).checkboxes.deselectAll();
```
