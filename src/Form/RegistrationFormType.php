<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prenom', TextType::class, [
                'label' => 'Prénom*',
                'attr' => [
                    'placeholder' => 'John'
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom*',
                'attr' => [
                    'placeholder' => 'Smith'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo*',
                'attr' => [
                    'placeholder' => 'Johnny2345'
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '0234567890'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email*',
                'attr' => [
                    'placeholder' => 'john.smith@eni.fr'
                ]
            ])
            ->add('campus', EntityType::class, [
                'class' => 'App\Entity\Campus',
                'invalid_message' => 'Eh le pirate ! Pas touche à notre code !',
                'choice_label' => 'nom'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe'
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez spécifier un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Mot de passe trop court ! 8 caractères minimum requis',
                        // Max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'maxMessage' => 'La limite du mot de passe est de 4096 caractères pour des raisons de sécurité'
                    ]),
                ]
            ])
            //Bouton de validation
//            ->add('create', SubmitType::class, [
//                'label' => 'Créer ce participant',
//                'attr' => [
//                    'class' => 'btn btn-secondary custom-button'
//                ]
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
