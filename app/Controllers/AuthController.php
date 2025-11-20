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

        $user = User::verifyCredentials($email, $password);
        if (!$user) {
            flash('error', 'Credenciales inválidas o usuario inactivo.');
            redirect('login');
        }

        $_SESSION['user'] = $user;
        User::updateLastLogin($user['id']);
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
