<?php

namespace App\Repository;

use App\Entity\Season;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Season>
 *
 * @method Season|null find($id, $lockMode = null, $lockVersion = null)
 * @method Season|null findOneBy(array $criteria, array $orderBy = null)
 * @method Season[]    findAll()
 * @method Season[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Season::class);
    }

    public function save(Season $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Season $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Season[] Returns an array of Season objects
     */
    public function findBySeriesId($id): array
    {
        return $this->createQueryBuilder('season')
            ->innerJoin(Series::class, 'serie',
                Join::WITH, 'serie.id = season.series')
            ->where('serie.id = :id')
            ->setParameter('id', $id)
            ->orderBy('serie.id', 'ASC')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult()
        ;
    }
}
