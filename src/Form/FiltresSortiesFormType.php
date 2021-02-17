<?php

namespace App\Form;


use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;


class FiltresSortiesFormType extends AbstractType
{
// Form du menu déroulant affichant les noms des différents campus sur la home
    public function buildForm(FormBuilderInterface $builder, array $campus){
        $builder
            ->add('campus', EntityType::class, [
                'class' =>Campus::class,
                'choice_label' => 'nom',
                'multiple' => false,
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'choice',
                'label' => "A partir de :"
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'choice',
                'label' => "Jusqu'à :"
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
            ])
            ->add('isInscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e",
                'required' => false,
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e",
                'required' => false,
                'mapped' => false
            ])


            ;

    }
}

