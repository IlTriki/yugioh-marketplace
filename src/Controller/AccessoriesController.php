<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccessoriesController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/accessories', name: 'accessories')]
    public function index(): Response
    {
        return $this->render('accessories/accessories.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
        ]);
    }
}