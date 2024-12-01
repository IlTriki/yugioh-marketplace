<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepository $cartRepository,
        private HubInterface $hub,
        protected string $mercurePublicUrl
    ) {}

    #[Route('/carts', name: 'cart_view')]
    public function view(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('admin_home');
        }

        $cart = $this->getOrCreateCart();

        return $this->render('cart/view.html.twig', [
            'cart' => $cart,
            'mercure_url' => $this->mercurePublicUrl,
        ]);
    }

    #[Route('/carts/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(Product $product, Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'User must be logged in'], Response::HTTP_UNAUTHORIZED);
        }

        $quantity = $request->request->getInt('quantity', 1);
        
        if ($quantity > $product->getStock()) {
            return new JsonResponse(['error' => 'Not enough stock'], Response::HTTP_BAD_REQUEST);
        }

        $cart = $this->getOrCreateCart();
        
        $cartItem = $cart->getItems()->filter(
            fn(CartItem $item) => $item->getProduct() === $product
        )->first();

        if ($cartItem) {
            $newQuantity = $cartItem->getQuantity() + $quantity;
            if ($newQuantity > $product->getStock()) {
                return new JsonResponse(['error' => 'Not enough stock'], Response::HTTP_BAD_REQUEST);
            }
            $cartItem->setQuantity($newQuantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart)
                    ->setProduct($product)
                    ->setQuantity($quantity)
                    ->setPriceAtAddition($product->getPrice());
            $cart->addItem($cartItem);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        // Publish Mercure update for stock
        $update = new Update(
            sprintf('product/%d', $product->getId()),
            json_encode([
                'stock' => $product->getStock() - $quantity
            ])
        );
        $this->hub->publish($update);

        return new JsonResponse([
            'message' => 'Product added to cart',
            'cartCount' => $cart->getItemCount()
        ]);
    }

    #[Route('/carts/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(CartItem $cartItem): JsonResponse
    {
        $cart = $cartItem->getCart();
        if ($cart->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $cart->removeItem($cartItem);
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Item removed from cart',
            'cartCount' => $cart->getItemCount()
        ]);
    }

    #[Route('/carts/update/{id}', name: 'cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(CartItem $cartItem, Request $request): JsonResponse
    {
        $quantity = $request->request->getInt('quantity');
        if ($quantity < 1) {
            return new JsonResponse(['error' => 'Invalid quantity'], Response::HTTP_BAD_REQUEST);
        }

        $product = $cartItem->getProduct();
        if ($quantity > $product->getStock()) {
            return new JsonResponse(['error' => 'Not enough stock'], Response::HTTP_BAD_REQUEST);
        }

        $cartItem->setQuantity($quantity);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Quantity updated',
            'newTotal' => $cartItem->getCart()->getTotal()
        ]);
    }

    private function getOrCreateCart(): Cart
    {
        $user = $this->getUser();
        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }
        
        return $cart;
    }
}