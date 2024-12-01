<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Product;
use App\Enum\OrderStatus;
use App\Enum\ProductStatus;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        protected string $mercurePublicUrl,
        private ProductRepository $productRepository,
        private OrderRepository $orderRepository,
        private ImageRepository $imageRepository,
        private UserRepository $userRepository,
    ) {
    }

    #[Route('', name: 'admin_home')]
    public function index(): Response
    {
        $productStats = $this->productRepository->getProductStatistics();

        $recentOrders = $this->orderRepository->findLastOrders(5);

        $availabilityRatio = $this->productRepository->getAvailabilityRatio();

        $monthlySales = $this->orderRepository->getMonthlySalesForLastYear();

        return $this->render('admin/admin_home.html.twig', [
            'mercure_url' => $this->mercurePublicUrl,
            'productStats' => $productStats,
            'recentOrders' => $recentOrders,
            'availabilityRatio' => $availabilityRatio,
            'monthlySales' => $monthlySales,
            'isAdministrator' => true,
        ]);
    }

    #[Route('/products', name: 'admin_products')]
    public function listProducts(
        Request $request,
        PaginatorInterface $paginator,
        CategoryRepository $categoryRepository
    ): Response {
        $filters = [
            'name' => $request->query->get('name'),
            'category' => $request->query->get('category'),
            'status' => $request->query->get('status'),
            'priceFrom' => $request->query->get('minPrice'),
            'priceTo' => $request->query->get('maxPrice'),
            'sortBy' => $request->query->get('sortField') . ', ' . $request->query->get('sortDirection'),
            'sortField' => $request->query->get('sortField'),
            'sortDirection' => $request->query->get('sortDirection'),
        ];

        $queryBuilder = $this->productRepository->getFilteredProductsQuery(null, $filters);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        $categories = $categoryRepository->findAll();

        return $this->render('admin/product/list.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'filters' => $filters,
            'isAdministrator' => true,
            'productStatuses' => ProductStatus::cases(),
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function listUsers(
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $filters = [
            'search' => $request->query->get('search'),
            'role' => $request->query->get('role'),
            'sortField' => $request->query->get('sortField', 'u.email'),
            'sortDirection' => $request->query->get('sortDirection', 'ASC'),
        ];

        $queryBuilder = $this->userRepository->getFilteredPaginatedUsersQuery($filters);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/list.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'isAdministrator' => true,
        ]);
    }

    #[Route('/orders', name: 'admin_orders')]
    public function listOrders(
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $filters = [
            'search' => $request->query->get('search'),
            'status' => $request->query->get('status'),
            'dateFrom' => $request->query->get('dateFrom'),
            'dateTo' => $request->query->get('dateTo'),
            'sortField' => $request->query->get('sortField', 'o.createdAt'),
            'sortDirection' => $request->query->get('sortDirection', 'DESC'),
        ];

        $queryBuilder = $this->orderRepository->getFilteredPaginatedOrdersQuery($filters);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/order/list.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
            'isAdministrator' => true,
        ]);
    }

    #[Route('/products/new', name: 'admin_product_new')]
    public function newProduct(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            if ($product->getCategory() && $product->getCategory()->getName() === 'card' && (
                empty($product->getType()) || empty($product->getFrameType()) ||
                is_null($product->getAtk()) || is_null($product->getDef()) ||
                is_null($product->getLevel()) || empty($product->getRace()) ||
                empty($product->getAttribute())
            )) {
                $this->addFlash('error', 'All card-specific fields must be filled out.');
                return $this->render('admin/product/form.html.twig', [
                    'form' => $form->createView(),
                    'product' => $product,
                    'isAdministrator' => true,
                ]);
            }

            try {
                foreach ($product->getImages() as $image) {
                    $this->imageRepository->remove($image);
                }
                
                $images = $form->get('images')->getData();

                foreach ($images as $imageUrl) {
                    $product->addImage($imageUrl);
                }

                $this->productRepository->save($product);
                $this->addFlash('success', 'Product created successfully.');

                return $this->redirectToRoute('admin_products');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while creating the product : ' . $e);
            }
        }

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'isAdministrator' => true,
        ]);
    }

    #[Route('/products/{id}/edit', name: 'admin_product_edit')]
    public function editProduct(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            if ($product->getCategory() && $product->getCategory()->getName() === 'card' && (empty($product->getType()) || empty($product->getFrameType()) ||
                is_null($product->getAtk()) || is_null($product->getDef()) ||
                is_null($product->getLevel()) || empty($product->getRace()) ||
                empty($product->getAttribute()))) {
                $this->addFlash('error', 'All card-specific fields must be filled out.');
                return $this->redirectToRoute('admin_products');
            }

            try {
                foreach ($product->getImages() as $image) {
                    $this->imageRepository->remove($image);
                    $product->removeImage($image);
                }

                $images = $form->get('images')->getData();

                foreach ($images as $imageUrl) {
                    $image = new Image();
                    $image->setUrl($imageUrl);
                    $image->setProduct($product);
                    $product->addImage($image);
                }

                $this->productRepository->save($product);
                $this->addFlash('success', 'Product updated successfully.');

                return $this->redirectToRoute('admin_products');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while updating the product : ' . $e);
                return $this->redirectToRoute('admin_products');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'form is not valid');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'isAdministrator' => true,
        ]);
    }

    #[Route('/products/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            try {
                if ($this->orderRepository->hasOrders($product)) {
                    $this->addFlash('error', 'Cannot delete product as it is associated with orders.');
                    return $this->redirectToRoute('admin_products');
                }

                foreach ($product->getImages() as $image) {
                    $this->imageRepository->remove($image);
                }

                $this->productRepository->remove($product);
                $this->addFlash('success', 'Product deleted successfully.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while deleting the product : ' . $e);
            }
        }

        return $this->redirectToRoute('admin_products');
    }

    #[Route('/orders/{reference}/status', name: 'admin_order_status_update', methods: ['POST'])]
    public function updateOrderStatus(
        Request $request,
        string $reference,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $order = $this->orderRepository->findOneBy(['reference' => $reference]);
        
        if (!$order) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Order not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        $status = $request->request->get('status');
        
        try {
            $newStatus = OrderStatus::from($status);
            $order->setStatus($newStatus);
            $this->orderRepository->save($order);
            
            return new JsonResponse([
                'success' => true,
                'newStatus' => $newStatus->value
            ]);
        } catch (\ValueError $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Invalid status'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
