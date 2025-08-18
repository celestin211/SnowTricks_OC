# Export de la sélection

* Créer un `FichierExporter` qui exportera une entité en fonction de leur identifiant.

e.g. : `DossierAgentByIdsFichierExporter` pour les agents.


* Créer la route faisant appel à ce service.

e.g. : `dossier_agent_exporte_by_ids` pour les agents.


* Dans la vue :

```twig
<a data-datatable-action="export"
    data-cible="#datatable-agents"
    data-url="{{ path('dossier_agent_exporte_by_ids') }}"
    data-nom-fichier="agents.xlsx">
    <em class="fa fa-cloud-download"></em> {{ 'action.exporter'|trans }}
</a>
```
