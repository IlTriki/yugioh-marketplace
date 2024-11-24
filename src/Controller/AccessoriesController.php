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
    Request $request): Response
    {
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryQuery("accessory");
        $accessories = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('accessories/accessories.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'accessories' => $accessories,
        ]);
    }
}