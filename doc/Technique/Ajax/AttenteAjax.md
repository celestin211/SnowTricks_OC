# Mise en place d'une animation d'attente

L'animation d'attente permet de voiler un élément de la page tout en affichant une animation de chargement.

Les méthodes à utiliser sont :

```javascript
// Voile l'élément
AjaxLoader.show(element);

// Dévoile l'élément une fois que celui-ci a été mis à jour
AjaxLoader.hide(element);
```

Exemple :

```javascript
const form = document.getElementById('mon-form');

// Voilage du formulaire
AjaxLoader.show(form);

fetch(form.action, {
    method: 'POST',
    body: new FormData(form),
    credentials: 'same-origin',
}).then((response) => {
    if (response.ok) {
        // Rechargement de la page
        Navigation.actualise();
    }

    return response.text();
}).then((html) => {
    // Affichage du formulaire avec les erreurs
    modal.querySelector('.modal-body').innerHTML = html;
    
    // Le formulaire est rendu visible
    AjaxLoader.hide(form);
});
```
