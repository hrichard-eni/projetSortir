<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])

            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie'
            ])

            ->add('duree', TimeType::class, [
                'label' => 'Durée'
            ])

            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date de fin d\'inscription'
            ])

            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre max de participants'
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
                'disabled' => true
            ])

            //Pré-rempli par le controller
            ->add('campusOrganisateur', EntityType::class, [
                'class' => 'App\Entity\Campus',
                'label' => 'Campus organisateur',
                'choice_label' => 'nom',
                'disabled' => true
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
