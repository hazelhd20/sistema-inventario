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
                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            <button type="button" id="clear-inventory-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white">
            <a href="<?= base_url('inventory') ?>"
               class="px-4 py-2.5 text-sm font-medium <?= $filter === 'all' ? 'bg-pastel-blue text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                Todos
            </a>
            <a href="<?= base_url('inventory?filter=low') ?>"
               class="px-4 py-2.5 text-sm font-medium border-l border-slate-200 <?= $filter === 'low' ? 'bg-pastel-rose text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?>">
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
                        <tr class="hover:bg-slate-50/50 <?= $isLow ? 'bg-pastel-rose/20' : '' ?>" data-product-id="<?= (int) $product['id'] ?>">
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800"><?= e($product['name']) ?></p>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= e($product['category']) ?></td>
                            <td class="px-5 py-4">
                                <span class="font-medium stock-value <?= $isLow ? 'text-red-600' : 'text-slate-800' ?>">
                                    <?= (int) $product['stock_quantity'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= (int) $product['min_stock_level'] ?></td>
                            <td class="px-5 py-4">
                                <?php if ($isLow): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-pastel-rose text-slate-700 status-badge">
                                        <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                                        Bajo
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-pastel-mint text-slate-700 status-badge">
                                        <i data-lucide="check-circle" class="h-3 w-3"></i>
                                        OK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td class="px-5 py-4">
                                    <form class="flex items-center gap-2 adjust-stock-form" data-product-id="<?= (int) $product['id'] ?>">
                                        <input type="number" min="0" name="stock_quantity"
                                               value="<?= (int) $product['stock_quantity'] ?>"
                                               class="w-20 px-2 py-1.5 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue stock-input">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-blue text-slate-700 hover:bg-pastel-blue/80 transition-colors save-btn">
                                            <i data-lucide="save" class="h-3.5 w-3.5"></i>
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
    const tableBody = document.querySelector('table tbody');
    const tableContainer = document.querySelector('.bg-white.rounded-xl.border');
    const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
    
    if (!searchInput || !clearBtn) return;
    
    let debounceId;
    const toggle = () => clearBtn.classList.toggle('hidden', !searchInput.value);
    toggle();
    searchInput.focus();

    // Función para renderizar la fila de un producto
    function renderRow(product) {
        const isLow = product.stock_quantity <= product.min_stock_level;
        return `
            <tr class="hover:bg-slate-50/50 ${isLow ? 'bg-pastel-rose/20' : ''}" data-product-id="${product.id}">
                <td class="px-5 py-4">
                    <p class="font-medium text-slate-800">${escapeHtml(product.name)}</p>
                </td>
                <td class="px-5 py-4 text-slate-600">${escapeHtml(product.category)}</td>
                <td class="px-5 py-4">
                    <span class="font-medium stock-value ${isLow ? 'text-red-600' : 'text-slate-800'}">
                        ${parseInt(product.stock_quantity)}
                    </span>
                </td>
                <td class="px-5 py-4 text-slate-600">${parseInt(product.min_stock_level)}</td>
                <td class="px-5 py-4">
                    ${isLow ? `
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-pastel-rose text-slate-700 status-badge">
                            <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                            Bajo
                        </span>
                    ` : `
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-pastel-mint text-slate-700 status-badge">
                            <i data-lucide="check-circle" class="h-3 w-3"></i>
                            OK
                        </span>
                    `}
                </td>
                ${isAdmin ? `
                    <td class="px-5 py-4">
                        <form class="flex items-center gap-2 adjust-stock-form" data-product-id="${product.id}">
                            <input type="number" min="0" name="stock_quantity"
                                   value="${parseInt(product.stock_quantity)}"
                                   class="w-20 px-2 py-1.5 text-center border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue stock-input">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-blue text-slate-700 hover:bg-pastel-blue/80 transition-colors save-btn">
                                <i data-lucide="save" class="h-3.5 w-3.5"></i>
                                Guardar
                            </button>
                        </form>
                    </td>
                ` : ''}
            </tr>
        `;
    }

    // Función para escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Función para mostrar toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 z-50 flex items-center gap-2 px-4 py-3 rounded-lg shadow-lg text-sm font-medium transition-all duration-300 transform translate-y-2 opacity-0 ${
            type === 'success' ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700'
        }`;
        toast.innerHTML = `
            <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" class="h-4 w-4"></i>
            <span>${escapeHtml(message)}</span>
        `;
        document.body.appendChild(toast);
        
        // Re-inicializar iconos
        if (window.lucide) lucide.createIcons();
        
        // Animar entrada
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-2', 'opacity-0');
        });
        
        // Remover después de 3 segundos
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Búsqueda AJAX
    async function searchProducts(query) {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('q', query);
        
        try {
            const response = await fetch(currentUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Error en la búsqueda');
            
            const data = await response.json();
            
            if (data.success && tableBody) {
                if (data.products.length === 0) {
                    tableBody.innerHTML = '';
                    // Mostrar mensaje de vacío
                    let emptyDiv = tableContainer.querySelector('.empty-message');
                    if (!emptyDiv) {
                        emptyDiv = document.createElement('div');
                        emptyDiv.className = 'empty-message text-center py-16';
                        emptyDiv.innerHTML = `
                            <i data-lucide="archive" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                            <p class="text-slate-500">No se encontraron productos</p>
                        `;
                        tableContainer.querySelector('.overflow-x-auto').after(emptyDiv);
                    }
                    emptyDiv.classList.remove('hidden');
                    if (window.lucide) lucide.createIcons();
                } else {
                    // Ocultar mensaje de vacío si existe
                    const emptyDiv = tableContainer.querySelector('.empty-message');
                    if (emptyDiv) emptyDiv.classList.add('hidden');
                    
                    tableBody.innerHTML = data.products.map(renderRow).join('');
                    if (window.lucide) lucide.createIcons();
                    attachFormListeners();
                }
                
                // Actualizar URL sin recargar
                window.history.replaceState({}, '', currentUrl.toString());
            }
        } catch (error) {
            console.error('Error en búsqueda:', error);
        }
    }

    // Ajustar stock con AJAX
    async function adjustStock(form, productId, newStock) {
        const btn = form.querySelector('.save-btn');
        const originalContent = btn.innerHTML;
        
        // Estado de carga
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        try {
            const formData = new FormData();
            formData.append('id', productId);
            formData.append('stock_quantity', newStock);

            const response = await fetch('<?= base_url('inventory/adjust') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                
                // Actualizar la fila visualmente
                const row = form.closest('tr');
                const stockValue = row.querySelector('.stock-value');
                const statusBadge = row.querySelector('.status-badge');
                const product = data.product;
                
                // Actualizar valor
                stockValue.textContent = product.stock_quantity;
                
                // Actualizar clases según si es bajo o no
                if (product.is_low) {
                    row.classList.add('bg-pastel-rose/20');
                    stockValue.classList.remove('text-slate-800');
                    stockValue.classList.add('text-red-600');
                    statusBadge.outerHTML = `
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-pastel-rose text-slate-700 status-badge">
                            <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                            Bajo
                        </span>
                    `;
                } else {
                    row.classList.remove('bg-pastel-rose/20');
                    stockValue.classList.remove('text-red-600');
                    stockValue.classList.add('text-slate-800');
                    statusBadge.outerHTML = `
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-pastel-mint text-slate-700 status-badge">
                            <i data-lucide="check-circle" class="h-3 w-3"></i>
                            OK
                        </span>
                    `;
                }
                
                if (window.lucide) lucide.createIcons();
            } else {
                showToast(data.message || 'Error al actualizar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
            if (window.lucide) lucide.createIcons();
        }
    }

    // Adjuntar listeners a los formularios
    function attachFormListeners() {
        document.querySelectorAll('.adjust-stock-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const productId = form.dataset.productId;
                const stockInput = form.querySelector('.stock-input');
                await adjustStock(form, productId, stockInput.value);
            });
        });
    }

    // Event listeners
    searchInput.addEventListener('input', () => {
        toggle();
        clearTimeout(debounceId);
        debounceId = setTimeout(() => searchProducts(searchInput.value), 400);
    });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        toggle();
        searchProducts('');
    });

    // Inicializar formularios existentes
    attachFormListeners();
})();
</script>
