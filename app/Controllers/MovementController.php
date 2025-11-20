<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Movement;
use App\Models\Product;

class MovementController extends Controller
{
    public function index(): void
    {
        require_login();

        $filters = [
            'type' => $_GET['type'] ?? 'all',
            'date_range' => $_GET['range'] ?? 'all',
            'search' => trim($_GET['q'] ?? ''),
        ];

        $normalizedFilters = $filters;
        if ($filters['type'] === 'all') {
            unset($normalizedFilters['type']);
        }
        if ($filters['date_range'] === 'all') {
            unset($normalizedFilters['date_range']);
        }

        $movements = Movement::filtered($normalizedFilters);

        $this->render('movements/index', [
            'products' => Product::all(),
            'movements' => $movements,
            'filters' => $filters,
            'message' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function save(): void
    {
        require_login();
        $user = auth_user();

        $data = [
            'product_id' => (int) ($_POST['product_id'] ?? 0),
            'type' => $_POST['type'] ?? 'in',
            'quantity' => (int) ($_POST['quantity'] ?? 0),
            'notes' => trim($_POST['notes'] ?? ''),
            'user_id' => $user['id'] ?? 0,
        ];

        if ($data['product_id'] <= 0 || $data['quantity'] <= 0) {
            flash('error', 'Seleccione un producto y cantidad válidos.');
            redirect('movements');
        }

        if (!in_array($data['type'], ['in', 'out'], true)) {
            $data['type'] = 'in';
        }

        try {
            Movement::create($data);
            flash('success', 'Movimiento registrado.');
        } catch (\Throwable $e) {
            flash('error', 'No se pudo registrar el movimiento: ' . $e->getMessage());
        }
        redirect('movements');
    }
}
