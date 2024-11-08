<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoostersController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/boosters', name: 'boosters')]
    public function index(): Response
    {
        return $this->render('boosters/boosters.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
        ]);
    }
}