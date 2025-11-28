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

        $currentUser = auth_user();
        $isAdmin = ($currentUser['role'] ?? null) === 'admin';

        $search = trim($_GET['q'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        $products = Product::all($search ?: null, null, true);

        if ($filter === 'low') {
            $products = array_values(array_filter($products, fn ($p) => $p['stock_quantity'] <= $p['min_stock_level']));
        }

        // Respuesta AJAX: devolver solo los datos
        if (is_ajax()) {
            json_response([
                'success' => true,
                'products' => $products,
                'filter' => $filter,
                'search' => $search,
                'isAdmin' => $isAdmin,
            ]);
        }

        $this->render('inventory/index', [
            'products' => $products,
            'search' => $search,
            'filter' => $filter,
            'isAdmin' => $isAdmin,
            'message' => flash('success'),
        ]);
    }

    public function adjust(): void
    {
        require_admin_ajax();
        $id = (int) ($_POST['id'] ?? 0);
        $stock = isset($_POST['stock_quantity']) ? (int) $_POST['stock_quantity'] : null;

        if ($id <= 0 || $stock === null) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'Datos inválidos para ajustar inventario.'], 400);
            }
            flash('error', 'Datos invalidos para ajustar inventario.');
            redirect('inventory');
        }

        $product = Product::find($id);
        if (!$product || empty($product['active'])) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'El producto no existe o está inactivo.'], 404);
            }
            flash('error', 'El producto no existe o esta inactivo.');
            redirect('inventory');
        }

        $newStock = max(0, $stock);
        Product::adjustStock($id, $newStock);

        // Respuesta AJAX
        if (is_ajax()) {
            $isLow = $newStock <= $product['min_stock_level'];
            json_response([
                'success' => true,
                'message' => 'Stock actualizado correctamente.',
                'product' => [
                    'id' => $id,
                    'stock_quantity' => $newStock,
                    'min_stock_level' => (int) $product['min_stock_level'],
                    'is_low' => $isLow,
                ],
            ]);
        }

        flash('success', 'Stock actualizado.');
        redirect('inventory');
    }
}
