<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function save(): void
    {
        require_admin();

        $name = trim($_POST['name'] ?? '');

        if ($name === '' || strlen($name) < 3) {
            flash('error', 'El nombre de la categoria es requerido y debe tener al menos 3 caracteres.');
            redirect('products');
        }

        if (Category::findByName($name)) {
            flash('error', 'Ya existe una categoria con ese nombre.');
            redirect('products');
        }

        Category::create($name);
        flash('success', 'Categoria creada.');
        redirect('products');
    }
}
