<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AddressController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    #[Route('/addresses/new', name: 'address_new')]
    public function new(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            if ($request->query->get('redirect') === 'checkout') {
                return $this->redirectToRoute('checkout', ['address' => $address->getId()]);
            }
            if ($request->query->get('isFromProfile')) {
                return $this->redirectToRoute('profile_addresses');
            }
            return $this->redirectToRoute('profile');
        }

        return $this->render('address/form.html.twig', [
            'form' => $form,
            'title' => 'Nouvelle adresse',
            'isFromProfile' => $request->query->get('isFromProfile')
        ]);
    }

    #[Route('/addresses/{id}/edit', name: 'address_edit')]
    public function edit(Address $address, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        if ($address->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AddressType::class, $address);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            if ($request->query->get('redirect') === 'checkout') {
                return $this->redirectToRoute('checkout', ['address' => $address->getId()]);
            }
            if ($request->query->get('isFromProfile')) {
                return $this->redirectToRoute('profile_addresses');
            }
            return $this->redirectToRoute('profile');
        }

        return $this->render('address/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier l\'adresse',
            'isFromProfile' => $request->query->get('isFromProfile')
        ]);
    }
} 