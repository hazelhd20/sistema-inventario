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
?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Reportes</h2>
            <p class="text-sm text-slate-500 mt-1">Análisis y estadísticas del inventario</p>
        </div>
        <button type="button" onclick="alert('Esta función exportaría el reporte (CSV/PDF).');"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-medium text-sm hover:bg-slate-50 transition-colors">
            <i data-lucide="download" class="h-4 w-4"></i>
            Exportar
        </button>
    </div>

    <!-- Selector de reportes -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <?php
        $reportOptions = [
            'inventory' => ['label' => 'Inventario', 'icon' => 'bar-chart-2'],
            'movements' => ['label' => 'Movimientos', 'icon' => 'repeat'],
            'lowStock' => ['label' => 'Stock Bajo', 'icon' => 'alert-triangle'],
            'value' => ['label' => 'Valor', 'icon' => 'pie-chart'],
        ];
        foreach ($reportOptions as $key => $meta):
            $active = $reportType === $key;
        ?>
            <a href="<?= base_url('reports?report=' . $key . '&range=' . e($dateRange)) ?>"
               class="flex items-center gap-3 p-4 rounded-xl border transition-colors
                      <?= $active ? 'bg-primary-50 border-primary-200 text-primary-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <div class="w-10 h-10 rounded-lg <?= $active ? 'bg-primary-100' : 'bg-slate-100' ?> flex items-center justify-center">
                    <i data-lucide="<?= e($meta['icon']) ?>" class="h-5 w-5"></i>
                </div>
                <span class="font-medium text-sm"><?= e($meta['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Filtro de rango -->
    <?php if (in_array($reportType, ['movements', 'value'], true)): ?>
        <div class="flex rounded-lg border border-slate-200 overflow-hidden bg-white w-fit">
            <?php
            $rangeOptions = ['week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre', 'all' => 'Todo'];
            foreach ($rangeOptions as $key => $label):
                $active = $dateRange === $key;
            ?>
                <a href="<?= base_url('reports?report=' . e($reportType) . '&range=' . $key) ?>"
                   class="px-4 py-2 text-sm font-medium <?= $active ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' ?> <?= $key !== 'week' ? 'border-l border-slate-200' : '' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Contenido del reporte -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <?php if ($reportType === 'inventory'): ?>
            <h3 class="text-lg font-semibold text-slate-800 mb-6">Reporte de Inventario General</h3>
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
                                            <div class="h-full bg-primary-400 rounded-full" style="width: <?= $totalProducts > 0 ? ($count / $totalProducts * 100) : 0 ?>%"></div>
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
                                            <div class="h-full bg-green-400 rounded-full" style="width: <?= $totalStock > 0 ? ($product['stock_quantity'] / $totalStock * 100) : 0 ?>%"></div>
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
                            <div class="space-y-3 p-4 bg-red-50 rounded-lg">
                                <?php foreach ($lowStock as $product): ?>
                                    <?php $percent = $product['min_stock_level'] > 0 ? ($product['stock_quantity'] / $product['min_stock_level']) * 100 : 0; ?>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-red-700 truncate max-w-[60%]"><?= e($product['name']) ?></span>
                                        <div class="flex items-center gap-2">
                                            <div class="w-20 h-2 bg-red-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-red-400 rounded-full" style="width: <?= $percent ?>%"></div>
                                            </div>
                                            <span class="text-xs text-red-600 w-12 text-right"><?= (int) $product['stock_quantity'] ?>/<?= (int) $product['min_stock_level'] ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($reportType === 'movements'): ?>
            <h3 class="text-lg font-semibold text-slate-800 mb-6">Movimientos - <?= e($movementRangeLabel) ?></h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Total Movimientos</p>
                    <p class="text-2xl font-semibold text-slate-800"><?= $movementStats['total'] ?></p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-green-600">Entradas</p>
                    <p class="text-2xl font-semibold text-green-700"><?= $movementStats['total_in'] ?></p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-xs text-red-600">Salidas</p>
                    <p class="text-2xl font-semibold text-red-700"><?= $movementStats['total_out'] ?></p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Balance</p>
                    <p class="text-2xl font-semibold <?= ($movementStats['incoming_qty'] - $movementStats['outgoing_qty']) >= 0 ? 'text-green-700' : 'text-red-700' ?>">
                        <?= ($movementStats['incoming_qty'] - $movementStats['outgoing_qty']) >= 0 ? '+' : '' ?><?= $movementStats['incoming_qty'] - $movementStats['outgoing_qty'] ?>
                    </p>
                </div>
            </div>

            <?php if (!empty($movements)): ?>
                <h4 class="text-sm font-medium text-slate-700 mb-3">Últimos Movimientos</h4>
                <div class="overflow-x-auto">
                    <table class="table-soft w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach (array_slice($movements, 0, 10) as $movement): ?>
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-3 text-slate-600"><?= date('d/m/Y', strtotime($movement['date'])) ?></td>
                                    <td class="px-4 py-3 font-medium text-slate-800"><?= e($movement['product_name']) ?></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            <?= $movement['type'] === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                            <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-800"><?= (int) $movement['quantity'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-500 text-center py-8">No hay movimientos en este período.</p>
            <?php endif; ?>

        <?php elseif ($reportType === 'lowStock'): ?>
            <h3 class="text-lg font-semibold text-slate-800 mb-6">Productos con Stock Bajo</h3>
            <?php if ($lowStockCount === 0): ?>
                <div class="text-center py-12">
                    <i data-lucide="check-circle" class="h-12 w-12 text-green-300 mx-auto mb-3"></i>
                    <p class="text-slate-500">Todos los productos tienen stock adecuado</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table-soft w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Categoría</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Stock</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Mínimo</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Déficit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($lowStock as $product): ?>
                                <?php $deficit = max(0, $product['min_stock_level'] - $product['stock_quantity']); ?>
                                <tr class="hover:bg-red-50/50 bg-red-50/30">
                                    <td class="px-4 py-3 font-medium text-slate-800"><?= e($product['name']) ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= e($product['category']) ?></td>
                                    <td class="px-4 py-3 font-medium text-red-600"><?= (int) $product['stock_quantity'] ?></td>
                                    <td class="px-4 py-3 text-slate-600"><?= (int) $product['min_stock_level'] ?></td>
                                    <td class="px-4 py-3 font-medium text-red-600">-<?= $deficit ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php elseif ($reportType === 'value'): ?>
            <h3 class="text-lg font-semibold text-slate-800 mb-6">Valor del Inventario - <?= e($movementRangeLabel) ?></h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Valor Total</p>
                    <p class="text-2xl font-semibold text-slate-800">$<?= number_format((float) $inventoryValue, 0) ?></p>
                </div>
                <div class="p-4 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Costo Total</p>
                    <p class="text-2xl font-semibold text-slate-800">$<?= number_format((float) $inventoryCost, 0) ?></p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-green-600">Margen Potencial</p>
                    <p class="text-2xl font-semibold text-green-700">$<?= number_format($inventoryValue - $inventoryCost, 0) ?></p>
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
                                        <div class="h-full bg-green-400 rounded-full" style="width: <?= $percentage ?>%"></div>
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
                                        <div class="h-full bg-primary-400 rounded-full" style="width: <?= $percentage ?>%"></div>
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
