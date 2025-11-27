<?php
$showForm = (bool) $editingUser;
$isDefaultAdmin = $editingUser && (int) $editingUser['id'] === 1;
?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Usuarios</h2>
            <p class="text-sm text-slate-500 mt-1">Gestión de cuentas de usuario</p>
        </div>
        <button type="button" id="toggleUserForm"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg font-medium text-sm hover:bg-pastel-blue/80 transition-colors">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span id="toggleUserFormText">Nuevo Usuario</span>
        </button>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-soft w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Usuario</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Correo</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Rol</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Último acceso</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-50/50 <?= !$user['active'] ? 'bg-slate-50/50' : '' ?>">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-pastel-blue flex items-center justify-center">
                                        <i data-lucide="user" class="h-5 w-5 text-slate-600"></i>
                                    </div>
                                    <span class="font-medium text-slate-800"><?= e($user['name']) ?></span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= e($user['email']) ?></td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    <?= $user['role'] === 'admin' ? 'bg-pastel-blue text-slate-700' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= $user['role'] === 'admin' ? 'Admin' : 'Empleado' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <?= $user['last_login'] ? date('d/m/Y', strtotime($user['last_login'])) : 'Nunca' ?>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    <?= $user['active'] ? 'bg-pastel-mint text-slate-700' : 'bg-slate-200 text-slate-600' ?>">
                                    <?= $user['active'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors edit-user"
                                            data-user='<?= htmlspecialchars(json_encode([
                                                'id' => (int) $user['id'],
                                                'name' => $user['name'],
                                                'email' => $user['email'],
                                                'role' => $user['role'],
                                            ]), ENT_QUOTES, 'UTF-8') ?>'>
                                        <i data-lucide="edit" class="h-3.5 w-3.5"></i>
                                        Editar
                                    </button>
                                    <?php if ($user['id'] != 1): ?>
                                        <form action="<?= base_url('users/toggle') ?>" method="POST">
                                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                            <?php if ($user['active']): ?>
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-peach text-slate-700 hover:bg-pastel-peach/80 transition-colors">
                                                    <i data-lucide="user-x" class="h-3.5 w-3.5"></i>
                                                    Desactivar
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-mint text-slate-700 hover:bg-pastel-mint/80 transition-colors">
                                                    <i data-lucide="user-check" class="h-3.5 w-3.5"></i>
                                                    Activar
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($users)): ?>
            <div class="text-center py-16">
                <i data-lucide="users" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500">No se encontraron usuarios</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Usuario -->
<div id="userModal" class="<?= $showForm ? '' : 'hidden' ?> fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" id="userModalContent">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-semibold text-slate-800" id="userFormTitle"><?= $editingUser ? 'Editar Usuario' : 'Nuevo Usuario' ?></h3>
                <p class="text-sm text-slate-500">La contraseña solo es obligatoria al crear</p>
            </div>
            <button type="button" id="closeUserModal" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <form id="userForm" action="<?= base_url('users/save') ?>" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="id" id="user-id" value="<?= $editingUser ? (int) $editingUser['id'] : '' ?>">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="user-name" required minlength="3" value="<?= e($editingUser['name'] ?? '') ?>"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Correo <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="user-email" required value="<?= e($editingUser['email'] ?? '') ?>"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Rol</label>
                <?php if ($isDefaultAdmin): ?>
                    <input type="hidden" name="role" value="admin">
                    <select id="user-role" disabled class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm bg-slate-50 text-slate-500">
                        <option value="admin" selected>Administrador</option>
                    </select>
                <?php else: ?>
                    <select name="role" id="user-role" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                        <option value="admin" <?= ($editingUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
                        <option value="employee" <?= ($editingUser['role'] ?? '') === 'employee' ? 'selected' : '' ?>>Empleado</option>
                    </select>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Contraseña <?= $editingUser ? '' : '<span class="text-red-500">*</span>' ?></label>
                <div class="relative">
                    <input type="password" name="password" id="user-password" <?= $editingUser ? '' : 'required' ?> minlength="8"
                           placeholder="<?= $editingUser ? 'Dejar vacío para mantener' : '' ?>"
                           class="w-full px-3 py-2.5 pr-10 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                    <button type="button" id="toggle-user-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i data-lucide="eye" class="h-5 w-5"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" id="cancelUserForm" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg text-sm font-medium hover:bg-pastel-blue/80">
                    <?= $editingUser ? 'Actualizar' : 'Guardar' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('userModal');
    const toggleBtn = document.getElementById('toggleUserForm');
    const toggleText = document.getElementById('toggleUserFormText');
    const closeBtn = document.getElementById('closeUserModal');
    const cancelBtn = document.getElementById('cancelUserForm');
    const title = document.getElementById('userFormTitle');
    const fields = {
        id: document.getElementById('user-id'),
        name: document.getElementById('user-name'),
        email: document.getElementById('user-email'),
        role: document.getElementById('user-role'),
        password: document.getElementById('user-password')
    };
    const togglePasswordBtn = document.getElementById('toggle-user-password');

    if (!modal || !toggleBtn) return;
    if (modal.parentElement !== document.body) document.body.appendChild(modal);

    const resetForm = () => {
        title.textContent = 'Nuevo Usuario';
        fields.id.value = '';
        fields.name.value = '';
        fields.email.value = '';
        if (fields.role && !fields.role.disabled) fields.role.value = 'admin';
        fields.password.value = '';
        fields.password.setAttribute('required', 'required');
        fields.password.placeholder = '';
    };

    const openModal = () => { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
    const closeModal = () => { modal.classList.add('hidden'); document.body.style.overflow = ''; resetForm(); };

    const fillForm = (u) => {
        title.textContent = 'Editar Usuario';
        fields.id.value = u.id || '';
        fields.name.value = u.name || '';
        fields.email.value = u.email || '';
        if (fields.role && !fields.role.disabled) fields.role.value = u.role || 'admin';
        fields.password.value = '';
        fields.password.removeAttribute('required');
        fields.password.placeholder = 'Dejar vacío para mantener';
    };

    toggleBtn.addEventListener('click', () => { resetForm(); openModal(); });
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => e.target === modal && closeModal());
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeModal());

    togglePasswordBtn?.addEventListener('click', () => {
        const isHidden = fields.password.type === 'password';
        fields.password.type = isHidden ? 'text' : 'password';
        const icon = togglePasswordBtn.querySelector('i');
        if (icon) {
            icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
            if (window.lucide) lucide.createIcons();
        }
    });

    document.querySelectorAll('.edit-user').forEach(btn => {
        btn.addEventListener('click', () => { fillForm(JSON.parse(btn.dataset.user)); openModal(); });
    });

    const initial = <?= $editingUser ? json_encode([
        'id' => (int) $editingUser['id'],
        'name' => $editingUser['name'],
        'email' => $editingUser['email'],
        'role' => $editingUser['role'],
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : 'null' ?>;
    if (initial) { fillForm(initial); openModal(); }
})();
</script>
