<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EpisodeController extends AbstractController
{
    private EpisodeRepository $episodeRepository;

    private SeasonRepository $seasonRepository;

    public function __construct(
        EpisodeRepository $episodeRepository,
        SeasonRepository $seasonRepository
    )
    {
        $this->episodeRepository = $episodeRepository;
        $this->seasonRepository = $seasonRepository;
    }

    #[Route('season/{id}/episodes',
        name: 'app_episode',
        requirements: ['season' => '\+d'],
        methods: ['GET']
    )
    ]
    public function index(int $id): Response
    {
        /** @var Season $season */
        $season = $this->seasonRepository->find($id);
        $episodes = $this->episodeRepository->findBy(['season'=>$season], ['id'=>'ASC']);

        return $this->render('episode/index.html.twig', [
            'episodes' => $episodes,
            'seasonId' => $season->getNumber()
        ]);
    }

    #[Route('season/{id}/episodes', name: 'app_episode_watched', methods: ['POST'])]
    public function watched(int $id, Request $request): RedirectResponse
    {
        $watchedEpisodesInput = $request->request->all('watched');

        /** @var Season $season */
        $season = $this->seasonRepository->find($id);

        /** @var Episode $episodes */
        $episodes = $this->episodeRepository->findBy(['season'=>$season]);

        /** @var Episode $episode */
        foreach ($episodes as $episode) {
            $watched = in_array($episode->getId(), $watchedEpisodesInput);

            if ($watched) {
                $episode->setWatched(true);
                $this->episodeRepository->save($episode, true);
            }
        }
        $this->addFlash('success', 'Episódios marcados foram atualizados.');

        return new RedirectResponse("/season/{$season->getId()}/episodes");
    }
}
