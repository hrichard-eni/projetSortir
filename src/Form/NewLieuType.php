<?php

namespace App\Form;

use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du lieu*',
                'attr' => [
                    'placeholder' => 'Tour Eiffel'
                ]
            ])
            ->add('rue', TextType::class, [
                'label' => 'Nom de la rue*',
                'attr' => [
                    'placeholder' => 'Champ de Mars, 5 rue Anatole-France'
                ]
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latiude',
                'required' => false
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'required' => false
            ])
            ->add('ville', EntityType::class, [
                'class' => 'App\Entity\Ville',
                'label' => 'Ville*',
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une ville',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter ce lieu',
                'attr' => [
                    'class' => 'btn btn-secondary'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
