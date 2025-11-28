<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Movement
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();

        $product = Product::find((int) $data['product_id']);
        if (!$product) {
            throw new PDOException('Producto no encontrado');
        }
        if (empty($product['active'])) {
            throw new PDOException('El producto esta inactivo y no puede registrar movimientos.');
        }
        if ($data['type'] === 'out' && (int) $product['stock_quantity'] < (int) $data['quantity']) {
            throw new PDOException('Stock insuficiente para registrar la salida.');
        }

        $stmt = $pdo->prepare('INSERT INTO movements (product_id, type, quantity, date, notes, user_id, status) VALUES (:product_id, :type, :quantity, NOW(), :notes, :user_id, :status)');
        $stmt->execute([
            'product_id' => $data['product_id'],
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'notes' => $data['notes'] ?? '',
            'user_id' => $data['user_id'],
            'status' => 'pending',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM movements WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function approve(int $id): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $movement = self::find($id);
            if (!$movement) {
                throw new PDOException('Movimiento no encontrado');
            }
            if ($movement['status'] !== 'pending') {
                throw new PDOException('El movimiento ya fue procesado');
            }

            $product = Product::find((int) $movement['product_id']);
            if (!$product) {
                throw new PDOException('Producto no encontrado');
            }
            if (empty($product['active'])) {
                throw new PDOException('El producto esta inactivo y no puede procesarse el movimiento.');
            }

            if ($movement['type'] === 'out' && $product['stock_quantity'] < $movement['quantity']) {
                throw new PDOException('Stock insuficiente para aprobar la salida.');
            }

            $stmt = $pdo->prepare('UPDATE movements SET status = :status WHERE id = :id');
            $stmt->execute(['status' => 'approved', 'id' => $id]);

            $delta = $movement['type'] === 'in' ? $movement['quantity'] : -$movement['quantity'];
            Product::changeStock((int) $movement['product_id'], $delta);

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function reject(int $id): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $movement = self::find($id);
            if (!$movement) {
                throw new PDOException('Movimiento no encontrado');
            }
            if ($movement['status'] !== 'pending') {
                throw new PDOException('El movimiento ya fue procesado');
            }

            $stmt = $pdo->prepare('DELETE FROM movements WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function latest(int $limit = 5, bool $onlyActiveProducts = true): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT m.*, p.name as product_name, c.name as product_category 
                FROM movements m 
                INNER JOIN products p ON p.id = m.product_id 
                INNER JOIN categories c ON c.id = p.category_id 
                WHERE m.status = :status';
        if ($onlyActiveProducts) {
            $sql .= ' AND p.active = 1';
        }
        $sql .= ' ORDER BY m.date DESC LIMIT :limit';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':status', 'approved');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function filtered(array $filters = [], bool $onlyActiveProducts = true): array
    {
        $pdo = Database::connection();
        $sql = 'SELECT m.*, p.name as product_name, c.name as product_category, u.name as user_name, u.role as user_role
                FROM movements m
                INNER JOIN products p ON p.id = m.product_id
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN users u ON u.id = m.user_id
                WHERE 1=1';
        $params = [];

        if ($onlyActiveProducts) {
            $sql .= ' AND p.active = 1';
        }

        $status = $filters['status'] ?? 'approved';
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $sql .= ' AND m.status = :status';
            $params['status'] = $status;
        }

        if (!empty($filters['type']) && in_array($filters['type'], ['in', 'out'], true)) {
            $sql .= ' AND m.type = :type';
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND (p.name LIKE :search OR m.notes LIKE :search OR c.name LIKE :search OR u.name LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date_range'])) {
            $sql .= ' AND m.date >= :start_date';
            $params['start_date'] = self::rangeStart($filters['date_range']);
        }

        // Filtro por fecha específica (inicio y fin)
        if (!empty($filters['date_from'])) {
            $sql .= ' AND m.date >= :date_from';
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $sql .= ' AND m.date <= :date_to';
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        $sql .= ' ORDER BY m.date DESC';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function stats(?string $range = null, bool $onlyActiveProducts = true): array
    {
        $pdo = Database::connection();
        $params = [];
        $filter = '';

        $conditions = ['m.status = :status'];
        $params['status'] = 'approved';

        if ($range) {
            $conditions[] = 'm.date >= :start_date';
            $params['start_date'] = self::rangeStart($range);
        }

        if ($onlyActiveProducts) {
            $conditions[] = 'p.active = 1';
        }

        $filter = 'WHERE ' . implode(' AND ', $conditions);

        $stmt = $pdo->prepare("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN m.type = 'in' THEN 1 ELSE 0 END) as total_in,
                SUM(CASE WHEN m.type = 'out' THEN 1 ELSE 0 END) as total_out,
                SUM(CASE WHEN m.type = 'in' THEN m.quantity ELSE 0 END) as incoming_qty,
                SUM(CASE WHEN m.type = 'out' THEN m.quantity ELSE 0 END) as outgoing_qty
            FROM movements m
            INNER JOIN products p ON p.id = m.product_id
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

    /**
     * Estadísticas de movimientos por rango de fechas específico
     */
    public static function statsByDateRange(?string $dateFrom, ?string $dateTo, bool $onlyActiveProducts = true): array
    {
        $pdo = Database::connection();
        $params = ['status' => 'approved'];
        $conditions = ['m.status = :status'];

        if ($onlyActiveProducts) {
            $conditions[] = 'p.active = 1';
        }

        if ($dateFrom) {
            $conditions[] = 'm.date >= :date_from';
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo) {
            $conditions[] = 'm.date <= :date_to';
            $params['date_to'] = $dateTo . ' 23:59:59';
        }

        $filter = 'WHERE ' . implode(' AND ', $conditions);

        $stmt = $pdo->prepare("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN m.type = 'in' THEN 1 ELSE 0 END) as total_in,
                SUM(CASE WHEN m.type = 'out' THEN 1 ELSE 0 END) as total_out,
                SUM(CASE WHEN m.type = 'in' THEN m.quantity ELSE 0 END) as incoming_qty,
                SUM(CASE WHEN m.type = 'out' THEN m.quantity ELSE 0 END) as outgoing_qty
            FROM movements m
            INNER JOIN products p ON p.id = m.product_id
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

    /**
     * Totales de movimientos agrupados por producto
     */
    public static function totalsByProduct(?string $dateFrom, ?string $dateTo, bool $onlyActiveProducts = true): array
    {
        $pdo = Database::connection();
        $params = ['status' => 'approved'];
        $conditions = ['m.status = :status'];

        if ($onlyActiveProducts) {
            $conditions[] = 'p.active = 1';
        }

        if ($dateFrom) {
            $conditions[] = 'm.date >= :date_from';
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo) {
            $conditions[] = 'm.date <= :date_to';
            $params['date_to'] = $dateTo . ' 23:59:59';
        }

        $filter = 'WHERE ' . implode(' AND ', $conditions);

        $stmt = $pdo->prepare("SELECT 
                p.id,
                p.name as product_name,
                c.name as category_name,
                SUM(CASE WHEN m.type = 'in' THEN m.quantity ELSE 0 END) as total_in,
                SUM(CASE WHEN m.type = 'out' THEN m.quantity ELSE 0 END) as total_out,
                COUNT(*) as total_movements
            FROM movements m
            INNER JOIN products p ON p.id = m.product_id
            INNER JOIN categories c ON c.id = p.category_id
            $filter
            GROUP BY p.id, p.name, c.name
            ORDER BY total_movements DESC");
        $stmt->execute($params);
        return $stmt->fetchAll();
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
