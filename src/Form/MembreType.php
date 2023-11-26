<?php

namespace App\Form;

use App\Entity\Membre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo')
            ->add('mdp')
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('civilite', ChoiceType::class, [
                'choices' => [
                    'homme' => 'm',
                    'femme' => 'f'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => $options['statusChoice']
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['labelSubmit'],
                'validate' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Membre::class,
            'statusChoice' => null,
            'labelSubmit' => null
        ]);
    }
}
