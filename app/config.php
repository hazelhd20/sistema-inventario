<?php
return [
    'app' => [
        'name' => 'Sistema de Inventarios',
        // Ajusta esta URL base si sirves el proyecto en un subdirectorio diferente.
        'base_url' => '', // ejemplo: '/sistema-inventario/public'
        // URL completa del sitio (necesaria para los enlaces de recuperación)
        'url' => 'http://localhost/sistema-inventario/public',
    ],
    'db' => [
        'host' => 'localhost',
        'name' => 'sistema_inventario',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    // Configuración SMTP para envío de correos (ajusta con los datos de tu hosting)
    'mail' => [
        'host' => 'mail.hazelhd.com',      // Servidor SMTP del hosting
        'port' => 465,                        // Puerto (587 para TLS, 465 para SSL)
        'username' => 'no-reply@hazelhd.com', // Usuario SMTP
        'password' => 'esDECczn*HkOZe-Y',   // Contraseña SMTP
        'encryption' => 'ssl',                // 'tls' o 'ssl'
        'from_email' => 'no-reply@hazelhd.com',
        'from_name' => 'Sistema de Inventarios',
    ],
];
