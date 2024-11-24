<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BrowseController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {}

    #[Route('/browse', name: 'browse')]
    public function index(ProductRepository $productRepository): Response
    {
        $cards = $productRepository->getPaginatedProductsByCategoryWithLimitQuery("card", 8);
        $boosters = $productRepository->getPaginatedProductsByCategoryWithLimitQuery("booster", 8);
        $accessories = $productRepository->getPaginatedProductsByCategoryWithLimitQuery("accessory", 8);

        return $this->render('browse/browse.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'cards' => $cards,
            'boosters' => $boosters,
            'accessories' => $accessories,
        ]);
    }
}
