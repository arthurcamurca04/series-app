<?php

namespace App\Repository;

use App\Entity\Season;
use App\Entity\Series;
use App\SeriesInputDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

class SeriesRepository extends ServiceEntityRepository
{
    private SeasonRepository $seasonRepository;

    public function __construct(ManagerRegistry $registry, SeasonRepository $seasonRepository)
    {
        parent::__construct($registry, Series::class);
        $this->seasonRepository = $seasonRepository;
    }

    /**
     */
    public function add(SeriesInputDto $seriesInputDto, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $serie = new Series($seriesInputDto->getName());
        $em->persist($serie);
        if ($flush){
            $em->flush();
        }

        try {
            $this->addSeason($serie->getId(), $seriesInputDto->getSeasonsQuantity());

            /** @var Season $seasons */
            $seasons = $this->seasonRepository->findBy(['series' => $serie]);

            /** @var Season $season */
            foreach($seasons as $season){
                $this->addEpisodePerSeason($season->getId(), $seriesInputDto->getEpisodesQuantity());
            }
        }catch (\Exception $e){
            $this->delete($serie,true);
            throw new \InvalidArgumentException($e->getMessage());
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

    /**
     * @throws Exception
     */
    private function addEpisodePerSeason(int $season_id, int $epQuantity)
    {
        $db = $this->getEntityManager()->getConnection();
        for($j = 1; $j <= $epQuantity; $j++){
            $query = "INSERT INTO Episode (season_id, number) VALUES (:season_id, :number)";
            $stmt = $db->prepare($query);
            $stmt->bindValue('season_id', $season_id);
            $stmt->bindValue('number', $j);

            $stmt->executeStatement();
        }
    }

    /**
     * @throws Exception
     */
    private function addSeason(int $series_id, int $seasonQuantity)
    {
        $db = $this->getEntityManager()->getConnection();

        $query = "INSERT INTO Season (series_id,number) VALUES (:series_id, :number)";
        $stmt = $db->prepare($query);
        $stmt->bindValue('series_id', $series_id);
        for($i = 1; $i <= $seasonQuantity; $i++){
            $stmt->bindValue('number', $i);
            $stmt->executeStatement();
        }
    }
}