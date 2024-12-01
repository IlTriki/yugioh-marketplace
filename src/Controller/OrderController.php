<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use App\Enum\ProductStatus;
use App\Repository\CartRepository;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
        private AddressRepository $addressRepository,
        private HubInterface $hub,
        protected string $mercurePublicUrl
    ) {}

    #[Route('/orders/checkout', name: 'checkout')]
    public function checkout(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        $cart = $this->cartRepository->findOneBy([
            'user' => $this->getUser(),
        ]);

        if (!$cart || $cart->getItems()->isEmpty()) {
            return $this->redirectToRoute('cart_view');
        }

        $selectedAddress = null;
        if ($addressId = $request->query->get('address')) {
            $selectedAddress = $this->addressRepository->find($addressId);
            if ($selectedAddress && $selectedAddress->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException();
            }
        } elseif ($this->getUser()->getAddresses()->count() > 0) {
            $selectedAddress = $this->getUser()->getAddresses()->first();
        }

        return $this->render('order/checkout.html.twig', [
            'cart' => $cart,
            'mercure_url' => $this->mercurePublicUrl,
            'selectedAddress' => $selectedAddress,
            'user' => $this->getUser()
        ]);
    }

    #[Route('/orders/confirm', name: 'order_confirm', methods: ['POST'])]
    public function confirmOrder(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        $cart = $this->cartRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        if (!$cart || $cart->getItems()->isEmpty()) {
            return $this->redirectToRoute('cart_view');
        }

        $addressId = $request->request->get('address_id');
        $address = $this->addressRepository->find($addressId);
        
        if (!$address || $address->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Please select a valid delivery address');
            return $this->redirectToRoute('checkout');
        }

        $order = new Order();
        $order->setUser($this->getUser())
              ->setReference($this->generateOrderReference())
              ->setCreatedAt(new \DateTime())
              ->setStatus(OrderStatus::SENDING)
              ->setDeliveryAddress($address);

        foreach ($cart->getItems() as $cartItem) {
            $product = $cartItem->getProduct();

            $orderItem = new OrderItem();
            $orderItem->setOrder($order)
                     ->setProduct($product)
                     ->setQuantity($cartItem->getQuantity())
                     ->setProductPrice($cartItem->getPriceAtAddition());

            $newStock = $product->getStock();
            $product->setStock($newStock);
            
            if ($newStock === 0) {
                $product->setStatus(ProductStatus::OUT_OF_STOCK);
            }

            $order->addOrderItem($orderItem);
            
            $update = new Update(
                sprintf('product/%d', $product->getId()),
                json_encode([
                    'stock' => $newStock,
                    'status' => $product->getStatus()->value
                ])
            );
            $this->hub->publish($update);
        }

        $this->entityManager->persist($order);
        
        $this->entityManager->remove($cart);
        
        $this->entityManager->flush();

        return $this->redirectToRoute('order_success', ['reference' => $order->getReference()]);
    }

    #[Route('/orders/success/{reference}', name: 'order_success')]
    public function success(string $reference): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }
        
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/success.html.twig', [
            'order' => $order
        ]);
    }

    private function generateOrderReference(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }
} 