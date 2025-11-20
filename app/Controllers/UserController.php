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
        ];

        $isValidPassword = static function (string $password): bool {
            return (bool) preg_match('/^(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$/', $password);
        };

        $data['role'] = in_array($data['role'], ['admin', 'employee'], true) ? $data['role'] : 'employee';

        if ($data['name'] === '' || strlen($data['name']) < 3) {
            flash('error', 'El nombre es obligatorio y debe tener al menos 3 caracteres.');
            redirect('users');
        }

        if ($data['email'] === '' || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Ingresa un correo electronico valido.');
            redirect('users');
        }

        $emailOwner = User::findByEmail($data['email']);
        if ($emailOwner && (!$id || (int) $emailOwner['id'] !== $id)) {
            flash('error', 'El correo ya esta en uso por otro usuario.');
            redirect('users');
        }

        if ($id) {
            $existing = User::find($id);
            if (!$existing) {
                flash('error', 'Usuario no encontrado.');
                redirect('users');
            }
            if ($data['password'] !== '' && !$isValidPassword($data['password'])) {
                flash('error', 'La contrasena debe tener minimo 8 caracteres, una mayuscula, un numero y un caracter especial.');
                redirect('users');
            }
            $data['active'] = (int) $existing['active'];
            User::update($id, $data);
            flash('success', 'Usuario actualizado.');
        } else {
            if ($data['password'] === '' || !$isValidPassword($data['password'])) {
                flash('error', 'La contrasena es obligatoria y debe tener minimo 8 caracteres, una mayuscula, un numero y un caracter especial.');
                redirect('users');
            }

            $data['active'] = 1;
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
