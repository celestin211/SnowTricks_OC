# Les filtres JS

## Exemple d'utilisation

Un champ `Formation` permet de filtrer un champ `Spécialité`. La spécialité étant obligatoire, on souhaite toujours pouvoir choisir une spécialité, même si aucune formation n'est choisie ou que la formation choisie n'est associée à aucune spécialité. 

```twig
{# formations.html.twig #}

{{ form_row(
    form.formation,
    {
        'label':  'Formation suivie',
        attr: {
            'data-filtre-type': constant('App\\Service\\Filtre\\Selection\\AbstractSelection::TYPE_AUTRE_FORMATION'),
            'data-filtre-cible': '#'~form.specialite.vars.id,
            'data-filtre-cible-si-aucun-filtre': 'tous'
        }
    }
) }}

{{ form_row(
    form.specialite,
    {
        'label':  'Spécialité',
        attr: {
            'data-filtre-type': constant('App\\Service\\Filtre\\Selection\\AbstractSelection::TYPE_SPECIALITE'),
            'data-filtre-si-vide': 'tous'
        }
    }
) }}
```

Si aucune formation n'est choisie, toutes les spécialités sont disponibles (`data-filtre-cible-si-aucun-filtre="tous"`), de même si la formation n'est liée à aucune spécialité (`data-filtre-si-vide="tous"`).

cf. JSDoc dans `assets/js/filtre/filtre.js`.

---

```php
class DossierAgentAutreFormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formation', null, [
                'placeholder' => '',
                'required' => false,
            ])
            ->add('specialite')
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /**
                 * @var \App\Entity\DossierAgent\DossierAgentAutreFormation|null $dossierAgentAutreFormation
                 */
                $dossierAgentAutreFormation = $event->getData();

                $this->setChampSpecialite(
                    $event->getForm(),
                    $dossierAgentAutreFormation !== null ? $dossierAgentAutreFormation->getFormation() : null
                );
            })
        ;

        $builder->get('formation')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $formationForm = $event->getForm();
            /**
             * @var \App\Entity\Referentiel\AutreFormation|null $formation
             */
            $formation = $formationForm->getData();

            if ($formation !== null) {
                $this->setChampSpecialite($formationForm->getParent(), $formation);
            }
        });
    }

    private function setChampSpecialite(FormInterface $form, ?AutreFormation $autreFormation = null)
    {
        $options = [
            'required' => true,
            'placeholder' => '',
        ];

        // On ne filtre les spécialités que si une formation est choisie
        if ($autreFormation !== null) {
            $specialites = $this->autreFormationSelection->getArray($autreFormation, AbstractSelection::TYPE_SPECIALITE);

            // Et si la formation est associée à une spécialité
            if (!empty($specialites)) {
                $options['choices'] = $specialites;
            }

        }

        $form->add('specialite', null, $options);
    }
}
```

La méthode `setChampSpecialite()` s'assure que la saisie utilisateur soit valide (par exemple que la spécialité choisie corresponde bien à la spécialité de la formation choisie si elle en possède une).
