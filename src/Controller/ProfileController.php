<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    #[Route('', name: 'profile')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'profile_edit')]
    public function edit(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully');
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/orders', name: 'profile_orders')]
    public function orders(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('profile/orders.html.twig', [
            'orders' => $this->getUser()->getOrders(),
        ]);
    }

    #[Route('/addresses', name: 'profile_addresses')]
    public function addresses(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('profile/addresses.html.twig', [
            'addresses' => $this->getUser()->getAddresses(),
        ]);
    }
} 