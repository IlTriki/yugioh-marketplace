<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(protected string $mercurePublicUrl) {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        return $this->render('home/home.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
        ]);
    }
}