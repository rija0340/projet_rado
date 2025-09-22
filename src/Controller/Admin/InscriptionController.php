<?php

namespace App\Controller\Admin;

use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Service\InscriptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/inscription", name="admin_inscription_")
 */
class InscriptionController extends AbstractController
{
    private $inscriptionService;

    public function __construct(InscriptionService $inscriptionService)
    {
        $this->inscriptionService = $inscriptionService;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $filters = [
            'student_name' => $request->query->get('student_name'),
            'classe_id' => $request->query->get('classe_id'),
            'statut' => $request->query->get('statut')
        ];

        $inscriptions = $this->inscriptionService->getInscriptions($filters);

        return $this->render('admin/inscription/index.html.twig', [
            'inscriptions' => $inscriptions,
            'filters' => $filters
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $inscription = new Inscription();
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Prepare student data
                $studentData = [];
                if ($form->get('etudiant')->getData()) {
                    // Use existing student
                    $studentData['id'] = $form->get('etudiant')->getData()->getId();
                } else {
                    // Create new student
                    $studentData = [
                        'nom' => $form->get('new_student_nom')->getData(),
                        'prenom' => $form->get('new_student_prenom')->getData(),
                        'date_naissance' => $form->get('new_student_date_naissance')->getData(),
                        'sexe' => $form->get('new_student_sexe')->getData(),
                        'telephone' => $form->get('new_student_telephone')->getData()
                    ];
                }

                // Prepare payment data
                $paymentData = [
                    'montant' => $form->get('payment_amount')->getData(),
                    'mode_paiement' => $form->get('payment_mode')->getData(),
                    'reference' => $form->get('payment_reference')->getData(),
                    'type' => $form->get('payment_type')->getData()
                ];

                // Create inscription
                $inscription = $this->inscriptionService->createInscription(
                    $studentData,
                    $form->get('classe')->getData()->getId(),
                    $paymentData
                );

                $this->addFlash('success', 'Inscription created successfully!');
                return $this->redirectToRoute('admin_inscription_show', ['id' => $inscription->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error creating inscription: ' . $e->getMessage());
            }
        }

        return $this->render('admin/inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(Inscription $inscription): Response
    {
        return $this->render('admin/inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Inscription $inscription): Response
    {
        // For simplicity, we're not implementing full edit functionality
        // In a real application, you might want to allow editing certain fields
        return $this->redirectToRoute('admin_inscription_show', ['id' => $inscription->getId()]);
    }

    /**
     * @Route("/{id}/confirm", name="confirm", methods={"POST"})
     */
    public function confirm(Request $request, Inscription $inscription): Response
    {
        if ($this->isCsrfTokenValid('confirm'.$inscription->getId(), $request->request->get('_token'))) {
            try {
                $this->inscriptionService->confirmInscription($inscription);
                $this->addFlash('success', 'Inscription confirmed successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error confirming inscription: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('admin_inscription_show', ['id' => $inscription->getId()]);
    }

    /**
     * @Route("/{id}/cancel", name="cancel", methods={"POST"})
     */
    public function cancel(Request $request, Inscription $inscription): Response
    {
        if ($this->isCsrfTokenValid('cancel'.$inscription->getId(), $request->request->get('_token'))) {
            try {
                $reason = $request->request->get('reason', '');
                $this->inscriptionService->cancelInscription($inscription, $reason);
                $this->addFlash('success', 'Inscription cancelled successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error cancelling inscription: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('admin_inscription_show', ['id' => $inscription->getId()]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, Inscription $inscription): Response
    {
        if ($this->isCsrfTokenValid('delete'.$inscription->getId(), $request->request->get('_token'))) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($inscription);
                $entityManager->flush();
                $this->addFlash('success', 'Inscription deleted successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error deleting inscription: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('admin_inscription_index');
    }
}