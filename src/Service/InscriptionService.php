<?php

namespace App\Service;

use App\Entity\AnneeScolaire;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Inscription;
use App\Entity\Paiement;
use App\Entity\TarifScolaire;
use App\Repository\AnneeScolaireRepository;
use App\Repository\ClasseRepository;
use App\Repository\EtudiantRepository;
use App\Repository\InscriptionRepository;
use App\Repository\TarifScolaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class InscriptionService
{
    private $entityManager;
    private $etudiantRepository;
    private $classeRepository;
    private $inscriptionRepository;
    private $anneeScolaireRepository;
    private $tarifScolaireRepository;
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        EtudiantRepository $etudiantRepository,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        AnneeScolaireRepository $anneeScolaireRepository,
        TarifScolaireRepository $tarifScolaireRepository,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->etudiantRepository = $etudiantRepository;
        $this->classeRepository = $classeRepository;
        $this->inscriptionRepository = $inscriptionRepository;
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        $this->tarifScolaireRepository = $tarifScolaireRepository;
        $this->logger = $logger;
    }

    /**
     * Create a new inscription for a student (existing or new)
     *
     * @param array $studentData Student data (can include id for existing student)
     * @param int $classeId Class ID
     * @param array $paymentData Payment information
     * @return Inscription
     * @throws \Exception
     */
    public function createInscription(array $studentData, int $classeId, array $paymentData = []): Inscription
    {
        // Start transaction
        $this->entityManager->beginTransaction();
        
        try {
            // Get or create student
            if (isset($studentData['id']) && !empty($studentData['id'])) {
                $etudiant = $this->etudiantRepository->find($studentData['id']);
                if (!$etudiant) {
                    throw new \Exception("Student not found with ID: " . $studentData['id']);
                }
            } else {
                $etudiant = $this->createStudent($studentData);
            }

            // Get class and academic year
            $classe = $this->classeRepository->find($classeId);
            if (!$classe) {
                throw new \Exception("Class not found with ID: " . $classeId);
            }

            $anneeScolaire = $this->getCurrentAcademicYear();
            if (!$anneeScolaire) {
                throw new \Exception("No active academic year found");
            }

            // Check for duplicate inscription
            $existingInscription = $this->inscriptionRepository->findOneBy([
                'etudiant' => $etudiant,
                'anneeScolaire' => $anneeScolaire
            ]);

            if ($existingInscription) {
                throw new \Exception("Student is already enrolled for this academic year");
            }

            // Create inscription
            $inscription = new Inscription();
            $inscription->setEtudiant($etudiant);
            $inscription->setClasse($classe);
            $inscription->setAnneeScolaire($anneeScolaire);
            $inscription->setDateInscription(new \DateTime());

            // Process payment if provided
            if (!empty($paymentData)) {
                $this->processPayment($inscription, $paymentData);
            }

            // Save inscription
            $this->entityManager->persist($inscription);
            $this->entityManager->flush();

            // Commit transaction
            $this->entityManager->commit();

            $this->logger->info('New inscription created', [
                'inscription_id' => $inscription->getId(),
                'student_id' => $etudiant->getId(),
                'class_id' => $classe->getId()
            ]);

            return $inscription;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error creating inscription', [
                'error' => $e->getMessage(),
                'student_data' => $studentData,
                'class_id' => $classeId
            ]);
            throw $e;
        }
    }

    /**
     * Create a new student
     *
     * @param array $studentData
     * @return Etudiant
     */
    private function createStudent(array $studentData): Etudiant
    {
        $etudiant = new Etudiant();
        $etudiant->setNom($studentData['nom'] ?? '');
        $etudiant->setPrenom($studentData['prenom'] ?? '');
        $etudiant->setSexe($studentData['sexe'] ?? '');
        
        if (!empty($studentData['date_naissance'])) {
            $etudiant->setDateNaissance(new \DateTime($studentData['date_naissance']));
        }
        
        if (!empty($studentData['telephone'])) {
            $etudiant->setTelephone($studentData['telephone']);
        }

        $this->entityManager->persist($etudiant);
        $this->entityManager->flush();

        return $etudiant;
    }

    /**
     * Get the current active academic year
     *
     * @return AnneeScolaire|null
     */
    private function getCurrentAcademicYear(): ?AnneeScolaire
    {
        return $this->anneeScolaireRepository->findOneBy(['active' => true]);
    }

    /**
     * Process payment for an inscription
     *
     * @param Inscription $inscription
     * @param array $paymentData
     * @return Paiement
     * @throws \Exception
     */
    private function processPayment(Inscription $inscription, array $paymentData): Paiement
    {
        $classe = $inscription->getClasse();
        $niveau = $classe->getNiveau();
        $anneeScolaire = $inscription->getAnneeScolaire();

        // Get tariff for this level and academic year
        $tarifScolaire = $this->tarifScolaireRepository->findOneBy([
            'niveau' => $niveau,
            'anneeScolaire' => $anneeScolaire
        ]);

        if (!$tarifScolaire) {
            throw new \Exception("No tariff found for level " . $niveau->getNom() . " and academic year " . $anneeScolaire->getLibelle());
        }

        $paiement = new Paiement();
        $paiement->setInsciption($inscription);
        $paiement->setTarifScolaire($tarifScolaire);
        $paiement->setMontant($paymentData['montant'] ?? 0);
        $paiement->setDatePaiement(new \DateTime());
        $paiement->setModePaiement($paymentData['mode_paiement'] ?? 'cash');
        $paiement->setReference($paymentData['reference'] ?? null);
        $paiement->setStatut('confirmed'); // Simplified for this example
        $paiement->setType($paymentData['type'] ?? 'inscription'); // inscription or ecolage
        $paiement->setDescription($paymentData['description'] ?? '');

        $this->entityManager->persist($paiement);

        return $paiement;
    }

    /**
     * Get expected total amount for an inscription based on class level
     *
     * @param Inscription $inscription
     * @return float
     */
    public function getExpectedTotal(Inscription $inscription): float
    {
        $classe = $inscription->getClasse();
        $niveau = $classe->getNiveau();
        $anneeScolaire = $inscription->getAnneeScolaire();

        $tarifScolaire = $this->tarifScolaireRepository->findOneBy([
            'niveau' => $niveau,
            'anneeScolaire' => $anneeScolaire
        ]);

        if (!$tarifScolaire) {
            return 0;
        }

        // Return sum of inscription fee and school fees
        return $tarifScolaire->getMontantInscription() + $tarifScolaire->getMontantEcolage() + ($tarifScolaire->getAutresFrais() ?? 0);
    }

    /**
     * Get all inscriptions with optional filters
     *
     * @param array $filters
     * @return Inscription[]
     */
    public function getInscriptions(array $filters = []): array
    {
        $qb = $this->inscriptionRepository->createQueryBuilder('i')
            ->leftJoin('i.etudiant', 'e')
            ->leftJoin('i.classe', 'c')
            ->leftJoin('i.anneeScolaire', 'a')
            ->orderBy('i.date_inscription', 'DESC');

        if (!empty($filters['student_name'])) {
            $qb->andWhere('e.nom LIKE :student_name OR e.prenom LIKE :student_name')
                ->setParameter('student_name', '%' . $filters['student_name'] . '%');
        }

        if (!empty($filters['classe_id'])) {
            $qb->andWhere('i.classe = :classe_id')
                ->setParameter('classe_id', $filters['classe_id']);
        }

        if (!empty($filters['annee_scolaire_id'])) {
            $qb->andWhere('i.anneeScolaire = :annee_scolaire_id')
                ->setParameter('annee_scolaire_id', $filters['annee_scolaire_id']);
        }

        if (!empty($filters['statut'])) {
            $qb->andWhere('i.statut = :statut')
                ->setParameter('statut', $filters['statut']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Confirm an inscription
     *
     * @param Inscription $inscription
     * @return Inscription
     */
    public function confirmInscription(Inscription $inscription): Inscription
    {
        $inscription->setStatut(Inscription::STATUS_CONFIRMED);
        $this->entityManager->flush();

        $this->logger->info('Inscription confirmed', [
            'inscription_id' => $inscription->getId()
        ]);

        return $inscription;
    }

    /**
     * Cancel an inscription
     *
     * @param Inscription $inscription
     * @param string $reason
     * @return Inscription
     */
    public function cancelInscription(Inscription $inscription, string $reason = ''): Inscription
    {
        $inscription->setStatut(Inscription::STATUS_CANCELLED);
        if (!empty($reason)) {
            $inscription->setNotes(($inscription->getNotes() ?? '') . "\nCancelled: " . $reason);
        }
        $this->entityManager->flush();

        $this->logger->info('Inscription cancelled', [
            'inscription_id' => $inscription->getId(),
            'reason' => $reason
        ]);

        return $inscription;
    }
}