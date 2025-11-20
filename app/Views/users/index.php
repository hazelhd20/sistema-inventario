<?php
$showForm = (bool) $editingUser;
?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestion de Usuarios</h2>
        <button type="button" id="toggleUserForm"
                class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
            <i data-lucide="plus" class="h-5 w-5 mr-1"></i>
            <span id="toggleUserFormText">Nuevo Usuario</span>
        </button>
    </div>

    <div class="card <?= $showForm ? '' : 'hidden' ?>" id="userFormCard">
        <h3 class="text-lg font-semibold mb-4" id="userFormTitle"><?= $editingUser ? 'Editar Usuario' : 'Agregar Nuevo Usuario' ?></h3>
        <form id="userForm" action="<?= base_url('users/save') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="id" id="user-id" value="<?= $editingUser ? (int) $editingUser['id'] : '' ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                <input type="text" name="name" id="user-name" required minlength="3" value="<?= e($editingUser['name'] ?? '') ?>"
                       class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electronico</label>
                <input type="email" name="email" id="user-email" required value="<?= e($editingUser['email'] ?? '') ?>"
                       class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select name="role" id="user-role" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    <option value="admin" <?= ($editingUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    <option value="employee" <?= ($editingUser['role'] ?? '') === 'employee' ? 'selected' : '' ?>>Empleado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contrasena</label>
                <input type="password" name="password" id="user-password" <?= $editingUser ? '' : 'required' ?> minlength="8"
                       pattern="(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}"
                       title="Minimo 8 caracteres, 1 mayuscula, 1 numero y 1 caracter especial"
                       class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"
                       placeholder="<?= $editingUser ? 'Deja en blanco para mantenerla' : '' ?>">
            </div>
            <div class="md:col-span-2 flex justify-end space-x-3">
                <button type="button" id="cancelUserForm" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                    <?= $editingUser ? 'Actualizar' : 'Guardar' ?>
                </button>
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ultimo Acceso</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $user): ?>
                    <tr class="<?= !$user['active'] ? 'bg-gray-50' : '' ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-pastel flex items-center justify-center mr-3">
                                    <i data-lucide="user" class="h-6 w-6 text-blue-700"></i>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= e($user['name']) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= e($user['email']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $user['role'] === 'admin' ? 'bg-blue-pastel text-blue-800' : 'bg-green-pastel text-green-800' ?>">
                                <?= $user['role'] === 'admin' ? 'Administrador' : 'Empleado' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                <?= $user['last_login'] ? date('d/m/Y', strtotime($user['last_login'])) : 'Nunca' ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $user['active'] ? 'bg-green-pastel text-green-800' : 'bg-gray-200 text-gray-800' ?>">
                                <?= $user['active'] ? 'Activo' : 'Inactivo' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button" class="text-blue-pastel hover:text-blue-700 edit-user" title="Editar"
                                        data-user='<?= htmlspecialchars(json_encode([
                                            'id' => (int) $user['id'],
                                            'name' => $user['name'],
                                            'email' => $user['email'],
                                            'role' => $user['role'],
                                        ]), ENT_QUOTES, 'UTF-8') ?>'>
                                    <i data-lucide="edit" class="h-5 w-5"></i>
                                </button>
                                <?php if ($user['id'] != 1): ?>
                                    <form action="<?= base_url('users/toggle') ?>" method="POST">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit" class="<?= $user['active'] ? 'text-green-pastel hover:text-green-700' : 'text-pink-pastel hover:text-pink-700' ?>" title="<?= $user['active'] ? 'Desactivar' : 'Activar' ?>">
                                            <i data-lucide="<?= $user['active'] ? 'user-check' : 'user-x' ?>" class="h-5 w-5"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function() {
        const formCard = document.getElementById('userFormCard');
        const toggleBtn = document.getElementById('toggleUserForm');
        const toggleText = document.getElementById('toggleUserFormText');
        const cancelBtn = document.getElementById('cancelUserForm');
        const title = document.getElementById('userFormTitle');
        const idField = document.getElementById('user-id');
        const nameField = document.getElementById('user-name');
        const emailField = document.getElementById('user-email');
        const roleField = document.getElementById('user-role');
        const passwordField = document.getElementById('user-password');

        if (!formCard || !toggleBtn || !toggleText || !cancelBtn || !title || !idField || !nameField || !emailField || !roleField || !passwordField) {
            return;
        }

        const setPasswordRequirement = (required) => {
            if (required) {
                passwordField.setAttribute('required', 'required');
                passwordField.placeholder = '';
            } else {
                passwordField.removeAttribute('required');
                passwordField.placeholder = 'Deja en blanco para mantenerla';
            }
        };

        const resetForm = () => {
            title.textContent = 'Agregar Nuevo Usuario';
            idField.value = '';
            nameField.value = '';
            emailField.value = '';
            roleField.value = 'admin';
            passwordField.value = '';
            setPasswordRequirement(true);
        };

        const openForm = () => {
            formCard.classList.remove('hidden');
            toggleText.textContent = 'Cerrar formulario';
            toggleBtn.blur();
        };

        const closeForm = () => {
            formCard.classList.add('hidden');
            toggleText.textContent = 'Nuevo Usuario';
            resetForm();
        };

        if (!formCard.classList.contains('hidden')) {
            toggleText.textContent = 'Cerrar formulario';
            if (idField.value) {
                setPasswordRequirement(false);
            }
        }

        toggleBtn.addEventListener('click', () => {
            if (formCard.classList.contains('hidden')) {
                openForm();
            } else {
                closeForm();
            }
        });

        cancelBtn.addEventListener('click', () => {
            closeForm();
        });

        document.querySelectorAll('.edit-user').forEach(btn => {
            btn.addEventListener('click', () => {
                const user = JSON.parse(btn.dataset.user);
                title.textContent = 'Editar Usuario';
            idField.value = user.id || '';
            nameField.value = user.name || '';
            emailField.value = user.email || '';
            roleField.value = user.role || 'admin';
            passwordField.value = '';
            setPasswordRequirement(false);
            openForm();
        });
        });
    })();
</script>
