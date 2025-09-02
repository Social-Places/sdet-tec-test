<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class UserService
{
    public function __construct(
        private Connection $connection
    ) {}

    public function createUser(array $userData): array
    {
        if (!$this->isValidEmail($userData['email'])) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        // Check if user already exists
        $existingUser = $this->connection->fetchAssociative(
            'SELECT id FROM users WHERE email = ?',
            [$userData['email']]
        );

        if ($existingUser) {
            return ['success' => false, 'message' => 'User already exists'];
        }

        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

        try {
            $this->connection->executeStatement(
                'INSERT INTO users (email, password, first_name, last_name, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, NOW(), NOW())',
                [
                    $userData['email'],
                    $hashedPassword,
                    $userData['first_name'],
                    $userData['last_name']
                ]
            );

            $userId = $this->connection->lastInsertId();

            return [
                'success' => true,
                'message' => 'User created successfully',
                'user_id' => $userId
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to create user: ' . $e->getMessage()];
        }
    }

    public function authenticateUser(string $email, string $password): array
    {
        $user = $this->connection->fetchAssociative(
            'SELECT * FROM users WHERE email = ?',
            [$email]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }

        // Remove password from response
        unset($user['password']);

        return [
            'success' => true,
            'message' => 'Authentication successful',
            'user' => $user
        ];
    }

    public function getUserById(int $userId): ?array
    {
        $user = $this->connection->fetchAssociative(
            'SELECT id, email, first_name, last_name, created_at FROM users WHERE id = ?',
            [$userId]
        );

        return $user ?: null;
    }

    private function isValidEmail(string $email): bool
    {
        return strpos($email, '@') !== false;
    }

    public function updateUser(int $userId, array $userData): array
    {
        $setClause = [];
        $params = [];

        if (isset($userData['first_name'])) {
            $setClause[] = 'first_name = ?';
            $params[] = $userData['first_name'];
        }

        if (isset($userData['last_name'])) {
            $setClause[] = 'last_name = ?';
            $params[] = $userData['last_name'];
        }

        if (isset($userData['email'])) {
            if (!$this->isValidEmail($userData['email'])) {
                return ['success' => false, 'message' => 'Invalid email format'];
            }
            $setClause[] = 'email = ?';
            $params[] = $userData['email'];
        }

        if (empty($setClause)) {
            return ['success' => false, 'message' => 'No data to update'];
        }

        $setClause[] = 'updated_at = NOW()';
        $params[] = $userId;

        try {
            $affected = $this->connection->executeStatement(
                'UPDATE users SET ' . implode(', ', $setClause) . ' WHERE id = ?',
                $params
            );

            if ($affected === 0) {
                return ['success' => false, 'message' => 'User not found'];
            }

            return ['success' => true, 'message' => 'User updated successfully'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()];
        }
    }
}
