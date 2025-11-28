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

    /**
     * Crea un token de recuperación de contraseña
     * Duración: 10 minutos
     */
    public static function createPasswordResetToken(string $email): ?string
    {
        $pdo = Database::connection();

        // Verificar que el usuario existe y está activo
        $user = self::findByEmail($email);
        if (!$user || !(bool) $user['active']) {
            return null;
        }

        // Invalidar tokens anteriores para este email
        $stmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE email = :email AND used = 0');
        $stmt->execute(['email' => $email]);

        // Generar token único
        $token = bin2hex(random_bytes(32));
        
        // Token expira en 10 minutos
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $pdo->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)');
        $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    /**
     * Valida un token de recuperación de contraseña
     */
    public static function validatePasswordResetToken(string $token): ?array
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare('
            SELECT pr.*, u.name, u.id as user_id, u.active
            FROM password_resets pr
            INNER JOIN users u ON u.email = pr.email
            WHERE pr.token = :token 
            AND pr.used = 0 
            AND pr.expires_at > NOW()
            LIMIT 1
        ');
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch();

        if (!$result || !(bool) $result['active']) {
            return null;
        }

        return $result;
    }

    /**
     * Resetea la contraseña usando un token válido
     */
    public static function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        $pdo = Database::connection();

        // Validar token
        $resetData = self::validatePasswordResetToken($token);
        if (!$resetData) {
            return false;
        }

        // Actualizar contraseña
        $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE email = :email');
        $stmt->execute([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'email' => $resetData['email'],
        ]);

        // Marcar token como usado
        $stmt = $pdo->prepare('UPDATE password_resets SET used = 1 WHERE token = :token');
        $stmt->execute(['token' => $token]);

        return true;
    }

    /**
     * Limpia tokens expirados (mantenimiento)
     */
    public static function cleanExpiredTokens(): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE expires_at < NOW() OR used = 1');
        $stmt->execute();
    }
}
