<?php

namespace App\Controller;

use App\Entity\AnneeScolaire;
use App\Form\AnneeScolaireType;
use App\Repository\AnneeScolaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/annee/scolaire")
 */
class AnneeScolaireController extends AbstractController
{
    /**
     * @Route("/", name="app_annee_scolaire_index", methods={"GET"})
     */
    public function index(AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        return $this->render('admin/annee_scolaire/index.html.twig', [
            'annee_scolaires' => $anneeScolaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_annee_scolaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $anneeScolaire = new AnneeScolaire();
        $form = $this->createForm(AnneeScolaireType::class, $anneeScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $anneeScolaireRepository->add($anneeScolaire, true);

            return $this->redirectToRoute('app_annee_scolaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/annee_scolaire/new.html.twig', [
            'annee_scolaire' => $anneeScolaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_annee_scolaire_show", methods={"GET"})
     */
    public function show(AnneeScolaire $anneeScolaire): Response
    {
        return $this->render('admin/annee_scolaire/show.html.twig', [
            'annee_scolaire' => $anneeScolaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_annee_scolaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, AnneeScolaire $anneeScolaire, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        $form = $this->createForm(AnneeScolaireType::class, $anneeScolaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $anneeScolaireRepository->add($anneeScolaire, true);

            return $this->redirectToRoute('app_annee_scolaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/annee_scolaire/edit.html.twig', [
            'annee_scolaire' => $anneeScolaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_annee_scolaire_delete", methods={"POST"})
     */
    public function delete(Request $request, AnneeScolaire $anneeScolaire, AnneeScolaireRepository $anneeScolaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$anneeScolaire->getId(), $request->request->get('_token'))) {
            $anneeScolaireRepository->remove($anneeScolaire, true);
        }

        return $this->redirectToRoute('app_annee_scolaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
