<?php
$totalProducts = count($products);
$totalStock = array_sum(array_map(fn ($p) => (int) $p['stock_quantity'], $products));
$lowStockCount = count($lowStock);
$rangeLabels = [
    'today' => 'Hoy',
    'week' => 'Última Semana',
    'month' => 'Último Mes',
    'quarter' => 'Último Trimestre',
    'all' => 'Todo',
];
$movementRangeLabel = $rangeLabels[$dateRange] ?? 'Último Mes';

$topStock = $products;
usort($topStock, fn ($a, $b) => $b['stock_quantity'] <=> $a['stock_quantity']);
$topStock = array_slice($topStock, 0, 5);

$topValue = $products;
usort($topValue, fn ($a, $b) => ($b['price'] * $b['stock_quantity']) <=> ($a['price'] * $a['stock_quantity']));
$topValue = array_slice($topValue, 0, 5);

// Determinar si hay filtro de fechas activo
$hasDateFilter = !empty($dateFrom) || !empty($dateTo);
$dateFilterLabel = '';
if ($hasDateFilter) {
    if ($dateFrom && $dateTo) {
        $dateFilterLabel = date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo));
    } elseif ($dateFrom) {
        $dateFilterLabel = 'Desde ' . date('d/m/Y', strtotime($dateFrom));
    } else {
        $dateFilterLabel = 'Hasta ' . date('d/m/Y', strtotime($dateTo));
    }
}
?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Reportes</h2>
            <p class="text-sm text-slate-500 mt-1">Análisis y estadísticas del inventario</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print();"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition-colors shadow-sm">
                <i data-lucide="printer" class="h-4 w-4"></i>
                Imprimir
            </button>
        </div>
    </div>

    <!-- Selector de reportes -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
        <?php
        $reportOptions = [
            'inventory' => ['label' => 'Inventario', 'icon' => 'bar-chart-2', 'color' => 'pastel-blue'],
            'stock' => ['label' => 'Estado Stock', 'icon' => 'package', 'color' => 'pastel-mint'],
            'movements' => ['label' => 'Movimientos', 'icon' => 'repeat', 'color' => 'pastel-peach'],
            'lowStock' => ['label' => 'Stock Bajo', 'icon' => 'alert-triangle', 'color' => 'pastel-rose'],
            'value' => ['label' => 'Valor', 'icon' => 'pie-chart', 'color' => 'pastel-blue'],
        ];
        foreach ($reportOptions as $key => $meta):
            $active = $reportType === $key;
        ?>
            <a href="<?= base_url('reports?report=' . $key . '&range=' . e($dateRange)) ?>"
               class="flex items-center gap-3 p-4 rounded-xl border transition-colors
                      <?= $active ? 'bg-' . $meta['color'] . '/30 border-' . $meta['color'] . ' text-slate-800' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <div class="w-10 h-10 rounded-lg bg-<?= $meta['color'] ?> flex items-center justify-center">
                    <i data-lucide="<?= e($meta['icon']) ?>" class="h-5 w-5 text-slate-600"></i>
                </div>
                <span class="font-medium text-sm"><?= e($meta['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Filtro por rango de fechas para movimientos -->
    <?php if ($reportType === 'movements'): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <form method="GET" action="<?= base_url('reports') ?>" class="flex flex-col lg:flex-row gap-4 items-end">
                <input type="hidden" name="report" value="movements">
                
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha Inicio</label>
                        <input type="date" name="date_from" value="<?= e($dateFrom) ?>"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Fecha Fin</label>
                        <input type="date" name="date_to" value="<?= e($dateTo) ?>"
                               class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg font-medium text-sm hover:bg-pastel-blue/80 transition-colors">
                        <i data-lucide="search" class="h-4 w-4"></i>
                        Filtrar
                    </button>
                    <?php if ($hasDateFilter): ?>
                        <a href="<?= base_url('reports?report=movements') ?>"
                           class="inline-flex items-center gap-2 px-4 py-2.5 border border-slate-200 text-slate-700 rounded-lg font-medium text-sm hover:bg-slate-50 transition-colors">
                            <i data-lucide="x" class="h-4 w-4"></i>
                            Limpiar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            
            <?php if (!$hasDateFilter): ?>
                <!-- Rangos predefinidos -->
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-2">Rangos rápidos:</p>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $rangeOptions = ['today' => 'Hoy', 'week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre', 'all' => 'Todo'];
                        foreach ($rangeOptions as $key => $label):
                            $active = $dateRange === $key;
                        ?>
                            <a href="<?= base_url('reports?report=movements&range=' . $key) ?>"
                               class="px-3 py-1.5 text-xs font-medium rounded-lg <?= $active ? 'bg-pastel-peach text-slate-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                                <?= $label ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Filtro de rango para valor -->
    <?php if ($reportType === 'value'): ?>
        <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white w-fit">
            <?php
            $rangeOptions = ['week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre', 'all' => 'Todo'];
            foreach ($rangeOptions as $key => $label):
                $active = $dateRange === $key;
            ?>
                <a href="<?= base_url('reports?report=' . e($reportType) . '&range=' . $key) ?>"
                   class="px-4 py-2 text-sm font-medium <?= $active ? 'bg-pastel-blue text-slate-700' : 'text-slate-600 hover:bg-slate-50' ?> <?= $key !== 'week' ? 'border-l border-slate-200' : '' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Contenido del reporte -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 print:shadow-none print:border-0">
        <!-- Encabezado del reporte con fecha de generación -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
            <div>
                <?php if ($reportType === 'inventory'): ?>
                    <h3 class="text-lg font-semibold text-slate-800">Reporte de Inventario General</h3>
                <?php elseif ($reportType === 'stock'): ?>
                    <h3 class="text-lg font-semibold text-slate-800">Reporte de Estado de Stock</h3>
                    <p class="text-sm text-slate-500">Todos los productos activos con su estado actual</p>
                <?php elseif ($reportType === 'movements'): ?>
                    <h3 class="text-lg font-semibold text-slate-800">Reporte de Movimientos</h3>
                    <p class="text-sm text-slate-500">
                        <?= $hasDateFilter ? $dateFilterLabel : $movementRangeLabel ?>
                    </p>
                <?php elseif ($reportType === 'lowStock'): ?>
                    <h3 class="text-lg font-semibold text-slate-800">Productos con Stock Bajo</h3>
                <?php elseif ($reportType === 'value'): ?>
                    <h3 class="text-lg font-semibold text-slate-800">Valor del Inventario</h3>
                    <p class="text-sm text-slate-500"><?= e($movementRangeLabel) ?></p>
                <?php endif; ?>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400">Generado el</p>
                <p class="text-sm font-medium text-slate-600"><?= e($reportGeneratedAt) ?></p>
            </div>
        </div>

        <?php if ($reportType === 'inventory'): ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium text-slate-700 mb-3">Resumen</h4>
                        <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg">
                            <div>
                                <p class="text-xs text-slate-500">Total Productos</p>
                                <p class="text-xl font-semibold text-slate-800"><?= $totalProducts ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Stock Total</p>
                                <p class="text-xl font-semibold text-slate-800"><?= $totalStock ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Stock Bajo</p>
                                <p class="text-xl font-semibold <?= $lowStockCount > 0 ? 'text-red-600' : 'text-slate-800' ?>"><?= $lowStockCount ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Valor</p>
                                <p class="text-xl font-semibold text-slate-800">$<?= number_format((float) $inventoryValue, 0) ?></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-slate-700 mb-3">Por Categoría</h4>
                        <div class="space-y-3 p-4 bg-slate-50 rounded-lg">
                            <?php foreach ($productsByCategory as $category => $count): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-700"><?= e($category) ?></span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-pastel-blue rounded-full" style="width: <?= $totalProducts > 0 ? ($count / $totalProducts * 100) : 0 ?>%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 w-6 text-right"><?= $count ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium text-slate-700 mb-3">Mayor Stock</h4>
                        <div class="space-y-3 p-4 bg-slate-50 rounded-lg">
                            <?php foreach ($topStock as $product): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-700 truncate max-w-[60%]"><?= e($product['name']) ?></span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-pastel-mint rounded-full" style="width: <?= $totalStock > 0 ? ($product['stock_quantity'] / $totalStock * 100) : 0 ?>%"></div>
                                        </div>
                                        <span class="text-xs text-slate-500 w-8 text-right"><?= (int) $product['stock_quantity'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if ($lowStockCount > 0): ?>
                        <div>
                            <h4 class="text-sm font-medium text-slate-700 mb-3">Stock Bajo</h4>
                            <div class="space-y-3 p-4 bg-pastel-rose/20 rounded-lg">
                                <?php foreach ($lowStock as $product): ?>
                                    <?php $percent = $product['min_stock_level'] > 0 ? ($product['stock_quantity'] / $product['min_stock_level']) * 100 : 0; ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-slate-700 truncate max-w-[60%]"><?= e($product['name']) ?></span>
                                        <div class="flex items-center gap-2">
                                            <div class="w-20 h-2 bg-pastel-rose/50 rounded-full overflow-hidden">
                                                <div class="h-full bg-pastel-rose rounded-full" style="width: <?= $percent ?>%"></div>
                                            </div>
                                            <span class="text-xs text-slate-600 w-12 text-right"><?= (int) $product['stock_quantity'] ?>/<?= (int) $product['min_stock_level'] ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($reportType === 'stock'): ?>
            <!-- REPORTE DE ESTADO DE STOCK -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Total Productos</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $totalProducts ?></p>
                </div>
                <div class="p-4 bg-pastel-mint/30 rounded-lg">
                    <p class="text-xs text-slate-600">Stock Normal</p>
                    <p class="text-2xl font-semibold text-green-600"><?= $totalProducts - $lowStockCount ?></p>
                </div>
                <div class="p-4 bg-pastel-rose/30 rounded-lg">
                    <p class="text-xs text-slate-600">Stock Bajo</p>
                    <p class="text-2xl font-semibold text-red-600"><?= $lowStockCount ?></p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Stock Total</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $totalStock ?></p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table-soft w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Categoría</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Stock Actual</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Stock Mínimo</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($products as $product): ?>
                            <?php 
                            $isLow = (int)$product['stock_quantity'] <= (int)$product['min_stock_level'];
                            ?>
                            <tr class="hover:bg-slate-50/50 <?= $isLow ? 'bg-pastel-rose/5' : '' ?>">
                                <td class="px-4 py-3 font-medium text-slate-800"><?= e($product['name']) ?></td>
                                <td class="px-4 py-3 text-slate-600"><?= e($product['category'] ?? 'Sin categoría') ?></td>
                                <td class="px-4 py-3 text-center font-medium <?= $isLow ? 'text-red-600' : 'text-slate-800' ?>">
                                    <?= (int) $product['stock_quantity'] ?>
                                </td>
                                <td class="px-4 py-3 text-center text-slate-600"><?= (int) $product['min_stock_level'] ?></td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($isLow): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                                            Bajo
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i data-lucide="check-circle" class="h-3 w-3"></i>
                                            Normal
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-12">
                    <i data-lucide="package" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                    <p class="text-slate-500">No hay productos activos</p>
                </div>
            <?php endif; ?>

        <?php elseif ($reportType === 'movements'): ?>
            <!-- REPORTE DE MOVIMIENTOS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Total Movimientos</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $movementStats['total'] ?></p>
                </div>
                <div class="p-4 bg-pastel-mint/30 rounded-lg">
                    <p class="text-xs text-slate-600">Entradas</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $movementStats['total_in'] ?></p>
                    <p class="text-xs text-slate-500 mt-1">+<?= $movementStats['incoming_qty'] ?> unidades</p>
                </div>
                <div class="p-4 bg-pastel-rose/30 rounded-lg">
                    <p class="text-xs text-slate-600">Salidas</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $movementStats['total_out'] ?></p>
                    <p class="text-xs text-slate-500 mt-1">-<?= $movementStats['outgoing_qty'] ?> unidades</p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Balance</p>
                    <p class="text-2xl font-semibold <?= ($movementStats['incoming_qty'] - $movementStats['outgoing_qty']) >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                        <?= ($movementStats['incoming_qty'] - $movementStats['outgoing_qty']) >= 0 ? '+' : '' ?><?= $movementStats['incoming_qty'] - $movementStats['outgoing_qty'] ?>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">unidades</p>
                </div>
            </div>

            <?php if (!empty($movements)): ?>
                <!-- Tabla de movimientos detallados -->
                <h4 class="text-sm font-medium text-slate-700 mb-3">Detalle de Movimientos</h4>
                <div class="overflow-x-auto mb-6">
                    <table class="table-soft w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha/Hora</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Cantidad</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase print:hidden">Notas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($movements as $movement): ?>
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3">
                                        <p class="text-slate-700"><?= date('d/m/Y', strtotime($movement['date'])) ?></p>
                                        <p class="text-xs text-slate-400"><?= date('H:i:s', strtotime($movement['date'])) ?></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-slate-800"><?= e($movement['product_name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= e($movement['product_category']) ?></p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium
                                            <?= $movement['type'] === 'in' ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700' ?>">
                                            <i data-lucide="<?= $movement['type'] === 'in' ? 'arrow-up' : 'arrow-down' ?>" class="h-3 w-3"></i>
                                            <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-medium text-slate-800"><?= (int) $movement['quantity'] ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= e($movement['user_name'] ?? 'Sistema') ?></td>
                                    <td class="px-4 py-3 text-slate-500 text-xs print:hidden"><?= e($movement['notes'] ?: '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Totales por producto (solo si hay filtro de fechas) -->
                <?php if ($hasDateFilter && !empty($totalsByProduct)): ?>
                    <h4 class="text-sm font-medium text-slate-700 mb-3 mt-8">Totales por Producto</h4>
                    <div class="overflow-x-auto">
                        <table class="table-soft w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Categoría</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Total Entradas</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Total Salidas</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Balance</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Movimientos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($totalsByProduct as $productTotal): ?>
                                    <?php $balance = (int)$productTotal['total_in'] - (int)$productTotal['total_out']; ?>
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-4 py-3 font-medium text-slate-800"><?= e($productTotal['product_name']) ?></td>
                                        <td class="px-4 py-3 text-slate-600"><?= e($productTotal['category_name']) ?></td>
                                        <td class="px-4 py-3 text-center text-green-600 font-medium">+<?= (int)$productTotal['total_in'] ?></td>
                                        <td class="px-4 py-3 text-center text-red-600 font-medium">-<?= (int)$productTotal['total_out'] ?></td>
                                        <td class="px-4 py-3 text-center font-medium <?= $balance >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                                            <?= $balance >= 0 ? '+' : '' ?><?= $balance ?>
                                        </td>
                                        <td class="px-4 py-3 text-center text-slate-600"><?= (int)$productTotal['total_movements'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-12">
                    <i data-lucide="inbox" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
                    <p class="text-slate-500 font-medium">No se encontraron movimientos</p>
                    <p class="text-sm text-slate-400 mt-1">
                        <?php if ($hasDateFilter): ?>
                            No hay movimientos en el rango de fechas seleccionado
                        <?php else: ?>
                            No hay movimientos en este período
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>

        <?php elseif ($reportType === 'lowStock'): ?>
            <?php if ($lowStockCount === 0): ?>
                <div class="text-center py-12">
                    <i data-lucide="check-circle" class="h-12 w-12 text-pastel-mint mx-auto mb-3"></i>
                    <p class="text-slate-500">Todos los productos tienen stock adecuado</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-soft w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Categoría</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Stock Actual</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Stock Mínimo</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase">Déficit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($lowStock as $product): ?>
                                <?php $deficit = max(0, $product['min_stock_level'] - $product['stock_quantity']); ?>
                                <tr class="hover:bg-pastel-rose/10 bg-pastel-rose/5">
                                    <td class="px-4 py-3 font-medium text-slate-800"><?= e($product['name']) ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= e($product['category']) ?></td>
                                    <td class="px-4 py-3 text-center font-medium text-red-600"><?= (int) $product['stock_quantity'] ?></td>
                                    <td class="px-4 py-3 text-center text-slate-600"><?= (int) $product['min_stock_level'] ?></td>
                                    <td class="px-4 py-3 text-center font-medium text-red-600">-<?= $deficit ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php elseif ($reportType === 'value'): ?>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Valor Total</p>
                    <p class="text-2xl font-semibold text-slate-800">$<?= number_format((float) $inventoryValue, 0) ?></p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Costo Total</p>
                    <p class="text-2xl font-semibold text-slate-800">$<?= number_format((float) $inventoryCost, 0) ?></p>
                </div>
                <div class="p-4 bg-pastel-mint/30 rounded-lg">
                    <p class="text-xs text-slate-600">Margen Potencial</p>
                    <p class="text-2xl font-semibold text-slate-800">$<?= number_format($inventoryValue - $inventoryCost, 0) ?></p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Productos</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $totalProducts ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-slate-700 mb-3">Mayor Valor</h4>
                    <div class="space-y-3 p-4 bg-slate-50 rounded-lg">
                        <?php foreach ($topValue as $product): ?>
                            <?php
                            $productValue = $product['price'] * $product['stock_quantity'];
                            $percentage = $inventoryValue > 0 ? ($productValue / $inventoryValue) * 100 : 0;
                            ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-700 truncate max-w-[50%]"><?= e($product['name']) ?></span>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-pastel-mint rounded-full" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 w-16 text-right">$<?= number_format($productValue, 0) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-slate-700 mb-3">Valor por Categoría</h4>
                    <div class="space-y-3 p-4 bg-slate-50 rounded-lg">
                        <?php foreach ($valueByCategory as $category => $value): ?>
                            <?php $percentage = $inventoryValue > 0 ? ($value / $inventoryValue) * 100 : 0; ?>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-700 truncate max-w-[50%]"><?= e($category) ?></span>
                                <div class="flex items-center gap-2">
                                    <div class="w-20 h-2 bg-slate-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-pastel-blue rounded-full" style="width: <?= $percentage ?>%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 w-16 text-right">$<?= number_format($value, 0) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estilos de impresión -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .max-w-6xl, .max-w-6xl * {
        visibility: visible;
    }
    .max-w-6xl {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100%;
    }
    button, a, form, .print\:hidden {
        display: none !important;
    }
    .bg-white {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }
}
</style>
