<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    public static function all(?string $search = null, ?int $categoryId = null, bool $onlyActive = false): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT p.*, c.name as category, c.id as category_id, (SELECT COUNT(*) FROM movements m WHERE m.product_id = p.id) as movements_count 
                FROM products p 
                INNER JOIN categories c ON c.id = p.category_id
                WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (p.name LIKE :search OR c.name LIKE :search OR p.description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if ($categoryId) {
            $sql .= ' AND p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        if ($onlyActive) {
            $sql .= ' AND p.active = 1';
        }

        $sql .= ' ORDER BY p.created_at DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function find(int $id, bool $includeInactive = true): ?array
    {
        $pdo = Database::connection();
        $sql = 'SELECT p.*, c.name as category, c.id as category_id 
                FROM products p 
                INNER JOIN categories c ON c.id = p.category_id 
                WHERE p.id = :id';
        if (!$includeInactive) {
            $sql .= ' AND p.active = 1';
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();

        return $product ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO products (name, description, category_id, price, cost, stock_quantity, min_stock_level, active, image_url, created_at) VALUES (:name, :description, :category_id, :price, :cost, :stock_quantity, :min_stock_level, :active, :image_url, NOW())');
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'cost' => $data['cost'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 0,
            'active' => 1,
            'image_url' => $data['image_url'] ?? null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE products SET name = :name, description = :description, category_id = :category_id, price = :price, cost = :cost, stock_quantity = :stock_quantity, min_stock_level = :min_stock_level, image_url = :image_url WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'category_id' => $data['category_id'],
            'price' => $data['price'],
            'cost' => $data['cost'],
            'stock_quantity' => $data['stock_quantity'] ?? 0,
            'min_stock_level' => $data['min_stock_level'] ?? 0,
            'image_url' => $data['image_url'] ?? null,
        ]);
    }

    public static function delete(int $id): bool
    {
        if (self::hasMovements($id)) {
            return false;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function deactivate(int $id): void
    {
        self::setStatus($id, false);
    }

    public static function activate(int $id): void
    {
        self::setStatus($id, true);
    }

    private static function setStatus(int $id, bool $active): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE products SET active = :active WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'active' => $active ? 1 : 0,
        ]);
    }

    public static function hasMovements(int $id): bool
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM movements WHERE product_id = :id');
        $stmt->execute(['id' => $id]);

        return ((int) $stmt->fetchColumn()) > 0;
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
        $stmt = $pdo->query('SELECT p.*, c.name as category, c.id as category_id FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.stock_quantity <= p.min_stock_level AND p.active = 1 ORDER BY p.stock_quantity ASC');
        return $stmt->fetchAll();
    }

    public static function stats(): array
    {
        $pdo = Database::connection();
        $summary = $pdo->query('SELECT COUNT(*) as total_products, SUM(stock_quantity) as total_stock, SUM(price * stock_quantity) as total_value FROM products WHERE active = 1')->fetch();

        return [
            'total_products' => (int) ($summary['total_products'] ?? 0),
            'total_stock' => (int) ($summary['total_stock'] ?? 0),
            'total_value' => (float) ($summary['total_value'] ?? 0),
        ];
    }

    public static function recent(int $limit = 5): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT p.*, c.name as category, c.id as category_id FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.active = 1 ORDER BY p.created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function topByStock(int $limit = 5): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT p.*, c.name as category, c.id as category_id FROM products p INNER JOIN categories c ON c.id = p.category_id WHERE p.active = 1 ORDER BY p.stock_quantity DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
