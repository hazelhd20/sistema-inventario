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
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
            <input id="inventory-search" type="text" name="q" placeholder="Buscar productos..." value="<?= e($search) ?>"
                   class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            <button type="button" id="clear-inventory-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white" id="filter-buttons">
            <button type="button" data-filter="all"
               class="filter-btn px-4 py-2.5 text-sm font-medium transition-colors <?= $filter === 'all' ? 'bg-pastel-blue text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                Todos
            </button>
            <button type="button" data-filter="low"
               class="filter-btn px-4 py-2.5 text-sm font-medium border-l border-slate-200 transition-colors <?= $filter === 'low' ? 'bg-pastel-rose text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                Stock bajo
            </button>
        </div>
    </div>

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
    const filterButtons = document.querySelectorAll('.filter-btn');
    const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
    let currentFilter = '<?= e($filter) ?>';
    
    if (!searchInput || !clearBtn) return;
    
    let debounceId;
    const toggle = () => clearBtn.classList.toggle('hidden', !searchInput.value);
    toggle();
    searchInput.focus();

    // Función para renderizar la fila de un producto
    function renderRow(product) {
        const isLow = parseInt(product.stock_quantity) <= parseInt(product.min_stock_level);
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
        if (window.lucide) lucide.createIcons();
        requestAnimationFrame(() => toast.classList.remove('translate-y-2', 'opacity-0'));
        setTimeout(() => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Actualizar estilos de botones de filtro
    function updateFilterButtons(activeFilter) {
        filterButtons.forEach(btn => {
            const filter = btn.dataset.filter;
            btn.classList.remove('bg-pastel-blue', 'bg-pastel-rose', 'text-slate-700', 'text-slate-600', 'hover:bg-slate-50');
            
            if (filter === activeFilter) {
                if (filter === 'low') {
                    btn.classList.add('bg-pastel-rose', 'text-slate-700');
                } else {
                    btn.classList.add('bg-pastel-blue', 'text-slate-700');
                }
            } else {
                btn.classList.add('text-slate-600', 'hover:bg-slate-50');
            }
        });
    }

    // Función unificada para cargar productos con filtros
    async function loadProducts(options = {}) {
        const query = options.query ?? searchInput.value;
        const filter = options.filter ?? currentFilter;
        
        const url = new URL('<?= base_url('inventory') ?>', window.location.origin);
        if (query) url.searchParams.set('q', query);
        if (filter && filter !== 'all') url.searchParams.set('filter', filter);
        
        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Error al cargar productos');
            
            const data = await response.json();
            
            if (data.success && tableBody) {
                // Actualizar estado actual
                currentFilter = data.filter || 'all';
                updateFilterButtons(currentFilter);
                
                // Manejar mensaje vacío existente en el HTML
                const existingEmpty = tableContainer.querySelector('.text-center.py-16:not(.empty-message)');
                if (existingEmpty) existingEmpty.remove();
                
                if (data.products.length === 0) {
                    tableBody.innerHTML = '';
                    let emptyDiv = tableContainer.querySelector('.empty-message');
                    if (!emptyDiv) {
                        emptyDiv = document.createElement('div');
                        emptyDiv.className = 'empty-message text-center py-16';
                        tableContainer.querySelector('.overflow-x-auto').after(emptyDiv);
                    }
                    emptyDiv.innerHTML = `
                        <i data-lucide="archive" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                        <p class="text-slate-500">${currentFilter === 'low' ? 'No hay productos con stock bajo' : 'No se encontraron productos'}</p>
                    `;
                    emptyDiv.classList.remove('hidden');
                    if (window.lucide) lucide.createIcons();
                } else {
                    const emptyDiv = tableContainer.querySelector('.empty-message');
                    if (emptyDiv) emptyDiv.classList.add('hidden');
                    
                    tableBody.innerHTML = data.products.map(renderRow).join('');
                    if (window.lucide) lucide.createIcons();
                    attachFormListeners();
                }
                
                window.history.replaceState({}, '', url.toString());
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Ajustar stock con AJAX
    async function adjustStock(form, productId, newStock) {
        const btn = form.querySelector('.save-btn');
        const originalContent = btn.innerHTML;
        
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
                
                const row = form.closest('tr');
                const stockValue = row.querySelector('.stock-value');
                const statusBadge = row.querySelector('.status-badge');
                const product = data.product;
                
                stockValue.textContent = product.stock_quantity;
                
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
                    
                    // Si estamos en filtro "low" y el producto ya no es bajo, removerlo
                    if (currentFilter === 'low') {
                        row.style.transition = 'opacity 0.3s, transform 0.3s';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            row.remove();
                            // Verificar si quedan productos
                            if (tableBody.children.length === 0) {
                                loadProducts({ filter: 'low' });
                            }
                        }, 300);
                        showToast('Producto removido del filtro de stock bajo', 'success');
                        return;
                    }
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

    // Event listeners para búsqueda
    searchInput.addEventListener('input', () => {
        toggle();
        clearTimeout(debounceId);
        debounceId = setTimeout(() => loadProducts({ query: searchInput.value }), 400);
    });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        toggle();
        loadProducts({ query: '' });
    });

    // Event listeners para filtros
    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;
            if (filter !== currentFilter) {
                loadProducts({ filter });
            }
        });
    });

    // Inicializar formularios existentes
    attachFormListeners();
})();
</script>
