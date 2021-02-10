<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            ->add('organisateur', EntityType::class, [
                'class' => 'App\Entity\Participant',
                'label' => 'Organisateur',
                'choice_label' => 'pseudo',
                'disabled' => true
            ])
//            ->add('participants')
            ->add('campusOrganisateur', EntityType::class, [
                'class' => 'App\Entity\Campus',
                'label' => 'Campus organisateur',
                'choice_label' => 'nom',
                'disabled' => true
            ])
            ->add('lieu', EntityType::class, [
                'class' => 'App\Entity\Lieu',
                'invalid_message' => 'Eh le pirate ! Pas touche à notre code !',
                'choice_label' => 'nom',
                'placeholder' => 'Choisir un lieu '
            ])
//            ->add('etat')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
