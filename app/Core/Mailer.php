<?php
declare(strict_types=1);

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $mailConfig = config('mail');

        // Configuración del servidor SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = $mailConfig['host'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $mailConfig['username'];
        $this->mailer->Password = $mailConfig['password'];
        $this->mailer->Port = $mailConfig['port'];

        // Configurar encriptación
        if ($mailConfig['encryption'] === 'tls') {
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($mailConfig['encryption'] === 'ssl') {
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        }

        // Configuración del remitente
        $this->mailer->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->isHTML(true);
    }

    /**
     * Envía un correo de recuperación de contraseña
     */
    public function sendPasswordReset(string $email, string $name, string $resetLink): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($email, $name);

            $appName = config('app.name', 'Sistema de Inventarios');
            $this->mailer->Subject = "Recuperación de contraseña - {$appName}";

            $this->mailer->Body = $this->getPasswordResetTemplate($name, $resetLink, $appName);
            $this->mailer->AltBody = $this->getPasswordResetPlainText($name, $resetLink);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo de recuperación: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Plantilla HTML para el correo de recuperación
     */
    private function getPasswordResetTemplate(string $name, string $resetLink, string $appName): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #A8D8EA 0%, #B8E0D2 100%); padding: 32px; text-align: center;">
                            <h1 style="margin: 0; color: #334155; font-size: 24px; font-weight: 600;">{$appName}</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 32px;">
                            <h2 style="margin: 0 0 16px; color: #1e293b; font-size: 20px; font-weight: 600;">
                                Hola, {$name}
                            </h2>
                            <p style="margin: 0 0 24px; color: #64748b; font-size: 15px; line-height: 1.6;">
                                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. 
                                Si no realizaste esta solicitud, puedes ignorar este correo.
                            </p>
                            
                            <!-- Button -->
                            <table role="presentation" style="margin: 32px 0;">
                                <tr>
                                    <td style="border-radius: 8px; background-color: #A8D8EA;">
                                        <a href="{$resetLink}" target="_blank" style="display: inline-block; padding: 14px 32px; color: #334155; text-decoration: none; font-weight: 600; font-size: 15px;">
                                            Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 0 0 16px; color: #64748b; font-size: 14px; line-height: 1.6;">
                                O copia y pega el siguiente enlace en tu navegador:
                            </p>
                            <p style="margin: 0 0 24px; padding: 12px; background-color: #f1f5f9; border-radius: 8px; word-break: break-all;">
                                <a href="{$resetLink}" style="color: #3b82f6; font-size: 13px; text-decoration: none;">{$resetLink}</a>
                            </p>
                            
                            <!-- Warning -->
                            <div style="padding: 16px; background-color: #FFF7ED; border-left: 4px solid #FFD8B5; border-radius: 0 8px 8px 0;">
                                <p style="margin: 0; color: #9a3412; font-size: 14px; font-weight: 500;">
                                    ⚠️ Este enlace expirará en 10 minutos
                                </p>
                                <p style="margin: 8px 0 0; color: #c2410c; font-size: 13px;">
                                    Por seguridad, el enlace solo puede usarse una vez.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 32px; background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; color: #94a3b8; font-size: 13px; text-align: center;">
                                Este correo fue enviado automáticamente. Por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Versión texto plano del correo
     */
    private function getPasswordResetPlainText(string $name, string $resetLink): string
    {
        return <<<TEXT
Hola, {$name}

Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.

Para restablecer tu contraseña, visita el siguiente enlace:
{$resetLink}

IMPORTANTE: Este enlace expirará en 10 minutos y solo puede usarse una vez.

Si no realizaste esta solicitud, puedes ignorar este correo.

---
Este correo fue enviado automáticamente. Por favor no respondas a este mensaje.
TEXT;
    }
}

