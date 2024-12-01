<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Enum\ProductStatus;
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

        // Update product stock
        $newStock = $product->getStock() - $quantity;
        $product->setStock($newStock);
        
        if ($newStock === 0) {
            $product->setStatus(ProductStatus::OUT_OF_STOCK);
        }

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        // Publish Mercure update for stock
        $update = new Update(
            sprintf('product/%d', $product->getId()),
            json_encode([
                'stock' => $newStock,
                'status' => $product->getStatus()->value
            ])
        );
        $this->hub->publish($update);

        $this->publishCartUpdate($cart, 'item_added', [
            'itemId' => $cartItem->getId(),
            'quantity' => $cartItem->getQuantity()
        ]);

        return new JsonResponse([
            'message' => 'Product added to cart',
            'cartCount' => $cart->getItemCount(),
            'newStock' => $newStock
        ]);
    }

    #[Route('/carts/remove/{id}', name: 'cart_remove', methods: ['POST'])]
    public function remove(CartItem $cartItem): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'User must be logged in'], Response::HTTP_UNAUTHORIZED);
        }

        $cart = $cartItem->getCart();
        $product = $cartItem->getProduct();
        $itemId = $cartItem->getId();
        
        $returnedQuantity = $cartItem->getQuantity();
        $newStock = $product->getStock() + $returnedQuantity;
        $product->setStock($newStock);
        
        if ($product->getStatus() === ProductStatus::OUT_OF_STOCK && $newStock > 0) {
            $product->setStatus(ProductStatus::AVAILABLE);
        }

        $cart->removeItem($cartItem);
        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();

        $update = new Update(
            sprintf('product/%d', $product->getId()),
            json_encode([
                'stock' => $newStock,
                'status' => $product->getStatus()->value
            ])
        );
        $this->hub->publish($update);

        $this->publishCartUpdate($cart, 'item_removed', [
            'itemId' => $itemId
        ]);

        return new JsonResponse([
            'message' => 'Item removed from cart',
            'cartCount' => $cart->getItemCount(),
            'newTotal' => $cart->getTotal()
        ]);
    }

    #[Route('/carts/update/{id}', name: 'cart_update_quantity', methods: ['POST'])]
    public function updateQuantity(CartItem $cartItem, Request $request): JsonResponse
    {
        if (!$this->getUser()) {
            return new JsonResponse(['error' => 'User must be logged in'], Response::HTTP_UNAUTHORIZED);
        }
    
        $newQuantity = $request->request->getInt('quantity', 1);
        $product = $cartItem->getProduct();
        $currentQuantity = $cartItem->getQuantity();
        
        $quantityDifference = $newQuantity - $currentQuantity;
        $newStock = $product->getStock() - $quantityDifference;
        
        if ($quantityDifference > 0 && $quantityDifference > $product->getStock()) {
            return new JsonResponse(['error' => 'Not enough stock'], Response::HTTP_BAD_REQUEST);
        }
    
        $cartItem->setQuantity($newQuantity);
        
        $product->setStock($newStock);
        
        if ($newStock === 0) {
            $product->setStatus(ProductStatus::OUT_OF_STOCK);
        } elseif ($newStock > 0 && $product->getStatus() === ProductStatus::OUT_OF_STOCK) {
            $product->setStatus(ProductStatus::AVAILABLE);
        }
    
        $this->entityManager->flush();
    
        $cart = $cartItem->getCart();
        $newTotal = $cart->getTotal();
    
        $update = new Update(
            sprintf('product/%d', $product->getId()),
            json_encode([
                'stock' => $newStock,
                'status' => $product->getStatus()->value
            ])
        );
        $this->hub->publish($update);
    
        $this->publishCartUpdate($cart, 'quantity_updated', [
            'itemId' => $cartItem->getId(),
            'quantity' => $newQuantity
        ]);
    
        return new JsonResponse([
            'newTotal' => $newTotal,
            'newStock' => $newStock
        ]);
    }

    #[Route('/carts/total', name: 'cart_total')]
    public function getCartTotal(): JsonResponse
    {
        $cart = $this->getOrCreateCart();
        return new JsonResponse([
            'total' => $cart->getTotal(),
            'count' => $cart->getItemCount()
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

    private function publishCartUpdate(Cart $cart, string $action, array $additionalData = []): void
    {
        $update = new Update(
            sprintf('cart/%d', $cart->getUser()->getId()),
            json_encode(array_merge([
                'action' => $action,
                'newTotal' => $cart->getTotal(),
                'cartCount' => $cart->getItemCount()
            ], $additionalData))
        );
        $this->hub->publish($update);
    }
}