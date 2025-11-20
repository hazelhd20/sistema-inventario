<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class InventoryController extends Controller
{
    public function index(): void
    {
        require_login();

        $search = trim($_GET['q'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        $products = Product::all($search ?: null, null);

        if ($filter === 'low') {
            $products = array_values(array_filter($products, fn ($p) => $p['stock_quantity'] <= $p['min_stock_level']));
        }

        $this->render('inventory/index', [
            'products' => $products,
            'search' => $search,
            'filter' => $filter,
            'message' => flash('success'),
        ]);
    }

    public function adjust(): void
    {
        require_login();
        $id = (int) ($_POST['id'] ?? 0);
        $stock = isset($_POST['stock_quantity']) ? (int) $_POST['stock_quantity'] : null;

        if ($id <= 0 || $stock === null) {
            flash('error', 'Datos inválidos para ajustar inventario.');
            redirect('inventory');
        }

        Product::adjustStock($id, max(0, $stock));
        flash('success', 'Stock actualizado.');
        redirect('inventory');
    }
}
