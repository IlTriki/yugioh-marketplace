<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Enum\ProductStatus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class ProductController extends AbstractController
{
    public function __construct(
        private HubInterface $hub,
        protected string $mercurePublicUrl
    ) {}

    #[Route('/product/{id}', name: 'product_detail')]
    public function show(Product $product): Response
    {
        $topic = sprintf('product/%d', $product->getId());

        return $this->render('products/detail.html.twig', [
            'product' => $product,
            'mercure_url' => $this->mercurePublicUrl,
            'topic' => $topic,
            'productStatuses' => ProductStatus::cases(),
        ]);
    }
}
