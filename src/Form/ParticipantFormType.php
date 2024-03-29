<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ParticipantFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo :'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :'
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom :'
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone :',
                'required' => false
            ])
            ->add('password', RepeatedType::class, [

                'type' => PasswordType::class,
                'invalid_message' => "Confirmation différente du mot de passe",
                'required' => true,
                'first_options' => ['label' => 'Mot de passe :'],
                'second_options' => ['label' => 'Confirmer le mot de passe :']

            ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus :',
                'class' => 'App\Entity\Campus',
                'invalid_message' => 'Eh le pirate ! Pas touche à notre code !',
                'choice_label' => 'nom'
            ])

            ->add('avatar', FileType::class, [
                'label' => 'Avatar :',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Fichier trop lourd, 5Mo max !',
                        'maxHeight' => 500,
                        'maxHeightMessage' => 'Image trop grande ! (maximum 500 x 500)',
                        'maxWidth' => 500,
                        'maxWidthMessage' => 'Image trop grande ! (maximum 500 x 500)',
                    ])
                ]
            ])

            ->add('submit', SubmitType::class, [
                "label"=>"Modifier",
                "attr" => ['class'=>'btn btn-primary col-4 col-lg-2']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
