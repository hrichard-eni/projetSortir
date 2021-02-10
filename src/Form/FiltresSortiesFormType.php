<?php

namespace App\Form;


use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;


class FiltresSortiesFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $campus){
        $builder
            ->add('campus', EntityType::class, [
                'class' =>Campus::class,
                'choice_label' => 'nom',
                'multiple' => false

            ]);
    }
}

