<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnneeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Libellé de l\'année scolaire (ex: 2023-2024)'
                ],
                'label' => 'Libellé',
                'label_attr' => ['class' => 'label-text']
            ])
            ->add('date_debut', DateType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full'
                ],
                'label' => 'Date de début',
                'label_attr' => ['class' => 'label-text'],
                'widget' => 'single_text'
            ])
            ->add('date_fin', DateType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full'
                ],
                'label' => 'Date de fin',
                'label_attr' => ['class' => 'label-text'],
                'widget' => 'single_text'
            ])
            ->add('active', CheckboxType::class, [
                'attr' => [
                    'class' => 'checkbox'
                ],
                'label' => 'Actif',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AnneeScolaire::class,
        ]);
    }
}
