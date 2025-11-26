<?php $showForm = false; ?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Movimientos de Inventario</h2>
        <button type="button" id="toggleMovementForm"
                class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
            <i data-lucide="plus" class="h-5 w-5 mr-1"></i>
            <span id="toggleMovementFormText">Nuevo Movimiento</span>
        </button>
    </div>

    <div id="movementModal" class="<?= $showForm ? '' : 'hidden' ?> fixed inset-0 z-40 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm px-4 py-8">
        <div class="card modal-card w-full max-w-3xl relative max-h-[80vh] overflow-y-auto" id="movementModalContent">
            <button type="button" id="closeMovementModal" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700" aria-label="Cerrar">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
            <div class="mb-4 pr-8">
                <h3 class="text-lg font-semibold" id="movementFormTitle">Registrar Movimiento</h3>
                <p class="text-sm text-gray-500">Campos marcados con <span class="text-red-500" aria-hidden="true">*</span> son obligatorios.</p>
            </div>
            <form id="movementForm" action="<?= base_url('movements/save') ?>" method="POST" class="form-modern grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto <span class="text-red-500" aria-hidden="true">*</span></label>
                    <select name="product_id" id="movement-product" required
                            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        <option value="">Seleccionar producto</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= (int) $product['id'] ?>">
                                <?= e($product['name']) ?> (Stock: <?= (int) $product['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimiento</label>
                    <div class="flex">
                        <label class="inline-flex items-center mr-4">
                            <input type="radio" name="type" id="movement-type-in" value="in" checked class="h-4 w-4 text-blue-pastel">
                            <span class="ml-2 text-gray-700">Entrada</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="type" id="movement-type-out" value="out" class="h-4 w-4 text-pink-pastel">
                            <span class="ml-2 text-gray-700">Salida</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="number" name="quantity" id="movement-quantity" min="1" step="1" required value="1"
                           class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                    <input type="text" name="notes" id="movement-notes" maxlength="255"
                           class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"
                           placeholder="Motivo del movimiento">
                </div>
                <div class="md:col-span-2 flex justify-end space-x-3 pt-2">
                    <button type="button" id="cancelMovementForm" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form method="GET" action="<?= base_url('movements') ?>" class="mb-4 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
            </div>
            <input id="movement-search" type="text" name="q" placeholder="Buscar movimientos..." value="<?= e($filters['search'] ?? '') ?>"
                   class="w-full pl-10 pr-10 py-3 border border-white/60 bg-white/80 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-pastel">
            <button type="button" id="clear-movement-search" class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600 focus:outline-none hidden" aria-label="Limpiar busqueda">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="flex space-x-2">
            <div class="inline-flex rounded-xl shadow-sm border border-white/60 overflow-hidden">
                <?php
                $typeOptions = ['all' => 'Todos', 'in' => 'Entradas', 'out' => 'Salidas'];
                foreach ($typeOptions as $key => $label):
                    $active = ($filters['type'] ?? 'all') === $key;
                    ?>
                    <a href="<?= base_url('movements?type=' . $key . '&range=' . e($filters['date_range'])) ?>"
                       class="px-3 py-2.5 text-xs font-semibold <?= $active ? 'bg-blue-pastel text-gray-900' : 'bg-white/80 text-gray-700 hover:bg-gray-50' ?> <?= $key === 'all' ? 'rounded-l-xl' : '' ?> <?= $key === 'out' ? 'rounded-r-xl border-l border-white/60' : '' ?> <?= $key === 'in' ? 'border-l border-white/60' : '' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="inline-flex rounded-xl shadow-sm border border-white/60 overflow-hidden">
                <?php
                $rangeOptions = ['all' => 'Todo', 'today' => 'Hoy', 'week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre'];
                foreach ($rangeOptions as $key => $label):
                    $active = ($filters['date_range'] ?? 'all') === $key;
                    ?>
                    <a href="<?= base_url('movements?range=' . $key . '&type=' . e($filters['type'])) ?>"
                       class="px-3 py-2.5 text-xs font-semibold <?= $active ? 'bg-peach-pastel text-gray-900' : 'bg-white/80 text-gray-700 hover:bg-gray-50' ?> <?= $key === 'all' ? 'rounded-l-xl' : '' ?> <?= $key === 'quarter' ? 'rounded-r-xl border-l border-white/60' : '' ?> <?= !in_array($key, ['all', 'quarter'], true) ? 'border-l border-white/60' : '' ?>">
                        <?= $label ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="table-soft min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($movements as $movement): ?>
                <tr class="hover:bg-gray-50/80 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($movement['date'])) ?>
                            <div class="text-xs text-gray-400"><?= date('H:i', strtotime($movement['date'])) ?></div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= e($movement['product_name']) ?></div>
                        <div class="text-xs text-gray-500"><?= e($movement['product_category']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $movement['type'] === 'in' ? 'bg-green-pastel text-green-800' : 'bg-pink-pastel text-pink-800' ?>">
                            <?php if ($movement['type'] === 'in'): ?>
                                <i data-lucide="arrow-up" class="h-3 w-3 mr-1"></i> Entrada
                            <?php else: ?>
                                <i data-lucide="arrow-down" class="h-3 w-3 mr-1"></i> Salida
                            <?php endif; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= (int) $movement['quantity'] ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900"><?= e($movement['notes'] ?: '-') ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-500">
                            <?= e($movement['user_name'] ?: 'Sistema') ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (empty($movements)): ?>
        <div class="text-center py-8">
            <p class="text-gray-500">No se encontraron movimientos.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    (function() {
        const searchInput = document.getElementById('movement-search');
        const clearBtn = document.getElementById('clear-movement-search');
        if (!searchInput || !clearBtn) return;
        const form = searchInput.closest('form');
        let debounceId;
        const submitForm = () => {
            if (!form) return;
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        };
        const toggle = () => clearBtn.classList[searchInput.value ? 'remove' : 'add']('hidden');
        const restoreFocus = () => {
            searchInput.focus({ preventScroll: true });
            const end = searchInput.value.length;
            searchInput.setSelectionRange(end, end);
        };
        toggle();
        restoreFocus();
        searchInput.addEventListener('input', () => {
            toggle();
            clearTimeout(debounceId);
            debounceId = setTimeout(submitForm, 400);
        });
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            toggle();
            submitForm();
            restoreFocus();
        });
    })();
</script>

<script>
    (function() {
        const modal = document.getElementById('movementModal');
        const toggleBtn = document.getElementById('toggleMovementForm');
        const toggleText = document.getElementById('toggleMovementFormText');
        const closeBtn = document.getElementById('closeMovementModal');
        const cancelBtn = document.getElementById('cancelMovementForm');
        const productField = document.getElementById('movement-product');
        const typeIn = document.getElementById('movement-type-in');
        const typeOut = document.getElementById('movement-type-out');
        const quantityField = document.getElementById('movement-quantity');
        const notesField = document.getElementById('movement-notes');

        if (!modal || !toggleBtn || !toggleText || !productField || !typeIn || !typeOut || !quantityField || !notesField) {
            return;
        }

        const setToggleText = (isOpen) => {
            toggleText.textContent = isOpen ? 'Cerrar' : 'Nuevo Movimiento';
        };

        const resetForm = () => {
            productField.value = '';
            typeIn.checked = true;
            typeOut.checked = false;
            quantityField.value = '1';
            notesField.value = '';
        };

        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setToggleText(true);
            toggleBtn.blur();
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            setToggleText(false);
            resetForm();
        };

        toggleBtn.addEventListener('click', () => {
            resetForm();
            openModal();
        });

        cancelBtn?.addEventListener('click', () => {
            closeModal();
        });

        closeBtn?.addEventListener('click', () => {
            closeModal();
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
