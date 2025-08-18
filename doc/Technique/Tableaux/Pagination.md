# Mise en place d'une pagination

## Création du service Lister

Il suffit de créer un service étendant `AbstractLister` et de définir la méthode `getQueryBuilder()`.

D'autres méthodes peuvent être redéfinies ; entre autres :

* `orderBy()` : Permettant de gérer le tri demandé par le Datatable ;
* `getResultatsFormates()` : Permettant de formatter les résultats à envoyer au `Datatable` (par exemple pour ajouter ou modifier une colonne).

## Création de la route

```php
/**
 * @Route("/ma-liste", name="ma_liste", methods={"POST"}, options={"expose": true})
 */
private function liste(Request $request, MonLister $monLister): JsonResponse
{
    $this->denyAccessUnlessGranted(...);

    $conditions = $request->request->get('conditions', []);

    return new JsonResponse($monLister->getDatatableResponse($conditions, $request->request));
}
```

## Création du `Datatable`

```javascript
Pagination.initMonTableau = function ($datatable, conditions) {
    $datatable.DataTable({
        ajax: {
            url: Routing.generate('ma_liste'),
            method: 'POST',
            data: { conditions: conditions }
        },
        columns: [
            {
                data: 'param_1',
            },
            // ...
        ],
    });
};
```

## Vue

```twig
<table class="table table-striped table-bordered" id="mon-datatable">
    <thead>
        <tr>
            <th>Param 1</th>
        </tr>
    </thead>
</table>

<script type="text/javascript">
    Pagination.initMonTableau($('#mon-datatable'), {{ form_conditions|json_encode|raw('json') }});
</script>
```
