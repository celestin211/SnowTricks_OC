# L'utilisation des habilitations

Les habilitations sont gérées dans les `Voter`.

Dans les actions de contrôleur :

```php
$this->denyAccessUnlessGranted(EnumFonctionnalite::DOSSIER_AGENT_MODIFIER, $dossierAgent);
```

Dans les vues Twig :

```twig
{% if is_granted(constant('App\\EnumTypes\\EnumFonctionnalite::DOSSIER_AGENT_MODIFIER'), dossierAgent) %}
    <a>Modifier</a>
{% endif %}
```
