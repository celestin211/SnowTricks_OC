<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Utilisateur;
use App\EnumTypes\EnumCiviliteType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UtilisateurType extends AbstractType
{
    private $security;
    private $ministereManager;
    private $directionManager;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = [];

        /* @var Utilisateur $utilisateur */
        $utilisateur = $builder->getData();
        $role = $utilisateur ? $utilisateur->getRole() : null;

        $builder
            ->add('nom')
            ->add('prenom')
            ->add('civilite', ChoiceType::class, [
                'choices' => [
                    'M.' => EnumCiviliteType::MONSIEUR,
                    'Mme' => EnumCiviliteType::MADAME,
                ],
                'expanded' => false,
                'multiple' => false,
                'placeholder' => '',
            ])
            ->add('email')

            ->add('password', PasswordType::class, array(
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Mot de passe',
                ],
            ))

            ->add('role', ChoiceType::class, [
                'choices' => $roles,
                'expanded' => true,
                'data' => $role,
                'multiple' => false,
                'mapped' => false,
                'constraints' => new NotBlank(['message' => 'Champ obligatoire']),
            ])
            ->add('passwordConfirm', PasswordType::class, array(
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Mot de passe',
                ],
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
