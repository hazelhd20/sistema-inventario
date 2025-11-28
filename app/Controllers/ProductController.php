<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(): void
    {
        require_login();

        $currentUser = auth_user();
        $isAdmin = ($currentUser['role'] ?? null) === 'admin';

        $search = trim($_GET['q'] ?? '');
        $category = isset($_GET['category']) ? (int) $_GET['category'] : null;
        if ($category !== null && $category <= 0) {
            $category = null;
        }
        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $editingProduct = ($isAdmin && $editId) ? Product::find($editId) : null;

        $products = Product::all($search ?: null, $category ?: null);
        $categories = Category::all();

        // Respuesta AJAX
        if (is_ajax()) {
            json_response([
                'success' => true,
                'products' => $products,
                'categories' => $categories,
                'search' => $search,
                'isAdmin' => $isAdmin,
            ]);
        }

        $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'editingProduct' => $editingProduct,
            'isAdmin' => $isAdmin,
            'message' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function save(): void
    {
        require_admin_ajax();

        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int) $_POST['id'] : null;
        $rawPrice = $_POST['price'] ?? null;
        $rawCost = $_POST['cost'] ?? null;
        $rawStock = $_POST['stock_quantity'] ?? null;
        $rawMin = $_POST['min_stock_level'] ?? null;
        $imageInput = $_POST['image_url'] ?? null;
        $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category_id' => $categoryId,
            'price' => (float) ($_POST['price'] ?? 0),
            'cost' => (float) ($_POST['cost'] ?? 0),
            'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
            'min_stock_level' => (int) ($_POST['min_stock_level'] ?? 0),
            'image_url' => $imageInput !== null ? trim((string) $imageInput) : null,
        ];

        $isNumeric = static fn ($value): bool => is_numeric($value);

        // Validaciones con soporte AJAX
        $validationError = function (string $message) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => $message], 400);
            }
            flash('error', $message);
            store_old($_POST);
            redirect('products');
        };

        if ($data['name'] === '' || strlen($data['name']) < 3) {
            $validationError('El nombre del producto es requerido y debe tener al menos 3 caracteres.');
        }

        if ($data['category_id'] <= 0 || !Category::find($data['category_id'])) {
            $validationError('Seleccione una categoría válida.');
        }

        if (!$isNumeric($rawPrice) || $data['price'] < 0) {
            $validationError('El precio debe ser un número mayor o igual a 0.');
        }

        if (!$isNumeric($rawCost) || $data['cost'] < 0) {
            $validationError('El costo debe ser un número mayor o igual a 0.');
        }

        if (!$isNumeric($rawStock) || $data['stock_quantity'] < 0) {
            $validationError('La cantidad en stock debe ser un número mayor o igual a 0.');
        }

        if (!$isNumeric($rawMin) || $data['min_stock_level'] < 0) {
            $validationError('El mínimo de stock debe ser un número mayor o igual a 0.');
        }

        if ($id && $imageInput === null) {
            $existing = Product::find($id);
            $data['image_url'] = $existing['image_url'] ?? null;
        }

        if ($id) {
            Product::update($id, $data);
            $message = 'Producto actualizado correctamente.';
        } else {
            $id = Product::create($data);
            $message = 'Producto creado correctamente.';
        }

        // Respuesta AJAX
        if (is_ajax()) {
            $product = Product::find($id);
            json_response([
                'success' => true,
                'message' => $message,
                'product' => $product,
            ]);
        }

        flash('success', $message);
        redirect('products');
    }

    public function delete(): void
    {
        require_admin_ajax();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'ID inválido.'], 400);
            }
            flash('error', 'ID invalido.');
            redirect('products');
        }

        $product = Product::find($id);
        if (!$product) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'Producto no encontrado.'], 404);
            }
            flash('error', 'Producto no encontrado.');
            redirect('products');
        }

        if (Product::hasMovements($id)) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'Este producto no puede eliminarse porque tiene transacciones registradas.'], 400);
            }
            flash('error', 'Este producto no puede eliminarse porque tiene transacciones registradas.');
            redirect('products');
        }

        $deleted = Product::delete($id);
        if ($deleted) {
            if (is_ajax()) {
                json_response(['success' => true, 'message' => 'Producto eliminado.', 'product_id' => $id]);
            }
            flash('success', 'Producto eliminado.');
        } else {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'No se pudo eliminar el producto.'], 500);
            }
            flash('error', 'No se pudo eliminar el producto.');
        }

        redirect('products');
    }

    public function deactivate(): void
    {
        require_admin_ajax();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'ID inválido.'], 400);
            }
            flash('error', 'ID invalido.');
            redirect('products');
        }

        $product = Product::find($id);
        if (!$product) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'Producto no encontrado.'], 404);
            }
            flash('error', 'Producto no encontrado.');
            redirect('products');
        }

        Product::deactivate($id);

        if (is_ajax()) {
            json_response(['success' => true, 'message' => 'Producto inactivado.', 'product_id' => $id, 'active' => false]);
        }

        flash('success', 'Producto inactivado.');
        redirect('products');
    }

    public function reactivate(): void
    {
        require_admin_ajax();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'ID inválido.'], 400);
            }
            flash('error', 'ID invalido.');
            redirect('products');
        }

        $product = Product::find($id);
        if (!$product) {
            if (is_ajax()) {
                json_response(['success' => false, 'message' => 'Producto no encontrado.'], 404);
            }
            flash('error', 'Producto no encontrado.');
            redirect('products');
        }

        Product::activate($id);

        if (is_ajax()) {
            json_response(['success' => true, 'message' => 'Producto reactivado.', 'product_id' => $id, 'active' => true]);
        }

        flash('success', 'Producto reactivado.');
        redirect('products');
    }
}
