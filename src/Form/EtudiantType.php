<?php

namespace App\Form;

use App\Entity\Etudiant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtudiantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Nom de l\'étudiant'
                ],
                'label' => 'Nom',
                'label_attr' => ['class' => 'label-text']
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Prénom de l\'étudiant'
                ],
                'label' => 'Prénom',
                'label_attr' => ['class' => 'label-text']
            ])
            ->add('date_naissance', DateType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full'
                ],
                'label' => 'Date de naissance',
                'label_attr' => ['class' => 'label-text'],
                'widget' => 'single_text'
            ])
            ->add('sexe', ChoiceType::class, [
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Sexe',
                'label_attr' => ['class' => 'label-text'],
                'choices' => [
                    'Homme' => 'M',
                    'Femme' => 'F'
                ],
                'placeholder' => 'Sélectionnez le sexe'
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Numéro de téléphone'
                ],
                'label' => 'Téléphone',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Etudiant::class,
        ]);
    }
}
