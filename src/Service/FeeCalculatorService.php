<?php

namespace App\Service;

use App\Entity\AnneeScolaire;
use App\Entity\Classe;
use App\Entity\TarifScolaire;
use Doctrine\ORM\EntityManagerInterface;

class FeeCalculatorService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get the tuition fees for a specific class and academic year
     */
    public function getTuitionFees(Classe $classe, AnneeScolaire $anneeScolaire): ?TarifScolaire
    {
        $niveau = $classe->getNiveau();
        
        if (!$niveau) {
            return null;
        }
        
        return $this->entityManager->getRepository(TarifScolaire::class)
            ->findOneBy([
                'niveau' => $niveau,
                'anneeScolaire' => $anneeScolaire
            ]);
    }

    /**
     * Get the expected tuition amount for a specific class and academic year
     */
    public function getExpectedTuitionAmount(Classe $classe, AnneeScolaire $anneeScolaire): float
    {
        $tarifScolaire = $this->getTuitionFees($classe, $anneeScolaire);
        
        if (!$tarifScolaire) {
            return 0.0;
        }
        
        return $tarifScolaire->getMontantEcolage();
    }

    /**
     * Get the expected registration amount for a specific class and academic year
     */
    public function getExpectedRegistrationAmount(Classe $classe, AnneeScolaire $anneeScolaire): float
    {
        $tarifScolaire = $this->getTuitionFees($classe, $anneeScolaire);
        
        if (!$tarifScolaire) {
            return 0.0;
        }
        
        return $tarifScolaire->getMontantInscription();
    }

    /**
     * Get all expected fees (tuition + registration) for a specific class and academic year
     */
    public function getExpectedTotalAmount(Classe $classe, AnneeScolaire $anneeScolaire): float
    {
        return $this->getExpectedTuitionAmount($classe, $anneeScolaire) + 
               $this->getExpectedRegistrationAmount($classe, $anneeScolaire);
    }
}