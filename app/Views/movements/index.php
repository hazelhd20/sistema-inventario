<?php 
$showForm = false;
$hasDateFilter = !empty($filters['date_from']) || !empty($filters['date_to']);
$dateFilterLabel = '';
if ($hasDateFilter) {
    if ($filters['date_from'] && $filters['date_to']) {
        $dateFilterLabel = date('d/m/Y', strtotime($filters['date_from'])) . ' - ' . date('d/m/Y', strtotime($filters['date_to']));
    } elseif ($filters['date_from']) {
        $dateFilterLabel = 'Desde ' . date('d/m/Y', strtotime($filters['date_from']));
    } else {
        $dateFilterLabel = 'Hasta ' . date('d/m/Y', strtotime($filters['date_to']));
    }
}
?>
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

    <!-- Filtros principales -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-4">
        <!-- Búsqueda y filtros rápidos -->
        <form method="GET" action="<?= base_url('movements') ?>" class="flex flex-col lg:flex-row gap-3" id="searchForm">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
                <input id="movement-search" type="text" name="q" placeholder="Buscar por producto, categoría, usuario o notas..." value="<?= e($filters['search'] ?? '') ?>"
                       class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                <button type="button" id="clear-movement-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="flex gap-2 flex-wrap">
                <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white" id="type-filter-buttons">
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
                <button type="button" id="toggleDateFilter"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border transition-colors <?= $hasDateFilter ? 'bg-pastel-peach border-pastel-peach text-slate-700' : 'border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                    <span id="dateFilterLabel"><?= $hasDateFilter ? $dateFilterLabel : 'Filtrar por fecha' ?></span>
                </button>
            </div>
        </form>

        <!-- Filtro de fechas expandible -->
        <div id="dateFilterPanel" class="<?= $hasDateFilter ? '' : 'hidden' ?> pt-4 border-t border-slate-100">
            <div class="flex flex-col lg:flex-row gap-4 items-end">
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha Inicio</label>
                        <input type="date" id="date-from" value="<?= e($filters['date_from'] ?? '') ?>"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha Fin</label>
                        <input type="date" id="date-to" value="<?= e($filters['date_to'] ?? '') ?>"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button type="button" id="applyDateFilter"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-pastel-peach text-slate-700 rounded-lg font-medium text-sm hover:bg-pastel-peach/80 transition-colors">
                        <i data-lucide="filter" class="h-4 w-4"></i>
                        Aplicar
                    </button>
                    <button type="button" id="clearDateFilter"
                           class="<?= $hasDateFilter ? '' : 'hidden' ?> inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200 text-slate-700 rounded-lg font-medium text-sm hover:bg-slate-50 transition-colors">
                        <i data-lucide="x" class="h-4 w-4"></i>
                        Limpiar
                    </button>
                </div>
            </div>
            
            <!-- Rangos rápidos -->
            <div id="quickRanges" class="<?= $hasDateFilter ? 'hidden' : '' ?> mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-2">Rangos rápidos:</p>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $rangeOptions = ['all' => 'Todo', 'today' => 'Hoy', 'week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre'];
                    foreach ($rangeOptions as $key => $label):
                        $active = ($filters['date_range'] ?? 'all') === $key && !$hasDateFilter;
                    ?>
                        <button type="button" data-range="<?= $key ?>"
                           class="range-filter-btn px-3 py-1.5 text-xs font-medium rounded-lg transition-colors <?= $active ? 'bg-pastel-peach text-slate-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                            <?= $label ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div id="dateRangeError" class="hidden flex items-center gap-2 p-3 bg-pastel-rose/30 border border-pastel-rose rounded-lg text-sm text-slate-700 mt-3">
                <i data-lucide="alert-circle" class="h-4 w-4 text-red-500"></i>
                <span>El rango de fechas no es válido.</span>
            </div>
        </div>
    </div>

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
                <i data-lucide="inbox" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500 font-medium">No se encontraron movimientos</p>
                <p class="text-sm text-slate-400 mt-1">
                    <?php if ($hasDateFilter): ?>
                        No hay movimientos en el rango de fechas seleccionado
                    <?php elseif (!empty($filters['search'])): ?>
                        No hay resultados para "<?= e($filters['search']) ?>"
                    <?php else: ?>
                        Aún no hay movimientos registrados
                    <?php endif; ?>
                </p>
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
    const tableBody = document.querySelector('table tbody');
    const typeFilterBtns = document.querySelectorAll('.type-filter-btn');
    const rangeFilterBtns = document.querySelectorAll('.range-filter-btn');
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');
    const applyDateBtn = document.getElementById('applyDateFilter');
    const clearDateBtn = document.getElementById('clearDateFilter');
    const toggleDateBtn = document.getElementById('toggleDateFilter');
    const dateFilterLabel = document.getElementById('dateFilterLabel');
    const dateFilterPanel = document.getElementById('dateFilterPanel');
    const quickRanges = document.getElementById('quickRanges');
    const dateRangeError = document.getElementById('dateRangeError');
    
    // Estado actual de filtros
    let currentFilters = {
        type: '<?= e($filters['type'] ?? 'all') ?>',
        range: '<?= e($filters['date_range'] ?? 'all') ?>',
        dateFrom: '<?= e($filters['date_from'] ?? '') ?>',
        dateTo: '<?= e($filters['date_to'] ?? '') ?>',
        search: '<?= e($filters['search'] ?? '') ?>'
    };
    
    if (!searchInput || !clearBtn) return;
    
    let debounceId;
    const toggle = () => clearBtn.classList.toggle('hidden', !searchInput.value);
    toggle();

    // Función para escapar HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    // Función para formatear fecha
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return { date: `${day}/${month}/${year}`, time: `${hours}:${minutes}` };
    }

    // Función para renderizar fila de movimiento
    function renderMovementRow(movement) {
        const dateInfo = formatDate(movement.date);
        const isIn = movement.type === 'in';
        
        return `
            <tr class="hover:bg-slate-50/50">
                <td class="px-5 py-4">
                    <p class="text-slate-700">${dateInfo.date}</p>
                    <p class="text-xs text-slate-400">${dateInfo.time}</p>
                </td>
                <td class="px-5 py-4">
                    <p class="font-medium text-slate-800">${escapeHtml(movement.product_name)}</p>
                    <p class="text-xs text-slate-500">${escapeHtml(movement.product_category)}</p>
                </td>
                <td class="px-5 py-4">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium ${isIn ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700'}">
                        <i data-lucide="${isIn ? 'arrow-up' : 'arrow-down'}" class="h-3 w-3"></i>
                        ${isIn ? 'Entrada' : 'Salida'}
                    </span>
                </td>
                <td class="px-5 py-4 font-medium text-slate-800">${parseInt(movement.quantity)}</td>
                <td class="px-5 py-4 text-slate-600">${escapeHtml(movement.notes || '-')}</td>
                <td class="px-5 py-4 text-slate-600">${escapeHtml(movement.user_name || 'Sistema')}</td>
            </tr>
        `;
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

    // Actualizar estilos de botones de rango
    function updateRangeButtons(activeRange) {
        rangeFilterBtns.forEach(btn => {
            btn.classList.remove('bg-pastel-peach', 'text-slate-700', 'bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
            if (btn.dataset.range === activeRange) {
                btn.classList.add('bg-pastel-peach', 'text-slate-700');
            } else {
                btn.classList.add('bg-slate-100', 'text-slate-600', 'hover:bg-slate-200');
            }
        });
    }

    // Actualizar UI de filtro de fechas
    function updateDateFilterUI() {
        const hasDateFilter = currentFilters.dateFrom || currentFilters.dateTo;
        
        // Actualizar botón de toggle
        toggleDateBtn.classList.remove('bg-pastel-peach', 'border-pastel-peach', 'text-slate-700', 'border-slate-200', 'text-slate-600', 'hover:bg-slate-50');
        if (hasDateFilter) {
            toggleDateBtn.classList.add('bg-pastel-peach', 'border-pastel-peach', 'text-slate-700');
            let label = '';
            if (currentFilters.dateFrom && currentFilters.dateTo) {
                label = formatDateLabel(currentFilters.dateFrom) + ' - ' + formatDateLabel(currentFilters.dateTo);
            } else if (currentFilters.dateFrom) {
                label = 'Desde ' + formatDateLabel(currentFilters.dateFrom);
            } else {
                label = 'Hasta ' + formatDateLabel(currentFilters.dateTo);
            }
            dateFilterLabel.textContent = label;
        } else {
            toggleDateBtn.classList.add('border-slate-200', 'text-slate-600', 'hover:bg-slate-50');
            dateFilterLabel.textContent = 'Filtrar por fecha';
        }
        
        // Mostrar/ocultar botón limpiar y rangos rápidos
        clearDateBtn.classList.toggle('hidden', !hasDateFilter);
        quickRanges?.classList.toggle('hidden', hasDateFilter);
    }

    function formatDateLabel(dateStr) {
        if (!dateStr) return '';
        const [year, month, day] = dateStr.split('-');
        return `${day}/${month}/${year}`;
    }

    // Validar fechas
    function validateDates() {
        if (dateFrom.value && dateTo.value) {
            const from = new Date(dateFrom.value);
            const to = new Date(dateTo.value);
            if (to < from) {
                dateRangeError.classList.remove('hidden');
                return false;
            }
        }
        dateRangeError.classList.add('hidden');
        return true;
    }

    // Función unificada para cargar movimientos
    async function loadMovements() {
        const url = new URL('<?= base_url('movements') ?>', window.location.origin);
        
        if (currentFilters.search) url.searchParams.set('q', currentFilters.search);
        if (currentFilters.type && currentFilters.type !== 'all') url.searchParams.set('type', currentFilters.type);
        if (currentFilters.dateFrom) url.searchParams.set('date_from', currentFilters.dateFrom);
        if (currentFilters.dateTo) url.searchParams.set('date_to', currentFilters.dateTo);
        if (!currentFilters.dateFrom && !currentFilters.dateTo && currentFilters.range && currentFilters.range !== 'all') {
            url.searchParams.set('range', currentFilters.range);
        }
        
        try {
            const response = await fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Error al cargar movimientos');
            
            const data = await response.json();
            
            if (data.success && tableBody) {
                const tableContainer = tableBody.closest('.bg-white.rounded-xl');
                
                // Eliminar mensaje vacío existente
                const existingEmpty = tableContainer.querySelector('.text-center.py-16');
                if (existingEmpty) existingEmpty.remove();
                
                if (data.movements.length === 0) {
                    tableBody.innerHTML = '';
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'text-center py-16';
                    
                    let emptyMessage = 'No se encontraron movimientos';
                    let emptyDetail = 'Aún no hay movimientos registrados';
                    
                    if (currentFilters.dateFrom || currentFilters.dateTo) {
                        emptyDetail = 'No hay movimientos en el rango de fechas seleccionado';
                    } else if (currentFilters.search) {
                        emptyDetail = `No hay resultados para "${escapeHtml(currentFilters.search)}"`;
                    } else if (currentFilters.type !== 'all') {
                        emptyDetail = `No hay ${currentFilters.type === 'in' ? 'entradas' : 'salidas'} registradas`;
                    }
                    
                    emptyDiv.innerHTML = `
                        <i data-lucide="inbox" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                        <p class="text-slate-500 font-medium">${emptyMessage}</p>
                        <p class="text-sm text-slate-400 mt-1">${emptyDetail}</p>
                    `;
                    tableContainer.querySelector('.overflow-x-auto').after(emptyDiv);
                } else {
                    tableBody.innerHTML = data.movements.map(renderMovementRow).join('');
                }
                
                if (window.lucide) lucide.createIcons();
                window.history.replaceState({}, '', url.toString());
                
                // Actualizar UI
                updateTypeButtons(currentFilters.type);
                updateRangeButtons(currentFilters.range);
                updateDateFilterUI();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Event listeners para búsqueda
    searchInput.addEventListener('input', () => {
        toggle();
        clearTimeout(debounceId);
        debounceId = setTimeout(() => {
            currentFilters.search = searchInput.value;
            loadMovements();
        }, 400);
    });

    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        toggle();
        currentFilters.search = '';
        loadMovements();
    });

    // Event listeners para filtros de tipo
    typeFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.type;
            if (type !== currentFilters.type) {
                currentFilters.type = type;
                loadMovements();
            }
        });
    });

    // Event listeners para rangos rápidos
    rangeFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const range = btn.dataset.range;
            if (range !== currentFilters.range) {
                currentFilters.range = range;
                currentFilters.dateFrom = '';
                currentFilters.dateTo = '';
                dateFrom.value = '';
                dateTo.value = '';
                loadMovements();
            }
        });
    });

    // Toggle panel de fechas
    toggleDateBtn?.addEventListener('click', () => {
        dateFilterPanel.classList.toggle('hidden');
    });

    // Aplicar filtro de fechas
    applyDateBtn?.addEventListener('click', () => {
        if (!validateDates()) return;
        
        currentFilters.dateFrom = dateFrom.value;
        currentFilters.dateTo = dateTo.value;
        currentFilters.range = 'all';
        loadMovements();
    });

    // Limpiar filtro de fechas
    clearDateBtn?.addEventListener('click', () => {
        currentFilters.dateFrom = '';
        currentFilters.dateTo = '';
        dateFrom.value = '';
        dateTo.value = '';
        dateRangeError.classList.add('hidden');
        loadMovements();
    });

    // Validación en tiempo real
    dateFrom?.addEventListener('change', validateDates);
    dateTo?.addEventListener('change', validateDates);
})();

(function() {
    const modal = document.getElementById('movementModal');
    const toggleBtn = document.getElementById('toggleMovementForm');
    const closeBtn = document.getElementById('closeMovementModal');
    const cancelBtn = document.getElementById('cancelMovementForm');
    const movementForm = document.getElementById('movementForm');

    if (!modal || !toggleBtn) return;
    if (modal.parentElement !== document.body) document.body.appendChild(modal);

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

    const openModal = () => { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
    const closeModal = () => { 
        modal.classList.add('hidden'); 
        document.body.style.overflow = ''; 
        movementForm?.reset();
    };

    toggleBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => e.target === modal && closeModal());
    document.addEventListener('keydown', (e) => e.key === 'Escape' && !modal.classList.contains('hidden') && closeModal());

    // Guardar movimiento con AJAX
    movementForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitBtn = movementForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Registrando...
        `;

        try {
            const formData = new FormData(movementForm);
            const response = await fetch('<?= base_url('movements/save') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message, 'success');
                closeModal();
                // Pequeña espera y recargar para mostrar el mensaje de pendiente
                setTimeout(() => window.location.reload(), 800);
            } else {
                showToast(data.message || 'Error al registrar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error de conexión', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
})();

</script>
