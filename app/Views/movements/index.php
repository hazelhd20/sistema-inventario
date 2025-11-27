<?php $showForm = false; ?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Movimientos</h2>
            <p class="text-sm text-slate-500 mt-1">Historial de entradas y salidas de inventario</p>
        </div>
        <button type="button" id="toggleMovementForm"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg font-medium text-sm hover:bg-pastel-blue/80 transition-colors">
            <i data-lucide="plus" class="h-4 w-4"></i>
            <span id="toggleMovementFormText">Nuevo Movimiento</span>
        </button>
    </div>

    <!-- Aviso -->
    <div class="flex items-center gap-3 px-4 py-3 bg-pastel-blue/30 border border-pastel-blue rounded-lg text-sm text-slate-700">
        <i data-lucide="info" class="h-4 w-4 shrink-0"></i>
        <p>Los movimientos nuevos quedan pendientes hasta que un administrador los apruebe.</p>
    </div>

    <!-- Filtros -->
    <form method="GET" action="<?= base_url('movements') ?>" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
            <input id="movement-search" type="text" name="q" placeholder="Buscar movimientos..." value="<?= e($filters['search'] ?? '') ?>"
                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            <button type="button" id="clear-movement-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="flex gap-2 flex-wrap">
            <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white">
                <?php
                $typeOptions = ['all' => 'Todos', 'in' => 'Entradas', 'out' => 'Salidas'];
                foreach ($typeOptions as $key => $label):
                    $active = ($filters['type'] ?? 'all') === $key;
                ?>
                    <a href="<?= base_url('movements?type=' . $key . '&range=' . e($filters['date_range'])) ?>"
                       class="px-3 py-2 text-xs font-medium <?= $active ? 'bg-pastel-blue text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?> <?= $key !== 'all' ? 'border-l border-slate-200' : '' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white">
                <?php
                $rangeOptions = ['all' => 'Todo', 'today' => 'Hoy', 'week' => 'Semana', 'month' => 'Mes'];
                foreach ($rangeOptions as $key => $label):
                    $active = ($filters['date_range'] ?? 'all') === $key;
                ?>
                    <a href="<?= base_url('movements?range=' . $key . '&type=' . e($filters['type'])) ?>"
                       class="px-3 py-2 text-xs font-medium <?= $active ? 'bg-pastel-peach text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?> <?= $key !== 'all' ? 'border-l border-slate-200' : '' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </form>

    <!-- Tabla -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-soft w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipo</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cantidad</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Notas</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($movements as $movement): ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-4">
                                <p class="text-slate-700"><?= date('d/m/Y', strtotime($movement['date'])) ?></p>
                                <p class="text-xs text-slate-400"><?= date('H:i', strtotime($movement['date'])) ?></p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800"><?= e($movement['product_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= e($movement['product_category']) ?></p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                                    <?= $movement['type'] === 'in' ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700' ?>">
                                    <i data-lucide="<?= $movement['type'] === 'in' ? 'arrow-up' : 'arrow-down' ?>" class="h-3 w-3"></i>
                                    <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-800"><?= (int) $movement['quantity'] ?></td>
                            <td class="px-5 py-4 text-slate-600"><?= e($movement['notes'] ?: '-') ?></td>
                            <td class="px-5 py-4 text-slate-600"><?= e($movement['user_name'] ?: 'Sistema') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($movements)): ?>
            <div class="text-center py-16">
                <i data-lucide="repeat" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500">No se encontraron movimientos</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Movimiento -->
<div id="movementModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" id="movementModalContent">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Registrar Movimiento</h3>
                <p class="text-sm text-slate-500">Campos con <span class="text-red-500">*</span> son obligatorios</p>
            </div>
            <button type="button" id="closeMovementModal" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <form id="movementForm" action="<?= base_url('movements/save') ?>" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Producto <span class="text-red-500">*</span></label>
                <select name="product_id" id="movement-product" required
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= (int) $product['id'] ?>">
                            <?= e($product['name']) ?> (Stock: <?= (int) $product['stock_quantity'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipo</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="in" checked class="w-4 h-4 text-pastel-blue focus:ring-pastel-blue">
                        <span class="text-sm text-slate-700">Entrada</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="out" class="w-4 h-4 text-pastel-rose focus:ring-pastel-rose">
                        <span class="text-sm text-slate-700">Salida</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Cantidad <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" id="movement-quantity" min="1" required value="1"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Notas</label>
                <input type="text" name="notes" id="movement-notes" maxlength="255" placeholder="Motivo del movimiento"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" id="cancelMovementForm" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg text-sm font-medium hover:bg-pastel-blue/80">
                    Registrar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const searchInput = document.getElementById('movement-search');
    const clearBtn = document.getElementById('clear-movement-search');
    if (!searchInput || !clearBtn) return;
    const form = searchInput.closest('form');
    let debounceId;
    const submitForm = () => form?.requestSubmit?.() || form?.submit();
    const toggle = () => clearBtn.classList.toggle('hidden', !searchInput.value);
    toggle();
    searchInput.addEventListener('input', () => {
        toggle();
        clearTimeout(debounceId);
        debounceId = setTimeout(submitForm, 400);
    });
    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        toggle();
        submitForm();
    });
})();

(function() {
    const modal = document.getElementById('movementModal');
    const toggleBtn = document.getElementById('toggleMovementForm');
    const closeBtn = document.getElementById('closeMovementModal');
    const cancelBtn = document.getElementById('cancelMovementForm');

    if (!modal || !toggleBtn) return;
    if (modal.parentElement !== document.body) document.body.appendChild(modal);

    const openModal = () => { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
    const closeModal = () => { modal.classList.add('hidden'); document.body.style.overflow = ''; };

    toggleBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => e.target === modal && closeModal());
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeModal());
})();
</script>
