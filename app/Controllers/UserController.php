<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        require_admin();

        $editId = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
        $editingUser = $editId ? User::find($editId) : null;

        $this->render('users/index', [
            'users' => User::all(),
            'editingUser' => $editingUser,
            'message' => flash('success'),
            'error' => flash('error'),
        ]);
    }

    public function save(): void
    {
        require_admin();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role' => $_POST['role'] ?? 'employee',
            'password' => trim($_POST['password'] ?? ''),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        if ($data['name'] === '' || $data['email'] === '') {
            flash('error', 'Nombre y correo son obligatorios.');
            redirect('users');
        }

        if ($id) {
            User::update($id, $data);
            flash('success', 'Usuario actualizado.');
        } else {
            if ($data['password'] === '') {
                flash('error', 'La contraseña es obligatoria para un nuevo usuario.');
                redirect('users');
            }

            User::create($data);
            flash('success', 'Usuario creado.');
        }

        redirect('users');
    }

    public function delete(): void
    {
        require_admin();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 1) {
            flash('error', 'No puedes eliminar al administrador principal.');
            redirect('users');
        }

        User::delete($id);
        flash('success', 'Usuario eliminado.');
        redirect('users');
    }

    public function toggle(): void
    {
        require_admin();
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 1) {
            flash('error', 'No puedes desactivar al administrador principal.');
            redirect('users');
        }

        User::toggleStatus($id);
        flash('success', 'Estado actualizado.');
        redirect('users');
    }
}
