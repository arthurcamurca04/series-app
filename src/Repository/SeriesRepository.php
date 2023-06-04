<?php

namespace App\Repository;

use App\DTO\SeriesInputDto;
use App\Entity\Season;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SeriesRepository extends ServiceEntityRepository
{
    private SeasonRepository $seasonRepository;
    private EpisodesRepository $episodesRepository;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        ManagerRegistry $registry,
        SeasonRepository $seasonRepository,
        EpisodesRepository $episodesRepository,
        ParameterBagInterface $parameterBag,
    )
    {
        parent::__construct($registry, Series::class);
        $this->seasonRepository = $seasonRepository;
        $this->episodesRepository = $episodesRepository;
        $this->parameterBag = $parameterBag;
    }

    /**
     */
    public function add(SeriesInputDto $seriesInputDto, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $serie = new Series($seriesInputDto->getName());

        if ($seriesInputDto->getCoverImage() !== null) {
            $serie->setCoverImagePath($seriesInputDto->getCoverImage());
        }

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

        $seasons = $this->seasonRepository->findBy(["series" => $series->getId()]);

        foreach ($seasons as $season) {
            $episodes = $this->episodesRepository->findBy(["season" => $season->getId()]);

            foreach ($episodes as $episode) {
                $this->episodesRepository->remove($episode);
            }

            $this->seasonRepository->remove($season);
        }

        $em->remove($series);

        if ($flush){
            $em->flush();
        }

        if ($series->getCoverImagePath()){
            unlink(
                $this->parameterBag->get('cover_image_directory') .
                DIRECTORY_SEPARATOR .
                $series->getCoverImagePath());
        }
    }

    /**
     * @throws Exception
     */
    private function addEpisodePerSeason(int $season_id, int $epQuantity): void
    {
        $db = $this->getEntityManager()->getConnection();
        for($j = 1; $j <= $epQuantity; $j++){
            $query = "INSERT INTO Episode (season_id, number, watched) VALUES (:season_id, :number, :watched)";
            $stmt = $db->prepare($query);
            $stmt->bindValue('season_id', $season_id);
            $stmt->bindValue('number', $j);
            $stmt->bindValue('watched', 0);

            $stmt->executeStatement();
        }
    }

    /**
     * @throws Exception
     */
    private function addSeason(int $series_id, int $seasonQuantity): void
    {
        $db = $this->getEntityManager()->getConnection();

        for($i = 1; $i <= $seasonQuantity; $i++){
            $query = "INSERT INTO Season (series_id,number) VALUES (:series_id, :number)";
            $stmt = $db->prepare($query);
            $stmt->bindValue('series_id', $series_id);
            $stmt->bindValue('number', $i);
            $stmt->executeStatement();
        }
    }
}