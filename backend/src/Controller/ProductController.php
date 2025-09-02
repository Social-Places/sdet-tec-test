<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products', name: 'api_products_')]
class ProductController extends AbstractController
{
    public function __construct(
        private Connection $connection
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $category = $request->query->get('category');
        $limit = $request->query->getInt('limit', 20);
        $offset = $request->query->getInt('offset', 0);

        $sql = 'SELECT p.*, c.name as category_name FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1';
        
        $params = [];

        if ($category) {
            $sql .= ' AND c.name = ?';
            $params[] = $category;
        }

        $sql .= " LIMIT {$limit} OFFSET {$offset}";
        $products = $this->connection->fetchAllAssociative($sql, $params);

        return $this->json([
            'success' => true,
            'data' => $products
        ]);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $product = $this->connection->fetchAssociative(
            'SELECT p.*, c.name as category_name FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ? AND p.is_active = 1',
            [$id]
        );

        if (!$product) {
            return $this->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return $this->json([
            'success' => true,
            'data' => $product
        ]);
    }

    #[Route('/categories', name: 'categories', methods: ['GET'])]
    public function categories(): JsonResponse
    {
        $categories = $this->connection->fetchAllAssociative(
            'SELECT * FROM categories ORDER BY name'
        );

        return $this->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        if (strlen($query) < 3) {
            return $this->json([
                'success' => false,
                'message' => 'Search query must be at least 3 characters long'
            ], 400);
        }

        $products = $this->connection->fetchAllAssociative(
            'SELECT p.*, c.name as category_name FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE p.is_active = 1 AND (p.name LIKE ? OR p.description LIKE ?)
             ORDER BY p.name',
            ["%{$query}%", "%{$query}%"]
        );

        return $this->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
