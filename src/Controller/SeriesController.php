<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use App\DTO\SeriesInputDto;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SeriesController extends AbstractController
{
    private SeriesRepository $seriesRepository;
    private EntityManagerInterface $entityManager;
    private SluggerInterface $slugger;
    private TranslatorInterface $translator;

    public function __construct(
        SeriesRepository $seriesRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        TranslatorInterface $translator
    )
    {
        $this->seriesRepository = $seriesRepository;
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
        $this->translator = $translator;
    }

    #[Route('/series', name: 'series', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();
        $userLogged = $this->getUser();

        return $this->render('series/series.html.twig', [
            'series' => $seriesList,
            'userLogged' => $userLogged
        ]);
    }

    #[Route('/series/create', name: 'series-create-form', methods: ['GET'])]
    public function addSeriesForm(): Response
    {
        $seriesForm = $this->createForm(SeriesType::class, new SeriesInputDto());
        $userLogged = $this->getUser();

        return $this->render('series/form.html.twig', [
            'seriesForm' => $seriesForm,
            'userLogged' => $userLogged
        ]);
    }

    #[Route('/series/create', name: 'series-create', methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $input = new SeriesInputDto();
        $filledSeries = $this->createForm(SeriesType::class, $input)->handleRequest($request);

        if (!$filledSeries->isValid()) {
            return $this->render('series/form.html.twig', [
                'seriesForm' => $filledSeries,
                'userLogged' => $this->getUser()
            ]);
        }

        /** @var UploadedFile $uploadedCoverImage */
        $uploadedCoverImage = $filledSeries->get('coverImage')->getData();

        if ($uploadedCoverImage) {
            $originalFileName = pathinfo($uploadedCoverImage->getClientOriginalName(), PATHINFO_FILENAME);

            $safeFileName = $this->slugger->slug($originalFileName);
            $newFileName = $safeFileName . '-' . uniqid() . '.' . $uploadedCoverImage->guessExtension();

            $uploadedCoverImage->move(
                $this->getParameter('cover_image_directory'),
                $newFileName
            );

            $input->setCoverImage($newFileName);
        };

        if($filledSeries->isSubmitted()){
            $this->seriesRepository->add($input, true);
            $this->addFlash('success', "Série \"{$input->getName()}\" adicionada com sucesso");
            return $this->redirectToRoute('series');
        }

        return $this->render('series/form.html.twig', [
            'seriesForm' => $filledSeries,
            'userLogged' => $this->getUser()
        ]);
    }

    /**
     * @throws ORMException
     */
    #[Route('/series/delete/{id}',
        name: 'series-delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE'])]
    public function deleteSeries(int $id): RedirectResponse
    {
        $series = $this->entityManager->getReference(Series::class, $id);
        $this->seriesRepository->delete($series, true);

        $this->addFlash('danger', $this->translator->trans('series.delete'));

        return $this->redirectToRoute('series');
    }

    #[Route('/series/edit/{id}',
        name: 'series-update-form',
        requirements: ['id' => '\d+'],
        methods: ['GET'])]
    public function editSeriesForm(int $id): Response
    {
        $series = $this->seriesRepository->find($id);
        $seriesForm = $this->createForm(
            SeriesType::class,
            new Series($series->getName()),
            ['is_edit' => true]);

        return $this->redirectToRoute('series');
    }

    #[Route('/series/edit/{id}',
        name: 'series-update',
        requirements: ['id' => '\d+'],
        methods: ['POST'])]
    public function editSeries(int $id, Request $request): RedirectResponse
    {
        $series = $this->seriesRepository->find($id);
        $filledSeries = $this->createForm(SeriesType::class, $series)
            ->handleRequest($request);

        if($filledSeries->isSubmitted() && $filledSeries->isValid()){
            $series->setName($series->getName());
            $this->seriesRepository->add($series, true);
            $this->addFlash('info', "Série \"{$series->getName()}\" atualizada");

            return $this->redirectToRoute('series');
        }

        $this->addFlash('danger', "Erro ao editar série.");

        return $this->redirectToRoute('series');
    }

}