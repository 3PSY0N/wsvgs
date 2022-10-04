<?php

namespace App\Controller;

use App\Entity\Icon;
use App\Repository\IconRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MeiliSearch\Bundle\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        IconRepository $iconRepository,
        SearchService $searchService,
        EntityManagerInterface $entityManager
    ): Response
    {
        $hits = null;
        $searchTime = null;

        $icons = $iconRepository->findBy([], ['createdAt' => 'DESC']);
        $currentPage = $request->query->get('page', 1);
        $iconsPaginated = $paginator->paginate($icons, $currentPage, 32);

        $searchQuery = $request->query->get('search');

        if ($searchQuery) {
            $time_start = microtime(true);

            $hits       = $searchService->search($entityManager, Icon::class, $searchQuery);

            $searchTime = (microtime(true) - $time_start) * 1000;
            $iconsPaginated = null;
        }

        return $this->render('home/index.html.twig', [
            'icons'       => $iconsPaginated,
            'hits'        => $hits,
            'searchQuery' => $searchQuery,
            'searchTime'  => (int)$searchTime,
        ]);
    }
}




