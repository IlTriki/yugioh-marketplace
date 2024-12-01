<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessoriesController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/accessories', name: 'accessories')]
    public function index(ProductRepository $productRepository,
    PaginatorInterface $paginator,
    Request $request,
    ): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryQuery("accessory");
        $accessories = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );
        
        return $this->render('accessories/accessories.html.twig',
        [
                'mercure_url' => $this->mercurePublicUrl,
                'products' => $accessories,
                'searchPath' => 'accessories_search',
                'errorMessage' => "Aucun accessoire trouvé",
            ]);
    }

    #[Route('/accessories/search', name: 'accessories_search')]
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

        $queryBuilder = $productRepository->getFilteredProductsQuery("accessory", $filters);
        
        $accessories = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('accessories/accessories.html.twig',
        [
                'mercure_url' => $this->mercurePublicUrl,
                'products' => $accessories,
                'searchTerm' => $filters['name'],
                'filters' => $filters,
                'searchPath' => 'accessories_search',
                'errorMessage' => "Aucun accessoire trouvé",
            ]);
    }
}