<?php

namespace App\Form;

use App\Entity\AnneeScolaire;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Service\FeeCalculatorService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    private $feeCalculator;

    public function __construct(FeeCalculatorService $feeCalculator)
    {
        $this->feeCalculator = $feeCalculator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Student information (can be existing or new)
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => function ($etudiant) {
                    return $etudiant->getNom() . ' ' . $etudiant->getPrenom();
                },
                'placeholder' => 'Sélectionnez un étudiant existant ou créez-en un nouveau ci-dessous',
                'required' => false,
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Étudiant existant (optionnel)',
                'label_attr' => ['class' => 'label-text']
            ])
            // Academic year selection
            ->add('anneeScolaire', EntityType::class, [
                'class' => AnneeScolaire::class,
                'choice_label' => 'libelle',
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Année scolaire',
                'label_attr' => ['class' => 'label-text']
            ])
            // Embedded form for new student
            ->add('new_student', EtudiantType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false
            ])
            // Class selection
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => function ($classe) {
                    return $classe->getNom() . ' (' . $classe->getNiveau()->getNom() . ')';
                },
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Classe',
                'label_attr' => ['class' => 'label-text']
            ])
            // Payment information (unmapped fields)
            ->add('payment_amount', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Montant du paiement'
                ],
                'label' => 'Montant du paiement',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
            ->add('payment_mode', ChoiceType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Mode de paiement',
                'label_attr' => ['class' => 'label-text'],
                'choices' => [
                    'Espèces' => 'cash',
                    'Virement bancaire' => 'bank_transfer',
                    'Carte de crédit' => 'credit_card',
                    'Mobile Money' => 'mobile_money'
                ],
                'placeholder' => 'Sélectionnez le mode de paiement',
                'required' => false
            ])
            ->add('payment_reference', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'input input-bordered w-full',
                    'placeholder' => 'Référence du paiement'
                ],
                'label' => 'Référence du paiement',
                'label_attr' => ['class' => 'label-text'],
                'required' => false
            ])
            ->add('payment_type', ChoiceType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'select select-bordered w-full'
                ],
                'label' => 'Type de paiement',
                'label_attr' => ['class' => 'label-text'],
                'choices' => [
                    'Droit d\'inscription' => 'inscription',
                    'Ecolage par mois' => 'ecolage',
                    'Autre' => 'other'
                ],
                'placeholder' => 'Sélectionnez le type de paiement',
                'required' => false
            ])
            // Hidden fields for expected fees
            ->add('expected_registration_fee', HiddenType::class, [
                'mapped' => false,
                'data' => 0
            ])
            ->add('expected_tuition_fee', HiddenType::class, [
                'mapped' => false,
                'data' => 0
            ]);

        // Add form events to dynamically update fees
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($data instanceof Inscription) {
            $anneeScolaire = $data->getAnneeScolaire();
            $classe = $data->getClasse();

            if ($anneeScolaire && $classe) {
                $registrationFee = $this->feeCalculator->getExpectedRegistrationAmount($classe, $anneeScolaire);
                $tuitionFee = $this->feeCalculator->getExpectedTuitionAmount($classe, $anneeScolaire);

                $form->get('expected_registration_fee')->setData($registrationFee);
                $form->get('expected_tuition_fee')->setData($tuitionFee);
            }
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (isset($data['anneeScolaire']) && isset($data['classe'])) {
            $anneeScolaireId = $data['anneeScolaire'];
            $classeId = $data['classe'];

            // Get entities from repository
            $anneeScolaire = $form->get('anneeScolaire')->getData();
            $classe = $form->get('classe')->getData();

            if ($anneeScolaire && $classe) {
                $registrationFee = $this->feeCalculator->getExpectedRegistrationAmount($classe, $anneeScolaire);
                $tuitionFee = $this->feeCalculator->getExpectedTuitionAmount($classe, $anneeScolaire);

                $data['expected_registration_fee'] = $registrationFee;
                $data['expected_tuition_fee'] = $tuitionFee;
                $event->setData($data);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
