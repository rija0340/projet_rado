<?php

namespace App\Form;

use App\Entity\TarifScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TarifScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant_ecolage')
            ->add('montant_inscription')
            ->add('autres_frais')
            ->add('description')
            ->add('niveau')
            ->add('anneeScolaire')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TarifScolaire::class,
        ]);
    }
}
