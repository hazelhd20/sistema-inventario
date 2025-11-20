<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, name, email, role, active, last_login, created_at FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function verifyCredentials(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if (!$user || !(bool) $user['active']) {
            return null;
        }

        if (password_verify($password, $user['password'])) {
            $cleanUser = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];
            return $cleanUser;
        }

        return null;
    }

    public static function all(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, name, email, role, active, last_login, created_at FROM users ORDER BY id ASC');
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role, active, created_at) VALUES (:name, :email, :password, :role, :active, NOW())');
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role' => $data['role'] ?? 'employee',
            'active' => $data['active'] ?? 1,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::connection();
        $fields = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'employee',
            'active' => $data['active'] ?? 1,
            'id' => $id,
        ];

        $sql = 'UPDATE users SET name = :name, email = :email, role = :role, active = :active';
        if (!empty($data['password'])) {
            $sql .= ', password = :password';
            $fields['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $sql .= ' WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($fields);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function toggleStatus(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE users SET active = IF(active = 1, 0, 1) WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function updateLastLogin(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
