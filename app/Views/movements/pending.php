<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Movimientos Pendientes</h2>
            <p class="text-sm text-slate-500 mt-1">Aprueba o rechaza los movimientos solicitados</p>
        </div>
        <a href="<?= base_url('movements') ?>"
           class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition-colors shadow-sm">
            <i data-lucide="list" class="h-4 w-4"></i>
            Ver aprobados
        </a>
    </div>

    <!-- Filtros -->
    <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white w-fit" id="type-filter-buttons">
        <?php
        $typeOptions = ['all' => 'Todos', 'in' => 'Entradas', 'out' => 'Salidas'];
        foreach ($typeOptions as $key => $label):
            $active = ($filters['type'] ?? 'all') === $key;
        ?>
            <button type="button" data-type="<?= $key ?>"
               class="type-filter-btn px-3 py-2 text-xs font-medium transition-colors <?= $active ? 'bg-pastel-blue text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?> <?= $key !== 'all' ? 'border-l border-slate-200' : '' ?>">
                <?= $label ?>
            </button>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden" id="pendingTableContainer">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Por aprobar</h3>
            <span class="text-sm text-slate-500" id="pendingCount"><?= count($movements) ?> pendiente(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-soft w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipo</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cantidad</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Notas</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Solicitado por</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="pendingTableBody">
                    <?php foreach ($movements as $movement): ?>
                        <tr class="hover:bg-slate-50/50" data-movement-id="<?= (int) $movement['id'] ?>">
                            <td class="px-5 py-4 text-slate-600">
                                <?= date('d/m/Y H:i', strtotime($movement['date'])) ?>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800"><?= e($movement['product_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= e($movement['product_category']) ?></p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    <?= $movement['type'] === 'in' ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700' ?>">
                                    <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800"><?= (int) $movement['quantity'] ?></td>
                            <td class="px-5 py-4 text-slate-600"><?= e($movement['notes'] ?: '-') ?></td>
                            <td class="px-5 py-4">
                                <p class="text-slate-600"><?= e($movement['user_name'] ?: 'Sistema') ?></p>
                                <p class="text-xs text-slate-400 capitalize"><?= e($movement['user_role'] ?? '') ?></p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="approve-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-mint text-slate-700 hover:bg-pastel-mint/80 transition-colors" data-id="<?= (int) $movement['id'] ?>">
                                        <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                        Aprobar
                                    </button>
                                    <button type="button" class="reject-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-rose text-slate-700 hover:bg-pastel-rose/80 transition-colors" data-id="<?= (int) $movement['id'] ?>">
                                        <i data-lucide="x" class="h-3.5 w-3.5"></i>
                                        Rechazar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="emptyState" class="<?= empty($movements) ? '' : 'hidden' ?> text-center py-16">
            <i data-lucide="check-circle" class="h-12 w-12 text-pastel-mint mx-auto mb-3"></i>
            <p class="text-slate-500">No hay movimientos pendientes</p>
        </div>
    </div>
</div>

<script>
(function() {
    const tableBody = document.getElementById('pendingTableBody');
    const pendingCount = document.getElementById('pendingCount');
    const emptyState = document.getElementById('emptyState');
    const typeFilterBtns = document.querySelectorAll('.type-filter-btn');
    
    let currentType = '<?= e($filters['type'] ?? 'all') ?>';

    // Función para escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Función para formatear fecha
    function formatDateTime(dateStr) {
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}`;
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

    // Actualizar estilos de botones de tipo
    function updateTypeButtons(activeType) {
        typeFilterBtns.forEach(btn => {
            btn.classList.remove('bg-pastel-blue', 'text-slate-700', 'text-slate-600', 'hover:bg-slate-50');
            if (btn.dataset.type === activeType) {
                btn.classList.add('bg-pastel-blue', 'text-slate-700');
            } else {
                btn.classList.add('text-slate-600', 'hover:bg-slate-50');
            }
        });
    }

    // Renderizar fila de movimiento pendiente
    function renderPendingRow(movement) {
        const isIn = movement.type === 'in';
        return `
            <tr class="hover:bg-slate-50/50" data-movement-id="${movement.id}">
                <td class="px-5 py-4 text-slate-600">
                    ${formatDateTime(movement.date)}
                </td>
                <td class="px-5 py-4">
                    <p class="font-medium text-slate-800">${escapeHtml(movement.product_name)}</p>
                    <p class="text-xs text-slate-500">${escapeHtml(movement.product_category)}</p>
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${isIn ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700'}">
                        ${isIn ? 'Entrada' : 'Salida'}
                    </span>
                </td>
                <td class="px-5 py-4 font-semibold text-slate-800">${parseInt(movement.quantity)}</td>
                <td class="px-5 py-4 text-slate-600">${escapeHtml(movement.notes || '-')}</td>
                <td class="px-5 py-4">
                    <p class="text-slate-600">${escapeHtml(movement.user_name || 'Sistema')}</p>
                    <p class="text-xs text-slate-400 capitalize">${escapeHtml(movement.user_role || '')}</p>
                </td>
                <td class="px-5 py-4">
                    <div class="flex items-center gap-2">
                        <button type="button" class="approve-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-mint text-slate-700 hover:bg-pastel-mint/80 transition-colors" data-id="${movement.id}">
                            <i data-lucide="check" class="h-3.5 w-3.5"></i>
                            Aprobar
                        </button>
                        <button type="button" class="reject-btn inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-rose text-slate-700 hover:bg-pastel-rose/80 transition-colors" data-id="${movement.id}">
                            <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            Rechazar
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    // Actualizar contador
    function updateCount(count) {
        if (count === undefined) {
            count = tableBody.querySelectorAll('tr').length;
        }
        pendingCount.textContent = `${count} pendiente(s)`;
        
        if (count === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    // Cargar movimientos pendientes con AJAX
    async function loadPendingMovements(type) {
        const url = new URL('<?= base_url('movements/pending') ?>', window.location.origin);
        if (type && type !== 'all') url.searchParams.set('type', type);
        
        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Error al cargar movimientos');
            
            const data = await response.json();
            
            if (data.success) {
                currentType = type;
                updateTypeButtons(currentType);
                
                if (data.movements.length === 0) {
                    tableBody.innerHTML = '';
                    emptyState.innerHTML = `
                        <i data-lucide="check-circle" class="h-12 w-12 text-pastel-mint mx-auto mb-3"></i>
                        <p class="text-slate-500">${currentType === 'all' ? 'No hay movimientos pendientes' : 
                            currentType === 'in' ? 'No hay entradas pendientes' : 'No hay salidas pendientes'}</p>
                    `;
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    tableBody.innerHTML = data.movements.map(renderPendingRow).join('');
                }
                
                updateCount(data.count);
                if (window.lucide) lucide.createIcons();
                window.history.replaceState({}, '', url.toString());
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Procesar movimiento (aprobar/rechazar)
    async function processMovement(btn, action) {
        const movementId = btn.dataset.id;
        const row = btn.closest('tr');
        const originalContent = btn.innerHTML;
        const confirmMsg = action === 'reject' ? '¿Rechazar este movimiento?' : null;
        
        if (confirmMsg && !confirm(confirmMsg)) return;

        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        `;

        try {
            const formData = new FormData();
            formData.append('id', movementId);

            const response = await fetch(`<?= base_url('movements/') ?>${action}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                
                row.style.transition = 'opacity 0.3s, transform 0.3s, background-color 0.3s';
                row.style.backgroundColor = action === 'approve' ? 'rgb(187, 247, 208)' : 'rgb(254, 202, 202)';
                
                setTimeout(() => {
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(' + (action === 'approve' ? '' : '-') + '20px)';
                    
                    setTimeout(() => {
                        row.remove();
                        updateCount();
                    }, 300);
                }, 200);
            } else {
                showToast(data.message || 'Error al procesar', 'error');
                btn.disabled = false;
                btn.innerHTML = originalContent;
                if (window.lucide) lucide.createIcons();
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
            btn.disabled = false;
            btn.innerHTML = originalContent;
            if (window.lucide) lucide.createIcons();
        }
    }

    // Event listeners para filtros de tipo
    typeFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.type;
            if (type !== currentType) {
                loadPendingMovements(type);
            }
        });
    });

    // Event delegation para botones de aprobar/rechazar
    document.addEventListener('click', (e) => {
        const approveBtn = e.target.closest('.approve-btn');
        const rejectBtn = e.target.closest('.reject-btn');

        if (approveBtn) {
            processMovement(approveBtn, 'approve');
        } else if (rejectBtn) {
            processMovement(rejectBtn, 'reject');
        }
    });
})();
</script>
