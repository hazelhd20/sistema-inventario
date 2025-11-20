<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    public static function all(?string $search = null, ?string $category = null): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT * FROM products WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (name LIKE :search OR category LIKE :search OR description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($category) {
            $sql .= ' AND category = :category';
            $params['category'] = $category;
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO products (name, description, category, price, cost, stock_quantity, min_stock_level, image_url, created_at) VALUES (:name, :description, :category, :price, :cost, :stock_quantity, :min_stock_level, :image_url, NOW())');
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'] ?? 'General',
            'price' => $data['price'],
            'cost' => $data['cost'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 0,
            'image_url' => $data['image_url'] ?? null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE products SET name = :name, description = :description, category = :category, price = :price, cost = :cost, stock_quantity = :stock_quantity, min_stock_level = :min_stock_level, image_url = :image_url WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'] ?? 'General',
            'price' => $data['price'],
            'cost' => $data['cost'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 0,
            'image_url' => $data['image_url'] ?? null,
        ]);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function adjustStock(int $id, int $newStock): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE products SET stock_quantity = :stock WHERE id = :id');
        $stmt->execute(['id' => $id, 'stock' => $newStock]);
    }

    public static function changeStock(int $id, int $delta): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity + :delta WHERE id = :id');
        $stmt->execute(['id' => $id, 'delta' => $delta]);
    }

    public static function lowStock(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT * FROM products WHERE stock_quantity <= min_stock_level ORDER BY stock_quantity ASC');
        return $stmt->fetchAll();
    }

    public static function stats(): array
    {
        $pdo = Database::connection();
        $summary = $pdo->query('SELECT COUNT(*) as total_products, SUM(stock_quantity) as total_stock, SUM(price * stock_quantity) as total_value FROM products')->fetch();

        return [
            'total_products' => (int) ($summary['total_products'] ?? 0),
            'total_stock' => (int) ($summary['total_stock'] ?? 0),
            'total_value' => (float) ($summary['total_value'] ?? 0),
        ];
    }

    public static function recent(int $limit = 5): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM products ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function topByStock(int $limit = 5): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM products ORDER BY stock_quantity DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
