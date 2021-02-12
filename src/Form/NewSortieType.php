<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class NewSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'invalid_message' => 'Ceci n\'est pas une date valide',
                'placeholder' => '-'
            ])

            ->add('duree_nombre', ChoiceType::class, [
                'label' => 'Durée',
                'mapped' => false,
                'choices' => [
                    //Parce que range() faisait n'importe quoi ^^ Ca va de 1 à 24
                    '1' => 1,'2' => 2,'3' => 3,'4' => 4,'5' => 5,'6' => 6,'7' => 7,'8' => 8,'9' => 9,'10' => 10,
                    '11' => 1,'12' => 2,'13' => 3,'14' => 4,'15' => 5,'16' => 6,'17' => 7,'18' => 8,'19' => 9,'20' => 20,
                    '21' => 1,'22' => 2,'23' => 3,'24' => 4
                ],
                'placeholder' => '-',
            ])
            ->add('duree_unite', ChoiceType::class, [
                'label' => 'Unité de temps',
                'mapped' => false,
                'choices' => [
                    'Heure(s)' => 1,
                    'Jour(s)' => 2
                ]
            ])

            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date de fin d\'inscription',
                'invalid_message' => 'Ceci n\'est pas une date valide',
                'placeholder' => '-'
            ])

            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre max de participants',
                'invalid_message' => 'Ceci n\'est pas un nombre de personnes valide'
            ])

            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description et infos complémentaires'
            ])

            ->add('lieu', EntityType::class, [
                'class' => 'App\Entity\Lieu',
                'invalid_message' => 'Eh le pirate ! Pas touche à notre code !',
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un lieu '
            ])

            //Pré-rempli par le controller
            ->add('organisateur', EntityType::class, [
                'class' => 'App\Entity\Participant',
                'label' => 'Organisateur',
                'choice_label' => 'pseudo',
                'disabled' => true,
                'placeholder' => 'Change pas ca marchera pas'
            ])

            //Pré-rempli par le controller
            ->add('campusOrganisateur', EntityType::class, [
                'class' => 'App\Entity\Campus',
                'label' => 'Campus organisateur',
                'choice_label' => 'nom',
                'disabled' => true,
                'placeholder' => 'Change pas ca marchera pas'
            ])

//            ->add('participants') : Géré sur le détail d'une sortie
//            ->add('etat') : Géré dans le controller
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
