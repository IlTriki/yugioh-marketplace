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
    Request $request): Response
    {
        $queryBuilder = $productRepository->getPaginatedProductsByCategoryQuery("booster");
        $boosters = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('boosters/boosters.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'boosters' => $boosters,
        ]);
    }
}