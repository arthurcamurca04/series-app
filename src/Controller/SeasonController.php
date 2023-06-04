<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Series;
use App\Repository\SeasonRepository;
use App\Repository\SeriesRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SeasonController extends AbstractController
{

    private SeasonRepository $seasonRepository;
    private SeriesRepository $seriesRepository;

    private CacheInterface $cache;

    /**
     * @param SeasonRepository $seasonRepository
     * @param SeriesRepository $seriesRepository
     * @param CacheInterface $cache
     */
    public function __construct(
        SeasonRepository $seasonRepository,
        SeriesRepository $seriesRepository,
        CacheInterface $cache
    )
    {
        $this->seasonRepository = $seasonRepository;
        $this->seriesRepository = $seriesRepository;
        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/series/{id}/season', name: 'app_season')]
    public function index($id): Response
    {
        $seasons = $this->cache->get("season + {$id}",
            function (ItemInterface $item) use ($id){
                $item->expiresAfter(10);

                /** @var Season $seasons */
                $seasons = $this->seasonRepository->findBySeriesId($id);

                return $seasons;
        });

        /** @var Series $serie */
        $serie = $this->cache->get("serie + {$id}",
            function (ItemInterface $item) use ($id){
                $item->expiresAfter(10);

                /** @var Series $serie */
                $serie = $this->seriesRepository->findOneBy(['id'=>$id]);

                return $serie;
            }
        );
        $userLogged = $this->getUser();

        return $this->render('season/index.html.twig', [
            'seasons' => $seasons,
            'serie' => $serie->getName(),
            'coverImage' => $serie->getCoverImagePath() !== null ? $serie->getCoverImagePath() : null,
            'userLogged' => $userLogged
        ]);
    }
}
