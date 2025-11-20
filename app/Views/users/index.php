<?php
$showForm = isset($_GET['show_form']) || $editingUser;
?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestión de Usuarios</h2>
        <a href="<?= base_url($showForm ? 'users' : 'users?show_form=1#form') ?>"
           class="mt-3 sm:mt-0 flex items-center px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
            <i data-lucide="plus" class="h-5 w-5 mr-1"></i>
            <?= $showForm ? 'Cerrar formulario' : 'Nuevo Usuario' ?>
        </a>
    </div>

    <?php if ($showForm): ?>
        <div class="card" id="form">
            <h3 class="text-lg font-semibold mb-4"><?= $editingUser ? 'Editar Usuario' : 'Agregar Nuevo Usuario' ?></h3>
            <form action="<?= base_url('users/save') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php if ($editingUser): ?>
                    <input type="hidden" name="id" value="<?= (int) $editingUser['id'] ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                    <input type="text" name="name" required value="<?= e($editingUser['name'] ?? '') ?>"
                           class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" required value="<?= e($editingUser['email'] ?? '') ?>"
                           class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select name="role" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        <option value="admin" <?= ($editingUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <option value="employee" <?= ($editingUser['role'] ?? '') === 'employee' ? 'selected' : '' ?>>Empleado</option>
                    </select>
                </div>
                <?php if (!$editingUser): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input type="password" name="password" required
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                <?php else: ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                        <input type="password" name="password"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"
                               placeholder="Deja en blanco para mantenerla">
                    </div>
                <?php endif; ?>
                <div class="flex items-center space-x-2 mt-2">
                    <input type="checkbox" name="active" value="1" <?= ($editingUser['active'] ?? 1) ? 'checked' : '' ?> class="h-4 w-4 text-blue-pastel">
                    <label class="text-sm text-gray-700">Usuario activo</label>
                </div>
                <div class="md:col-span-2 flex justify-end space-x-3">
                    <a href="<?= base_url('users') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                        <?= $editingUser ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Último Acceso</th>
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
                            <form action="<?= base_url('users/toggle') ?>" method="POST">
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <button type="submit" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $user['active'] ? 'bg-green-pastel text-green-800' : 'bg-gray-200 text-gray-800' ?>" <?= $user['id'] == 1 ? 'disabled' : '' ?>>
                                    <?= $user['active'] ? 'Activo' : 'Inactivo' ?>
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="<?= base_url('users?edit=' . (int) $user['id'] . '#form') ?>" class="text-blue-pastel hover:text-blue-700">
                                    <i data-lucide="edit" class="h-5 w-5"></i>
                                </a>
                                <?php if ($user['id'] != 1): ?>
                                    <form action="<?= base_url('users/delete') ?>" method="POST" onsubmit="return confirm('¿Eliminar este usuario?');">
                                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                        <button type="submit" class="text-pink-pastel hover:text-pink-700">
                                            <i data-lucide="trash" class="h-5 w-5"></i>
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
