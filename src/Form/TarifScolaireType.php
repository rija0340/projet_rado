<?php

namespace App\Form;

use App\Entity\TarifScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant_ecolage', NumberType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Montant de l\'écolage'
                ],
                'label' => 'Montant Écolage',
                'label_attr' => ['class' => 'label-text']
            ])
            ->add('montant_inscription', NumberType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Montant de l\'inscription'
                ],
                'label' => 'Montant Inscription',
                'label_attr' => ['class' => 'label-text']
            ])
            ->add('autres_frais', NumberType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Autres frais'
                ],
                'label' => 'Autres Frais',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Description des frais'
                ],
                'label' => 'Description',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
            ->add('niveau', null, [
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Niveau',
                'label_attr' => ['class' => 'label-text'],
                'placeholder' => 'Sélectionnez un niveau'
            ])
            ->add('anneeScolaire', null, [
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Année Scolaire',
                'label_attr' => ['class' => 'label-text'],
                'placeholder' => 'Sélectionnez une année scolaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TarifScolaire::class,
        ]);
    }
}
