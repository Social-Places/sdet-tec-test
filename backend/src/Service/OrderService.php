<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use App\Service\CartService;

class OrderService
{
    public function __construct(
        private Connection $connection,
        private CartService $cartService
    ) {}

    public function createOrder(int $userId, string $shippingAddress): array
    {
        $cartItems = $this->cartService->getCartItems($userId);
        
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item['stock_quantity'] < $item['quantity']) {
                return [
                    'success' => false, 
                    'message' => "Insufficient stock for {$item['name']}"
                ];
            }
        }

        $totals = $this->cartService->calculateCartTotal($cartItems);

        try {
            $this->connection->beginTransaction();

            // Create order
            $this->connection->executeStatement(
                'INSERT INTO orders (user_id, total_amount, tax_amount, shipping_address, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, NOW(), NOW())',
                [$userId, $totals['total'], $totals['tax'], $shippingAddress]
            );

            $orderId = $this->connection->lastInsertId();

            // Create order items and update stock
            foreach ($cartItems as $item) {
                $this->connection->executeStatement(
                    'INSERT INTO order_items (order_id, product_id, quantity, unit_price, total_price) 
                     VALUES (?, ?, ?, ?, ?)',
                    [
                        $orderId, 
                        $item['product_id'], 
                        $item['quantity'], 
                        $item['price'], 
                        $item['price'] * $item['quantity']
                    ]
                );
            }

            // Clear cart
            $this->cartService->clearCart($userId);

            $this->connection->commit();

            return [
                'success' => true, 
                'message' => 'Order created successfully',
                'order_id' => $orderId
            ];

        } catch (\Exception $e) {
            $this->connection->rollBack();
            return ['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()];
        }
    }

    public function getOrderHistory(int $userId): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT o.*, 
                    GROUP_CONCAT(CONCAT(p.name, " (", oi.quantity, ")") SEPARATOR ", ") as items
             FROM orders o
             LEFT JOIN order_items oi ON o.id = oi.order_id
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC',
            [$userId]
        );
    }

    public function getOrderDetails(int $orderId, int $userId): ?array
    {
        $order = $this->connection->fetchAssociative(
            'SELECT * FROM orders WHERE id = ? AND user_id = ?',
            [$orderId, $userId]
        );

        if (!$order) {
            return null;
        }

        $orderItems = $this->connection->fetchAllAssociative(
            'SELECT oi.*, p.name, p.image_url 
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?',
            [$orderId]
        );

        $order['items'] = $orderItems;
        return $order;
    }
}
