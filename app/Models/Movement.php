<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Movement
{
    public static function create(array $data): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $product = Product::find((int) $data['product_id']);
            if (!$product) {
                throw new PDOException('Producto no encontrado');
            }

            if ($data['type'] === 'out' && $product['stock_quantity'] < $data['quantity']) {
                throw new PDOException('Stock insuficiente para registrar la salida.');
            }

            $stmt = $pdo->prepare('INSERT INTO movements (product_id, type, quantity, date, notes, user_id) VALUES (:product_id, :type, :quantity, NOW(), :notes, :user_id)');
            $stmt->execute([
                'product_id' => $data['product_id'],
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'notes' => $data['notes'] ?? '',
                'user_id' => $data['user_id'],
            ]);

            $delta = $data['type'] === 'in' ? $data['quantity'] : -$data['quantity'];
            Product::changeStock((int) $data['product_id'], $delta);

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function latest(int $limit = 5): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT m.*, p.name as product_name, p.category as product_category FROM movements m INNER JOIN products p ON p.id = m.product_id ORDER BY m.date DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function filtered(array $filters = []): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT m.*, p.name as product_name, p.category as product_category, u.name as user_name, u.role as user_role
                FROM movements m
                INNER JOIN products p ON p.id = m.product_id
                LEFT JOIN users u ON u.id = m.user_id
                WHERE 1=1';
        $params = [];

        if (!empty($filters['type']) && in_array($filters['type'], ['in', 'out'], true)) {
            $sql .= ' AND m.type = :type';
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (p.name LIKE :search OR m.notes LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date_range'])) {
            $sql .= ' AND m.date >= :start_date';
            $params['start_date'] = self::rangeStart($filters['date_range']);
        }

        $sql .= ' ORDER BY m.date DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function stats(?string $range = null): array
    {
        $pdo = Database::connection();
        $params = [];
        $filter = '';

        if ($range) {
            $filter = 'WHERE date >= :start_date';
            $params['start_date'] = self::rangeStart($range);
        }

        $stmt = $pdo->prepare("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN type = 'in' THEN 1 ELSE 0 END) as total_in,
                SUM(CASE WHEN type = 'out' THEN 1 ELSE 0 END) as total_out,
                SUM(CASE WHEN type = 'in' THEN quantity ELSE 0 END) as incoming_qty,
                SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as outgoing_qty
            FROM movements
            $filter");
        $stmt->execute($params);
        $rows = $stmt->fetch();

        return [
            'total' => (int) ($rows['total'] ?? 0),
            'total_in' => (int) ($rows['total_in'] ?? 0),
            'total_out' => (int) ($rows['total_out'] ?? 0),
            'incoming_qty' => (int) ($rows['incoming_qty'] ?? 0),
            'outgoing_qty' => (int) ($rows['outgoing_qty'] ?? 0),
        ];
    }

    private static function rangeStart(string $range): string
    {
        $now = new \DateTimeImmutable('today');

        return match ($range) {
            'today' => $now->format('Y-m-d 00:00:00'),
            'week' => $now->modify('-7 days')->format('Y-m-d 00:00:00'),
            'month' => $now->modify('-1 month')->format('Y-m-d 00:00:00'),
            'quarter' => $now->modify('-3 months')->format('Y-m-d 00:00:00'),
            default => '1970-01-01 00:00:00',
        };
    }
}
