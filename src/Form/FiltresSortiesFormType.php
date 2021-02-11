<?php

namespace App\Form;


use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                

            ]);
    }
}

