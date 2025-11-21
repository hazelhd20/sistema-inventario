<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (is_logged_in()) {
            redirect('/');
        }

        $this->render('auth/login', [
            'error' => flash('error'),
        ], null);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        store_old(['email' => $email]);

        if ($email === '' || $password === '') {
            flash('error', 'Debes ingresar tus credenciales.');
            redirect('login');
        }

        $user = User::findByEmail($email);

        if ($user && !(bool) $user['active']) {
            flash('error', 'Tu cuenta esta inactiva. Contacta al administrador para reactivarla.');
            redirect('login');
        }

        $maxAttempts = 5;
        $lockoutSeconds = 300;
        $attemptKey = strtolower($email);
        $attempt = $_SESSION['login_attempts'][$attemptKey] ?? ['count' => 0, 'locked_until' => 0];

        if ($attempt['locked_until'] > time()) {
            $remaining = $attempt['locked_until'] - time();
            $minutes = ceil($remaining / 60);
            flash('error', "Demasiados intentos fallidos. Intenta nuevamente en {$minutes} minuto(s).");
            redirect('login');
        }

        $isValidPassword = $user && password_verify($password, $user['password']);
        if (!$user || !$isValidPassword) {
            $attempt['count']++;
            if ($attempt['count'] >= $maxAttempts) {
                $attempt['locked_until'] = time() + $lockoutSeconds;
            }
            $_SESSION['login_attempts'][$attemptKey] = $attempt;

            $remainingAttempts = max(0, $maxAttempts - $attempt['count']);
            $message = $attempt['locked_until'] > time()
                ? 'Demasiados intentos fallidos. Intenta nuevamente mas tarde.'
                : 'Credenciales invalidas.';

            if ($remainingAttempts > 0 && $attempt['locked_until'] <= time()) {
                $message .= " Intentos restantes: {$remainingAttempts}.";
            }

            flash('error', $message);
            redirect('login');
        }

        $cleanUser = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        $_SESSION['user'] = $cleanUser;
        User::updateLastLogin($cleanUser['id']);
        unset($_SESSION['login_attempts'][$attemptKey]);
        clear_old();
        redirect('/');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('login');
    }
}
