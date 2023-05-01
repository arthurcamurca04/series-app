<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Series;
use App\Repository\SeasonRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeasonController extends AbstractController
{

    private SeasonRepository $seasonRepository;
    private SeriesRepository $seriesRepository;

    /**
     * @param SeasonRepository $seasonRepository
     */
    public function __construct(
        SeasonRepository $seasonRepository,
        SeriesRepository $seriesRepository
    )
    {
        $this->seasonRepository = $seasonRepository;
        $this->seriesRepository = $seriesRepository;
    }

    #[Route('/series/{id}/season', name: 'app_season')]
    public function index($id): Response
    {
        /** @var Season $seasons */
        $seasons = $this->seasonRepository->findBySeriesId($id);

        /** @var Series $serie */
        $serie = $this->seriesRepository->findOneBy(['id'=>$id]);

        return $this->render('season/index.html.twig', [
            'seasons' => $seasons,
            'serie' => $serie->getName()
        ]);
    }
}
