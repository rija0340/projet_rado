<?php

namespace App\Form;

use App\Entity\Niveau;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NiveauType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Nom du niveau (ex: Primaire, Collège, Lycée)'
                ],
                'label' => 'Nom',
                'label_attr' => ['class' => 'label-text']
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Description du niveau'
                ],
                'label' => 'Description',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
            ->add('ordre', NumberType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Ordre d\'affichage'
                ],
                'label' => 'Ordre',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Niveau::class,
        ]);
    }
}
