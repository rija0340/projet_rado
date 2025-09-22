<?php

namespace App\Controller;

use App\Entity\TarifScolaire;
use App\Form\TarifScolaireType;
use App\Repository\TarifScolaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarif/scolaire")
 */
class TarifScolaireController extends AbstractController
{
    /**
     * @Route("/", name="app_tarif_scolaire_index", methods={"GET"})
     */
    public function index(TarifScolaireRepository $tarifScolaireRepository): Response
    {
        return $this->render('admin/tarif_scolaire/index.html.twig', [
            'tarif_scolaires' => $tarifScolaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_tarif_scolaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TarifScolaireRepository $tarifScolaireRepository, EntityManagerInterface $entityManager): Response
    {
        $tarifScolaire = new TarifScolaire();
        $form = $this->createForm(TarifScolaireType::class, $tarifScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for duplicate combination of niveau and anneeScolaire
            $existingTarif = $tarifScolaireRepository->findOneBy([
                'niveau' => $tarifScolaire->getNiveau(),
                'anneeScolaire' => $tarifScolaire->getAnneeScolaire()
            ]);

            if ($existingTarif) {
                $this->addFlash('error', 'Un tarif existe déjà pour ce niveau et cette année scolaire.');
                return $this->renderForm('admin/tarif_scolaire/new.html.twig', [
                    'tarif_scolaire' => $tarifScolaire,
                    'form' => $form,
                    'tarif_scolaires' => $tarifScolaireRepository->findAll(),
                ]);
            }

            $tarifScolaireRepository->add($tarifScolaire, true);

            return $this->redirectToRoute('app_tarif_scolaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/tarif_scolaire/new.html.twig', [
            'tarif_scolaire' => $tarifScolaire,
            'form' => $form,
            'tarif_scolaires' => $tarifScolaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarif_scolaire_show", methods={"GET"})
     */
    public function show(TarifScolaire $tarifScolaire): Response
    {
        return $this->render('admin/tarif_scolaire/show.html.twig', [
            'tarif_scolaire' => $tarifScolaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tarif_scolaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TarifScolaire $tarifScolaire, TarifScolaireRepository $tarifScolaireRepository, EntityManagerInterface $entityManager): Response
    {
        // Store original values for comparison
        $originalNiveau = $tarifScolaire->getNiveau();
        $originalAnneeScolaire = $tarifScolaire->getAnneeScolaire();
        
        $form = $this->createForm(TarifScolaireType::class, $tarifScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check for duplicate combination of niveau and anneeScolaire (excluding current entity)
            if ($originalNiveau !== $tarifScolaire->getNiveau() || 
                $originalAnneeScolaire !== $tarifScolaire->getAnneeScolaire()) {
                
                $existingTarif = $tarifScolaireRepository->findOneBy([
                    'niveau' => $tarifScolaire->getNiveau(),
                    'anneeScolaire' => $tarifScolaire->getAnneeScolaire()
                ]);

                if ($existingTarif && $existingTarif->getId() !== $tarifScolaire->getId()) {
                    $this->addFlash('error', 'Un tarif existe déjà pour ce niveau et cette année scolaire.');
                    return $this->renderForm('admin/tarif_scolaire/edit.html.twig', [
                        'tarif_scolaire' => $tarifScolaire,
                        'form' => $form,
                        'tarif_scolaires' => $tarifScolaireRepository->findAll(),
                    ]);
                }
            }

            $tarifScolaireRepository->add($tarifScolaire, true);

            return $this->redirectToRoute('app_tarif_scolaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/tarif_scolaire/edit.html.twig', [
            'tarif_scolaire' => $tarifScolaire,
            'form' => $form,
            'tarif_scolaires' => $tarifScolaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarif_scolaire_delete", methods={"POST"})
     */
    public function delete(Request $request, TarifScolaire $tarifScolaire, TarifScolaireRepository $tarifScolaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tarifScolaire->getId(), $request->request->get('_token'))) {
            $tarifScolaireRepository->remove($tarifScolaire, true);
        }

        return $this->redirectToRoute('app_tarif_scolaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
