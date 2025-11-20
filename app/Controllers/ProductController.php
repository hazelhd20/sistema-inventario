<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(): void
    {
        require_login();

        $search = trim($_GET['q'] ?? '');
        $category = $_GET['category'] ?? null;
        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $editingProduct = $editId ? Product::find($editId) : null;

        $products = Product::all($search ?: null, $category ?: null);
        $categories = array_values(array_unique(array_map(fn ($p) => $p['category'], $products)));

        $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'editingProduct' => $editingProduct,
            'message' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function save(): void
    {
        require_login();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category' => trim($_POST['category'] ?? 'General'),
            'price' => (float) ($_POST['price'] ?? 0),
            'cost' => (float) ($_POST['cost'] ?? 0),
            'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
            'min_stock_level' => (int) ($_POST['min_stock_level'] ?? 0),
            'image_url' => trim($_POST['image_url'] ?? ''),
        ];

        if ($data['name'] === '') {
            flash('error', 'El nombre del producto es requerido.');
            store_old($_POST);
            redirect('products');
        }

        if ($id) {
            Product::update($id, $data);
            flash('success', 'Producto actualizado correctamente.');
        } else {
            Product::create($data);
            flash('success', 'Producto creado correctamente.');
        }

        redirect('products');
    }

    public function delete(): void
    {
        require_login();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('error', 'ID inválido.');
            redirect('products');
        }

        Product::delete($id);
        flash('success', 'Producto eliminado.');
        redirect('products');
    }
}
