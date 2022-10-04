<?php

namespace App\Controller;

use App\Entity\Icon;
use App\Form\IconType;
use App\Repository\CategoryRepository;
use App\Repository\IconRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IconController extends AbstractController
{
    private const MAX_ICONS_IN_PAGE = 32;

    #[Route('/icons', name: 'app_icon_index', methods: ['GET'])]
    public function index(IconRepository $iconRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $icons = $iconRepository->findBy([], ['createdAt' => 'DESC']);

        $currentPage = $request->query->get('page', 1);

        $iconsPaginated = $paginator->paginate(
            $icons,
            $currentPage, self::MAX_ICONS_IN_PAGE
        );

        return $this->render('icon/index.html.twig', [
            'title' => 'all',
            'icons' => $iconsPaginated,
        ]);
    }

    #[Route('/icons/category/{category}', name: 'app_icon_category_filter', methods: ['GET'])]
    public function categoryFilter(
        CategoryRepository $categoryRepository,
        Request $request,
        PaginatorInterface $paginator,
        $category
    ): Response
    {
        $icons = $categoryRepository->findOneBy(['name' => $category]);

        $currentPage = $request->query->get('page', 1);

        $iconsPaginated = $paginator->paginate(
            $icons->getIcons()->getValues(),
            $currentPage, self::MAX_ICONS_IN_PAGE
        );

        return $this->render('icon/index.html.twig', [
            'title' => $category,
            'icons' => $iconsPaginated,
        ]);
    }

    #[Route('/icon/new', name: 'app_icon_new', methods: ['GET', 'POST'])]
    public function new(Request $request, IconRepository $iconRepository): Response
    {
        $icon = new Icon();
        $form = $this->createForm(IconType::class, $icon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $iconRepository->add($icon, true);

            return $this->redirectToRoute('app_icon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('icon/new.html.twig', [
            'icon' => $icon,
            'form' => $form,
        ]);
    }

    #[Route('/icon/{id}', name: 'app_icon_show', methods: ['GET'])]
    public function show(Icon $icon): Response
    {
        return $this->render('icon/show.html.twig', [
            'icon' => $icon,
        ]);
    }

    #[Route('/icon/{id}/edit', name: 'app_icon_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Icon $icon, IconRepository $iconRepository): Response
    {
        $form = $this->createForm(IconType::class, $icon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $iconRepository->add($icon, true);

            return $this->redirectToRoute('app_icon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('icon/edit.html.twig', [
            'icon' => $icon,
            'form' => $form,
        ]);
    }

    #[Route('/icon/{id}', name: 'app_icon_delete', methods: ['POST'])]
    public function delete(Request $request, Icon $icon, IconRepository $iconRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$icon->getId(), $request->request->get('_token'))) {
            $iconRepository->remove($icon, true);
        }

        return $this->redirectToRoute('app_icon_index', [], Response::HTTP_SEE_OTHER);
    }
}
