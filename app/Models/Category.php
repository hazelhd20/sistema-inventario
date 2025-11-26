<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE name = :name');
        $stmt->execute(['name' => $name]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function create(string $name): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO categories (name, created_at) VALUES (:name, NOW())');
        $stmt->execute(['name' => $name]);

        return (int) $pdo->lastInsertId();
    }
}
