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
    Request $request,
    ): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryQuery("card");
        $cards = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('cards/cards.html.twig',
        [
            'mercure_url' => $this->mercurePublicUrl,
            'products' => $cards,
            'searchPath' => 'cards_search',
            'errorMessage' => "Aucune carte trouvée",
        ]);
    }

    #[Route('/cards/search', name: 'cards_search')]
    public function search(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request,
        
    ): Response {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        $filters = [
            'name' => $request->query->get('name'),
            'inStock' => $request->query->getBoolean('inStock'),
            'preOrder' => $request->query->getBoolean('preOrder'),
            'outOfStock' => $request->query->getBoolean('outOfStock'),
            'priceFrom' => $request->query->get('priceFrom'),
            'priceTo' => $request->query->get('priceTo'),
            'sortBy' => $request->query->get('sortBy'),
        ];

        $queryBuilder = $productRepository->getFilteredProductsQuery("card", $filters);
        
        $cards = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('cards/cards.html.twig',
            [
                'mercure_url' => $this->mercurePublicUrl,
                'products' => $cards,
                'searchTerm' => $filters['name'],
                'filters' => $filters,
                'searchPath' => 'cards_search',
                'errorMessage' => "Aucune carte trouvée",
            ]);
    }
}