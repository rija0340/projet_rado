<?php

namespace App\Repository;

use App\Entity\TarifScolaire;
use App\Entity\Niveau;
use App\Entity\AnneeScolaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TarifScolaire>
 *
 * @method TarifScolaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method TarifScolaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method TarifScolaire[]    findAll()
 * @method TarifScolaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TarifScolaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TarifScolaire::class);
    }

    public function add(TarifScolaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TarifScolaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Find a tariff by niveau and anneeScolaire combination
     *
     * @param \App\Entity\Niveau $niveau
     * @param \App\Entity\AnneeScolaire $anneeScolaire
     * @return TarifScolaire|null
     */
    public function findOneByNiveauAndAnneeScolaire($niveau, $anneeScolaire): ?TarifScolaire
    {
        return $this->findOneBy([
            'niveau' => $niveau,
            'anneeScolaire' => $anneeScolaire
        ]);
    }

    /**
     * Find all niveaux that have tariffs for a given anneeScolaire
     *
     * @param AnneeScolaire $anneeScolaire
     * @param Niveau|null $exclude Optional niveau to exclude from results
     * @return array Array of Niveau entities
     */
    public function findUsedNiveauxForAnneeScolaire(AnneeScolaire $anneeScolaire, ?Niveau $exclude = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('n')
            ->join('t.niveau', 'n')
            ->where('t.anneeScolaire = :anneeScolaire')
            ->setParameter('anneeScolaire', $anneeScolaire);

        if ($exclude) {
            $qb->andWhere('t.niveau != :exclude')
               ->setParameter('exclude', $exclude);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all anneeScolaires that have tariffs for a given niveau
     *
     * @param Niveau $niveau
     * @param AnneeScolaire|null $exclude Optional anneeScolaire to exclude from results
     * @return array Array of AnneeScolaire entities
     */
    public function findUsedAnneeScolairesForNiveau(Niveau $niveau, ?AnneeScolaire $exclude = null): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('a')
            ->join('t.anneeScolaire', 'a')
            ->where('t.niveau = :niveau')
            ->setParameter('niveau', $niveau);

        if ($exclude) {
            $qb->andWhere('t.anneeScolaire != :exclude')
               ->setParameter('exclude', $exclude);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return TarifScolaire[] Returns an array of TarifScolaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TarifScolaire
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
