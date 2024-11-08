<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BrowseController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/browse', name: 'browse')]
    public function index(): Response
    {
        return $this->render('browse/browse.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
        ]);
    }
}