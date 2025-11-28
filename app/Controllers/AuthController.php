<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
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
            'success' => flash('success'),
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

    /**
     * Muestra el formulario de solicitud de recuperación de contraseña
     */
    public function showForgotPassword(): void
    {
        if (is_logged_in()) {
            redirect('/');
        }

        $this->render('auth/forgot-password', [
            'error' => flash('error'),
            'success' => flash('success'),
        ], null);
    }

    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function forgotPassword(): void
    {
        $email = trim($_POST['email'] ?? '');

        store_old(['email' => $email]);

        // Validación del email
        if ($email === '') {
            flash('error', 'Debes ingresar tu correo electrónico.');
            redirect('forgot-password');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'El correo electrónico no tiene un formato válido.');
            redirect('forgot-password');
        }

        // Limitar intentos de solicitud (anti-spam)
        $rateLimitKey = 'password_reset_' . md5(strtolower($email));
        $lastRequest = $_SESSION[$rateLimitKey] ?? 0;
        $cooldownSeconds = 60; // 1 minuto entre solicitudes

        if ($lastRequest > (time() - $cooldownSeconds)) {
            $remaining = $cooldownSeconds - (time() - $lastRequest);
            flash('error', "Por favor espera {$remaining} segundos antes de solicitar otro enlace.");
            redirect('forgot-password');
        }

        // Crear token de recuperación
        $token = User::createPasswordResetToken($email);

        // Siempre mostrar mensaje genérico por seguridad (no revelar si el email existe)
        $_SESSION[$rateLimitKey] = time();

        if ($token) {
            // Obtener datos del usuario para el email
            $user = User::findByEmail($email);
            $resetLink = config('app.url') . '/reset-password?token=' . $token;

            // Enviar correo
            $mailer = new Mailer();
            $emailSent = $mailer->sendPasswordReset($email, $user['name'], $resetLink);

            if (!$emailSent) {
                error_log("Error al enviar correo de recuperación a: {$email}");
            }
        }

        // Mensaje genérico para no revelar información
        clear_old();
        flash('success', 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña. Revisa también tu carpeta de spam.');
        redirect('forgot-password');
    }

    /**
     * Muestra el formulario para restablecer la contraseña
     */
    public function showResetPassword(): void
    {
        if (is_logged_in()) {
            redirect('/');
        }

        $token = trim($_GET['token'] ?? '');

        if ($token === '') {
            flash('error', 'Enlace de recuperación inválido.');
            redirect('forgot-password');
        }

        // Validar token
        $resetData = User::validatePasswordResetToken($token);

        if (!$resetData) {
            flash('error', 'El enlace de recuperación ha expirado o ya fue utilizado. Solicita uno nuevo.');
            redirect('forgot-password');
        }

        $this->render('auth/reset-password', [
            'token' => $token,
            'email' => $resetData['email'],
            'error' => flash('error'),
        ], null);
    }

    /**
     * Procesa el restablecimiento de contraseña
     */
    public function resetPassword(): void
    {
        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validaciones
        if ($token === '') {
            flash('error', 'Token de recuperación inválido.');
            redirect('forgot-password');
        }

        // Validar que el token aún sea válido
        $resetData = User::validatePasswordResetToken($token);
        if (!$resetData) {
            flash('error', 'El enlace de recuperación ha expirado o ya fue utilizado. Solicita uno nuevo.');
            redirect('forgot-password');
        }

        if ($password === '' || $passwordConfirm === '') {
            flash('error', 'Debes completar ambos campos de contraseña.');
            redirect('reset-password?token=' . urlencode($token));
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Las contraseñas no coinciden.');
            redirect('reset-password?token=' . urlencode($token));
        }

        // Validar fortaleza de la contraseña
        if (strlen($password) < 8) {
            flash('error', 'La contraseña debe tener al menos 8 caracteres.');
            redirect('reset-password?token=' . urlencode($token));
        }

        if (!preg_match('/[A-Z]/', $password)) {
            flash('error', 'La contraseña debe contener al menos una letra mayúscula.');
            redirect('reset-password?token=' . urlencode($token));
        }

        if (!preg_match('/[a-z]/', $password)) {
            flash('error', 'La contraseña debe contener al menos una letra minúscula.');
            redirect('reset-password?token=' . urlencode($token));
        }

        if (!preg_match('/[0-9]/', $password)) {
            flash('error', 'La contraseña debe contener al menos un número.');
            redirect('reset-password?token=' . urlencode($token));
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            flash('error', 'La contraseña debe contener al menos un carácter especial.');
            redirect('reset-password?token=' . urlencode($token));
        }

        // Resetear la contraseña
        $success = User::resetPasswordWithToken($token, $password);

        if (!$success) {
            flash('error', 'No se pudo restablecer la contraseña. El enlace puede haber expirado.');
            redirect('forgot-password');
        }

        // Limpiar tokens expirados (mantenimiento)
        User::cleanExpiredTokens();

        flash('success', 'Tu contraseña ha sido restablecida exitosamente. Ya puedes iniciar sesión.');
        redirect('login');
    }
}
