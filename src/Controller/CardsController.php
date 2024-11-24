<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CardsController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/cards', name: 'cards')]
    public function index(ProductRepository $productRepository,
    PaginatorInterface $paginator,
    Request $request): Response
    {
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryQuery("card");
        $cards = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('cards/cards.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'cards' => $cards,
            'unfiltered_cards' => $cards
        ]);
    }

    #[Route('/cards/search', name: 'cards_search')]
    public function search(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $searchTerm = $request->query->get('name', null);
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryAndNameQuery("card", $searchTerm);
        $cards = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('cards/cards.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'cards' => $cards,
            'searchTerm' => $searchTerm,
        ]);
    }
}