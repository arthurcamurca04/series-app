<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{
    private SeriesRepository $seriesRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SeriesRepository $seriesRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->seriesRepository = $seriesRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/series', name: 'series', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();
        $session = $request->getSession();
        $session->remove('success');
        $session->remove('removed');
        return $this->render('series/series.html.twig', [
            'series' => $seriesList
        ]);
    }

    #[Route('/series/create', name: 'series-create-form', methods: ['GET'])]
    public function addSeriesForm(): Response
    {
        $seriesForm = $this->createForm(SeriesType::class, new Series(''));

        return $this->render('series/form.html.twig', [
            'seriesForm' => $seriesForm
        ]);
    }

    #[Route('/series/create', name: 'series-create', methods: ['POST'])]
    public function addSeries(Request $request): RedirectResponse
    {
        $series = new Series();
        $filledSerie = $this->createForm(SeriesType::class, $series)->handleRequest($request);

        if($filledSerie->isSubmitted() && $filledSerie->isValid()){
            $this->seriesRepository->add($series, true);
            $this->addFlash('success', "Série \"{$series->getName()}\" adicionada com sucesso");
            return new RedirectResponse('/series', 302);
        }
        $this->addFlash('danger', "Erro ao adicionar série.");
        return new RedirectResponse('/series', 400);
    }

    #[Route('/series/delete/{id}',
        name: 'series-delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE'])]
    public function deleteSeries(int $id, Request $request): RedirectResponse
    {
        $series = $this->entityManager->getPartialReference(Series::class, $id);
        $this->seriesRepository->delete($series, true);

        $this->addFlash('danger', 'Série removida.');

        return new RedirectResponse('/series', 302);
    }

    #[Route('/series/edit/{id}', name: 'series-update-form', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function editSeriesForm(int $id): Response
    {
        $series = $this->seriesRepository->find($id);
        $seriesForm = $this->createForm(SeriesType::class, new Series($series->getName()));
        return $this->render('series/form.html.twig', [
            'seriesForm' => $seriesForm
        ]);
    }

    #[Route('/series/edit/{id}', name: 'series-update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function editSeries(int $id, Request $request): RedirectResponse
    {
        $series = $this->seriesRepository->find($id);
        $filledSeries = $this->createForm(SeriesType::class, $series)
            ->handleRequest($request);

        if($filledSeries->isSubmitted() && $filledSeries->isValid()){
            $series->setName($series->getName());
            $this->seriesRepository->add($series, true);
            $this->addFlash('info', "Série \"{$series->getName()}\" atualizada");

            return new RedirectResponse('/series', 302);
        }

        $this->addFlash('danger', "Erro ao editar série.");
        return new RedirectResponse('/series', 400);
    }

}