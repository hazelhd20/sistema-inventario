<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/helpers.php';
require __DIR__ . '/autoload.php';
require __DIR__ . '/../vendor/autoload.php'; // PHPMailer

setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');

// Limpia datos antiguos al final del ciclo de vida
register_shutdown_function('clear_old');
