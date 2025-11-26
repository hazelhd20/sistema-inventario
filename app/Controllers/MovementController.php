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
            'status' => 'approved',
        ];

        $normalizedFilters = $filters;
        if ($filters['type'] === 'all') {
            unset($normalizedFilters['type']);
        }
        if ($filters['date_range'] === 'all') {
            unset($normalizedFilters['date_range']);
        }
        if ($normalizedFilters['status'] === 'approved') {
            unset($normalizedFilters['status']);
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

        if ($data['product_id'] <= 0) {
            flash('error', 'Selecciona un producto válido.');
            redirect('movements');
        }

        if ($data['quantity'] <= 0) {
            flash('error', 'La cantidad debe ser mayor a 0.');
            redirect('movements');
        }

        if (!in_array($data['type'], ['in', 'out'], true)) {
            flash('error', 'Tipo de movimiento inválido.');
            redirect('movements');
        }

        if (strlen($data['notes']) > 255) {
            flash('error', 'Las notas deben tener máximo 255 caracteres.');
            redirect('movements');
        }

        $product = Product::find($data['product_id']);
        if (!$product) {
            flash('error', 'Producto no encontrado.');
            redirect('movements');
        }

        try {
            Movement::create($data);
            flash('success', 'Movimiento registrado como pendiente.');
        } catch (\Throwable $e) {
            flash('error', 'No se pudo registrar el movimiento: ' . $e->getMessage());
        }
        redirect('movements');
    }

    public function pending(): void
    {
        require_admin();

        $filters = [
            'status' => 'pending',
            'type' => $_GET['type'] ?? 'all',
        ];

        $normalizedFilters = $filters;
        if ($filters['type'] === 'all') {
            unset($normalizedFilters['type']);
        }

        $pendingMovements = Movement::filtered($normalizedFilters);

        $this->render('movements/pending', [
            'movements' => $pendingMovements,
            'filters' => $filters,
            'message' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function approve(): void
    {
        require_admin();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'Movimiento invalido.');
            redirect('movements/pending');
        }

        try {
            Movement::approve($id);
            flash('success', 'Movimiento aprobado y stock actualizado.');
        } catch (\Throwable $e) {
            flash('error', 'No se pudo aprobar el movimiento: ' . $e->getMessage());
        }

        redirect('movements/pending');
    }

    public function reject(): void
    {
        require_admin();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'Movimiento invalido.');
            redirect('movements/pending');
        }

        try {
            Movement::reject($id);
            flash('success', 'Movimiento rechazado y eliminado.');
        } catch (\Throwable $e) {
            flash('error', 'No se pudo rechazar el movimiento: ' . $e->getMessage());
        }

        redirect('movements/pending');
    }
}
