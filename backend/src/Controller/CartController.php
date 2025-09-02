<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    public function __construct(
        private CartService $cartService
    ) {}

    #[Route('/{userId}', name: 'get', methods: ['GET'])]
    public function getCart(int $userId): JsonResponse
    {
        $cartItems = $this->cartService->getCartItems($userId);
        $totals = $this->cartService->calculateCartTotal($cartItems);

        return $this->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'totals' => $totals
            ]
        ]);
    }

    #[Route('/{userId}/add', name: 'add', methods: ['POST'])]
    public function addToCart(int $userId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            return $this->json([
                'success' => false,
                'message' => 'Product ID and quantity are required'
            ], 400);
        }

        $result = $this->cartService->addToCart(
            $userId,
            (int)$data['product_id'],
            (int)$data['quantity']
        );

        return $this->json($result);
    }

    #[Route('/{userId}/update', name: 'update', methods: ['PUT'])]
    public function updateCartItem(int $userId, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            return $this->json([
                'success' => false,
                'message' => 'Product ID and quantity are required'
            ], 400);
        }

        $result = $this->cartService->updateCartItem(
            $userId,
            (int)$data['product_id'],
            (int)$data['quantity']
        );

        return $this->json($result);
    }

    #[Route('/{userId}/remove/{productId}', name: 'remove', methods: ['DELETE'])]
    public function removeFromCart(int $userId, int $productId): JsonResponse
    {
        $result = $this->cartService->removeFromCart($userId, $productId);
        return $this->json($result);
    }

    #[Route('/{userId}/clear', name: 'clear', methods: ['DELETE'])]
    public function clearCart(int $userId): JsonResponse
    {
        $this->cartService->clearCart($userId);
        return $this->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }
}
