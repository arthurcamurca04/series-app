<?php

namespace App\Repository;

use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    public function add(Series $series, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->persist($series);

        if ($flush){
            $em->flush();
        }
    }

    public function delete(Series $series, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->remove($series);

        if ($flush){
            $em->flush();
        }
    }
}