<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private OrderService $orderService
    ) {}

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $requiredFields = ['email', 'password', 'first_name', 'last_name'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return $this->json([
                    'success' => false,
                    'message' => "Field '{$field}' is required"
                ], 400);
            }
        }

        $result = $this->userService->createUser($data);
        $statusCode = $result['success'] ? 201 : 400;
        
        return $this->json($result, $statusCode);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'success' => false,
                'message' => 'Email and password are required'
            ], 400);
        }

        $result = $this->userService->authenticateUser($data['email'], $data['password']);
        $statusCode = $result['success'] ? 200 : 401;
        
        return $this->json($result, $statusCode);
    }

    #[Route('/{id}', name: 'profile', methods: ['GET'])]
    public function getProfile(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        return $this->json([
            'success' => true,
            'data' => $user
        ]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateProfile(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $result = $this->userService->updateUser($id, $data);
        $statusCode = $result['success'] ? 200 : 400;
        
        return $this->json($result, $statusCode);
    }

    #[Route('/{id}/orders', name: 'orders', methods: ['GET'])]
    public function getOrderHistory(int $id): JsonResponse
    {
        $orders = $this->orderService->getOrderHistory($id);
        
        return $this->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    #[Route('/{userId}/orders/{orderId}', name: 'order_details', methods: ['GET'])]
    public function getOrderDetails(int $userId, int $orderId): JsonResponse
    {
        $order = $this->orderService->getOrderDetails($orderId, $userId);
        
        if (!$order) {
            return $this->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return $this->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
