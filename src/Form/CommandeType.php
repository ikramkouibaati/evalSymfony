<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_heur_depart', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('date_heur_fin', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('membre')
            ->add('vehicule')
            ->add('submit', SubmitType::class, [
                'label' => $options['labelSubmit'],
                'validate' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'labelSubmit' => null
        ]);
    }
}
