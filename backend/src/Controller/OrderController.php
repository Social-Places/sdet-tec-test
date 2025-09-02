<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders', name: 'api_orders_')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService
    ) {}

    #[Route('', name: 'create', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['user_id']) || !isset($data['shipping_address'])) {
            return $this->json([
                'success' => false,
                'message' => 'User ID and shipping address are required'
            ], 400);
        }

        $result = $this->orderService->createOrder(
            (int)$data['user_id'],
            $data['shipping_address']
        );

        $statusCode = $result['success'] ? 201 : 400;
        return $this->json($result, $statusCode);
    }
}
