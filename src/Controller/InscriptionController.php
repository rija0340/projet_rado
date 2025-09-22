<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Service\FeeCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InscriptionController extends AbstractController
{
    private $entityManager;
    private $feeCalculator;

    public function __construct(EntityManagerInterface $entityManager, FeeCalculatorService $feeCalculator)
    {
        $this->entityManager = $entityManager;
        $this->feeCalculator = $feeCalculator;
    }

    /**
     * @Route("/inscription", name="app_inscription_index")
     */
    public function index(): Response
    {
        // Get all inscriptions
        $inscriptions = $this->entityManager->getRepository(Inscription::class)->findAll();
        
        return $this->render('admin/inscription/index.html.twig', [
            'inscriptions' => $inscriptions,
        ]);
    }
    
    /**
     * @Route("/inscription/new", name="app_inscription_new")
     */
    public function new(Request $request): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingStudent = $form->get('etudiant')->getData();
            $anneeScolaire = $form->get('anneeScolaire')->getData();
            $classe = $form->get('classe')->getData();
            
            if ($existingStudent) {
                $inscription->setEtudiant($existingStudent);
            } else {
                // Get data from embedded form
                $newStudentForm = $form->get('new_student');
                if ($newStudentForm->getData()) {
                    $newStudent = $newStudentForm->getData();
                    $this->entityManager->persist($newStudent);
                    $inscription->setEtudiant($newStudent);
                }
            }
            
            $inscription->setAnneeScolaire($anneeScolaire);
            $inscription->setDateInscription(new \DateTime());
            $inscription->setStatut(Inscription::STATUS_PENDING);
            
            $this->entityManager->persist($inscription);
            $this->entityManager->flush();

            // Handle payment if provided
            $paymentAmount = $form->get('payment_amount')->getData();
            if ($paymentAmount && is_numeric($paymentAmount)) {
                $paymentMode = $form->get('payment_mode')->getData();
                $paymentReference = $form->get('payment_reference')->getData();
                $paymentType = $form->get('payment_type')->getData();
                
                // Create payment entity
                $payment = new \App\Entity\Paiement();
                $payment->setInsciption($inscription);
                $payment->setMontant((float)$paymentAmount);
                $payment->setDatePaiement(new \DateTime());
                $payment->setModePaiement($paymentMode ?? 'cash');
                $payment->setReference($paymentReference ?? '');
                $payment->setStatut('confirmed'); // Default to confirmed for now
                $payment->setType($paymentType ?? 'other');
                
                // Set the tariff based on academic year and class
                $tarifScolaire = $this->feeCalculator->getTuitionFees($classe, $anneeScolaire);
                if ($tarifScolaire) {
                    $payment->setTarifScolaire($tarifScolaire);
                }
                
                $this->entityManager->persist($payment);
                $this->entityManager->flush();
            }

            return $this->redirectToRoute('app_inscription_index');
        }

        return $this->render('admin/inscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
