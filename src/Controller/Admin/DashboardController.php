<?php

namespace App\Controller\Admin;

use App\Repository\NiveauRepository;
use App\Repository\ClasseRepository;
use App\Repository\AnneeScolaireRepository;
use App\Repository\TarifScolaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/dashboard")
     */
class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function index(
        NiveauRepository $niveauRepository,
        ClasseRepository $classeRepository,
        AnneeScolaireRepository $anneeScolaireRepository,
        TarifScolaireRepository $tarifScolaireRepository
    ): Response {
        $niveaux = $niveauRepository->findAll();
        $classes = $classeRepository->findAll();
        $anneesScolaires = $anneeScolaireRepository->findAll();
        $tarifScolaires = $tarifScolaireRepository->findAll();
        $tarifScolaires = $tarifScolaireRepository->findAll();

        return $this->render('admin/dashboard/index.html.twig', [
            'niveaux' => $niveaux,
            'classes' => $classes,
            'anneesScolaires' => $anneesScolaires,
            'tarifScolaires' => $tarifScolaires,
        ]);
    }
}