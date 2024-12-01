<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoostersController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/boosters', name: 'boosters')]
    public function index(ProductRepository $productRepository,
    PaginatorInterface $paginator,
    Request $request,
    ): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryQuery("booster");
        $boosters = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );
        
        return $this->render('boosters/boosters.html.twig',
        [
                'mercure_url' => $this->mercurePublicUrl,
                'products' => $boosters,
                'searchPath' => 'boosters_search',
                'errorMessage' => "Aucun booster trouvé",
            ]);
    }

    #[Route('/boosters/search', name: 'boosters_search')]
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

        $queryBuilder = $productRepository->getFilteredProductsQuery("booster", $filters);
        
        $boosters = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('boosters/boosters.html.twig',
        [
                'mercure_url' => $this->mercurePublicUrl,
                'products' => $boosters,
                'searchTerm' => $filters['name'],
                'filters' => $filters,
                'searchPath' => 'boosters_search',
                'errorMessage' => "Aucun booster trouvé",
            ]);
    }
}