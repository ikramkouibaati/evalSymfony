<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Le titre contient',
                'required' => false
            ])
            ->add('marque', TextType::class, [
                'label' => 'La marque contient',
                'required' => false
            ])
            ->add('modele', TextType::class, [
                'label' => 'Le modele contient',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'La description contient',
                'required' => false
            ])
            ->add('prix_journalier', NumberType::class, [
                'label' => 'Le prix journalié est de ',
                'required' => false,
                'html5' => true
            ])
            ->add('orderprix_journalier', ChoiceType::class, [
                'label' => 'Trier les résultats avec les prix : ',
                'placeholder' => 'Ne pas trier',
                'choices' => [
                    'Ascendant' => 'ASC',
                    'Déscendant' => 'DESC'
                ],
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'validate' => false
            ])
            ->add('reinitialiser', SubmitType::class, [
                'label' => 'Réinitialiser',
                'validate' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //'data_class' => Vehicule::class,
            'data_class' => null
        ]);
    }
}
