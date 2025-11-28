<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\InventoryController;
use App\Controllers\MovementController;
use App\Controllers\ProductController;
use App\Controllers\ReportController;
use App\Controllers\UserController;
use App\Core\Router;

require __DIR__ . '/../app/bootstrap.php';

$router = new Router();

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Recuperación de contraseña
$router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password', [AuthController::class, 'showResetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);

$router->get('/', [DashboardController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->post('/products/save', [ProductController::class, 'save']);
$router->post('/products/deactivate', [ProductController::class, 'deactivate']);
$router->post('/products/reactivate', [ProductController::class, 'reactivate']);
$router->post('/products/delete', [ProductController::class, 'delete']);
$router->post('/categories/save', [CategoryController::class, 'save']);

$router->get('/inventory', [InventoryController::class, 'index']);
$router->post('/inventory/adjust', [InventoryController::class, 'adjust']);

$router->get('/movements', [MovementController::class, 'index']);
$router->post('/movements/save', [MovementController::class, 'save']);
$router->get('/movements/pending', [MovementController::class, 'pending']);
$router->post('/movements/approve', [MovementController::class, 'approve']);
$router->post('/movements/reject', [MovementController::class, 'reject']);

$router->get('/reports', [ReportController::class, 'index']);

$router->get('/users', [UserController::class, 'index']);
$router->post('/users/save', [UserController::class, 'save']);
$router->post('/users/delete', [UserController::class, 'delete']);
$router->post('/users/toggle', [UserController::class, 'toggle']);

$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
