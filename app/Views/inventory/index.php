<?php
$isAdmin = $isAdmin ?? false;
?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Inventario</h2>
            <p class="text-sm text-slate-500 mt-1">Control de existencias en tiempo real</p>
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" action="<?= base_url('inventory') ?>" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
            <input id="inventory-search" type="text" name="q" placeholder="Buscar productos..." value="<?= e($search) ?>"
                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
            <button type="button" id="clear-inventory-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white">
            <a href="<?= base_url('inventory') ?>"
               class="px-4 py-2.5 text-sm font-medium <?= $filter === 'all' ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' ?>">
                Todos
            </a>
            <a href="<?= base_url('inventory?filter=low') ?>"
               class="px-4 py-2.5 text-sm font-medium border-l border-slate-200 <?= $filter === 'low' ? 'bg-red-50 text-red-600' : 'text-slate-600 hover:bg-slate-50' ?>">
                Stock bajo
            </a>
        </div>
    </form>

    <!-- Tabla -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-soft w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Categoría</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Stock</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Mínimo</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                        <?php if ($isAdmin): ?>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Ajustar</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($products as $product): ?>
                        <?php $isLow = $product['stock_quantity'] <= $product['min_stock_level']; ?>
                        <tr class="hover:bg-slate-50/50 <?= $isLow ? 'bg-red-50/30' : '' ?>">
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800"><?= e($product['name']) ?></p>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= e($product['category']) ?></td>
                            <td class="px-5 py-4">
                                <span class="font-medium <?= $isLow ? 'text-red-600' : 'text-slate-800' ?>">
                                    <?= (int) $product['stock_quantity'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= (int) $product['min_stock_level'] ?></td>
                            <td class="px-5 py-4">
                                <?php if ($isLow): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                        <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                                        Bajo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                        <i data-lucide="check-circle" class="h-3 w-3"></i>
                                        OK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td class="px-5 py-4">
                                    <form action="<?= base_url('inventory/adjust') ?>" method="POST" class="flex items-center gap-2">
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <input type="number" min="0" name="stock_quantity"
                                               value="<?= (int) $product['stock_quantity'] ?>"
                                               class="w-20 px-2 py-1.5 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500">
                                        <button type="submit" class="px-3 py-1.5 bg-primary-500 text-white rounded-lg text-xs font-medium hover:bg-primary-600">
                                            Guardar
                                        </button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-16">
                <i data-lucide="archive" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500">No se encontraron productos</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    const searchInput = document.getElementById('inventory-search');
    const clearBtn = document.getElementById('clear-inventory-search');
    if (!searchInput || !clearBtn) return;
    const form = searchInput.closest('form');
    let debounceId;
    const submitForm = () => form?.requestSubmit?.() || form?.submit();
    const toggle = () => clearBtn.classList.toggle('hidden', !searchInput.value);
    toggle();
    searchInput.focus();
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
</script>
