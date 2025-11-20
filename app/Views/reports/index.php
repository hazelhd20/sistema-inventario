<?php
$totalProducts = count($products);
$totalStock = array_sum(array_map(fn ($p) => (int) $p['stock_quantity'], $products));
$lowStockCount = count($lowStock);
$rangeLabels = [
    'today' => 'Hoy',
    'week' => 'Última Semana',
    'month' => 'Último Mes',
    'quarter' => 'Último Trimestre',
    'all' => 'Todo el Tiempo',
];
$movementRangeLabel = $rangeLabels[$dateRange] ?? 'Último Mes';

$topStock = $products;
usort($topStock, fn ($a, $b) => $b['stock_quantity'] <=> $a['stock_quantity']);
$topStock = array_slice($topStock, 0, 5);

$topValue = $products;
usort($topValue, fn ($a, $b) => ($b['price'] * $b['stock_quantity']) <=> ($a['price'] * $a['stock_quantity']));
$topValue = array_slice($topValue, 0, 5);
?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Reportes</h2>
        <button type="button" onclick="alert('Esta función exportaría el reporte actual (CSV/PDF).');"
                class="mt-3 sm:mt-0 flex items-center px-4 py-2 bg-green-pastel rounded-md text-gray-800 hover:bg-green-400 transition-colors duration-200">
            <i data-lucide="download" class="h-5 w-5 mr-1"></i>
            Exportar Reporte
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php
        $reportOptions = [
            'inventory' => ['label' => 'Inventario General', 'icon' => 'bar-chart-2', 'bg' => 'bg-blue-pastel', 'text' => 'text-blue-700'],
            'movements' => ['label' => 'Movimientos', 'icon' => 'repeat', 'bg' => 'bg-peach-pastel', 'text' => 'text-pink-700'],
            'lowStock' => ['label' => 'Stock Bajo', 'icon' => 'alert-triangle', 'bg' => 'bg-pink-pastel', 'text' => 'text-pink-700'],
            'value' => ['label' => 'Valor del Inventario', 'icon' => 'pie-chart', 'bg' => 'bg-green-pastel', 'text' => 'text-green-700'],
        ];
        foreach ($reportOptions as $key => $meta):
            $active = $reportType === $key;
            ?>
            <a href="<?= base_url('reports?report=' . $key . '&range=' . e($dateRange)) ?>"
               class="card flex items-center p-4 <?= $active ? 'ring-2 ring-blue-pastel' : '' ?>">
                <div class="p-3 rounded-full <?= e($meta['bg']) ?> mr-3">
                    <i data-lucide="<?= e($meta['icon']) ?>" class="h-5 w-5 <?= e($meta['text']) ?>"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800"><?= e($meta['label']) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (in_array($reportType, ['movements', 'value'], true)): ?>
        <div class="inline-flex rounded-md shadow-sm">
            <?php
            $rangeOptions = ['week' => 'Última Semana', 'month' => 'Último Mes', 'quarter' => 'Último Trimestre', 'all' => 'Todo'];
            foreach ($rangeOptions as $key => $label):
                $active = $dateRange === $key;
                $classes = 'px-4 py-2 text-sm font-medium border border-gray-300';
                if ($key === 'week') {
                    $classes .= ' rounded-l-md';
                } elseif ($key === 'all') {
                    $classes .= ' rounded-r-md border-l-0';
                } else {
                    $classes .= ' border-l-0';
                }
                ?>
                <a href="<?= base_url('reports?report=' . e($reportType) . '&range=' . $key) ?>"
                   class="<?= $classes ?> <?= $active ? 'bg-blue-pastel text-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card space-y-6">
        <?php if ($reportType === 'inventory'): ?>
            <h3 class="text-lg font-semibold mb-4">Reporte de Inventario General</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-medium mb-3">Resumen</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Total de Productos</p>
                                <p class="text-xl font-semibold"><?= $totalProducts ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Stock Total</p>
                                <p class="text-xl font-semibold"><?= $totalStock ?> unidades</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Productos con Stock Bajo</p>
                                <p class="text-xl font-semibold"><?= $lowStockCount ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Valor del Inventario</p>
                                <p class="text-xl font-semibold">$<?= number_format((float) $inventoryValue, 2) ?></p>
                            </div>
                        </div>
                    </div>
                    <h4 class="text-md font-medium mt-6 mb-3">Productos por Categoría</h4>
                    <div class="bg-gray-50 p-4 rounded-md space-y-3">
                        <?php foreach ($productsByCategory as $category => $count): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-700"><?= e($category) ?></span>
                                <div class="flex items-center">
                                    <div class="w-32 h-3 bg-gray-200 rounded-full mr-2 overflow-hidden">
                                        <div class="h-full bg-blue-pastel rounded-full" style="width: <?= $totalProducts > 0 ? ($count / $totalProducts * 100) : 0 ?>%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500"><?= $count ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-medium mb-3">Top 5 Productos con Mayor Stock</h4>
                    <div class="bg-gray-50 p-4 rounded-md mb-6">
                        <div class="space-y-3">
                            <?php foreach ($topStock as $product): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700"><?= e($product['name']) ?></span>
                                    <div class="flex items-center">
                                        <div class="w-32 h-3 bg-gray-200 rounded-full mr-2 overflow-hidden">
                                            <div class="h-full bg-green-pastel rounded-full" style="width: <?= $totalStock > 0 ? ($product['stock_quantity'] / $totalStock * 100) : 0 ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500"><?= (int) $product['stock_quantity'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <h4 class="text-md font-medium mb-3">Productos con Stock Bajo</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <?php if ($lowStockCount === 0): ?>
                            <p class="text-sm text-gray-500">No hay productos con stock bajo.</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($lowStock as $product): ?>
                                    <?php $percent = $product['min_stock_level'] > 0 ? ($product['stock_quantity'] / $product['min_stock_level']) * 100 : 0; ?>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700"><?= e($product['name']) ?></span>
                                        <div class="flex items-center">
                                            <div class="w-32 h-3 bg-gray-200 rounded-full mr-2 overflow-hidden">
                                                <div class="h-full bg-pink-pastel rounded-full" style="width: <?= $percent ?>%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500"><?= (int) $product['stock_quantity'] ?> / <?= (int) $product['min_stock_level'] ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php elseif ($reportType === 'movements'): ?>
            <h3 class="text-lg font-semibold mb-4">Reporte de Movimientos - <?= e($movementRangeLabel) ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-medium mb-3">Resumen de Movimientos</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Total de Movimientos</p>
                                <p class="text-xl font-semibold"><?= $movementStats['total'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Entradas</p>
                                <p class="text-xl font-semibold text-green-700"><?= $movementStats['total_in'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Salidas</p>
                                <p class="text-xl font-semibold text-pink-700"><?= $movementStats['total_out'] ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Proporción Entrada/Salida</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden flex">
                                        <?php $totalQty = max(1, $movementStats['incoming_qty'] + $movementStats['outgoing_qty']); ?>
                                        <div class="h-full bg-green-pastel" style="width: <?= ($movementStats['incoming_qty'] / $totalQty) * 100 ?>%"></div>
                                        <div class="h-full bg-pink-pastel" style="width: <?= ($movementStats['outgoing_qty'] / $totalQty) * 100 ?>%"></div>
                                    </div>
                                </div>
                                <div class="flex justify-between mt-1 text-xs">
                                    <span><?= $movementStats['incoming_qty'] ?> unidades</span>
                                    <span><?= $movementStats['outgoing_qty'] ?> unidades</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-medium mb-3">Productos con Mayor Movimiento</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <?php if (empty($movements)): ?>
                            <p class="text-sm text-gray-500">No hay movimientos en este período.</p>
                        <?php else: ?>
                            <?php
                            $productActivity = [];
                            foreach ($movements as $movement) {
                                $pid = $movement['product_id'];
                                if (!isset($productActivity[$pid])) {
                                    $productActivity[$pid] = ['name' => $movement['product_name'], 'count' => 0, 'quantity' => 0];
                                }
                                $productActivity[$pid]['count']++;
                                $productActivity[$pid]['quantity'] += $movement['quantity'];
                            }
                            usort($productActivity, fn ($a, $b) => $b['count'] <=> $a['count']);
                            $productActivity = array_slice($productActivity, 0, 5);
                            ?>
                            <div class="space-y-3">
                                <?php foreach ($productActivity as $item): ?>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-700"><?= e($item['name']) ?></span>
                                        <div class="flex items-center">
                                            <div class="w-24 h-3 bg-gray-200 rounded-full mr-2 overflow-hidden">
                                                <div class="h-full bg-peach-pastel rounded-full" style="width: 100%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500"><?= $item['count'] ?> mov. (<?= $item['quantity'] ?> unid.)</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <h4 class="text-md font-medium mt-6 mb-3">Últimos Movimientos</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach (array_slice($movements, 0, 10) as $movement): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($movement['date'])) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= e($movement['product_name']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $movement['type'] === 'in' ? 'bg-green-pastel text-green-800' : 'bg-pink-pastel text-pink-800' ?>">
                                    <?php if ($movement['type'] === 'in'): ?>
                                        <i data-lucide="trending-up" class="h-3 w-3 mr-1"></i> Entrada
                                    <?php else: ?>
                                        <i data-lucide="trending-down" class="h-3 w-3 mr-1"></i> Salida
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= (int) $movement['quantity'] ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= e($movement['notes'] ?: '-') ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($reportType === 'lowStock'): ?>
            <h3 class="text-lg font-semibold mb-4">Reporte de Stock Bajo</h3>
            <?php if ($lowStockCount === 0): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500">No hay productos con stock bajo.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel Mínimo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Déficit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Requerido</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($lowStock as $product): ?>
                            <?php
                            $deficit = max(0, $product['min_stock_level'] - $product['stock_quantity']);
                            $valueNeeded = $deficit * $product['cost'];
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= e($product['name']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= e($product['category']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-red-600"><?= (int) $product['stock_quantity'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= (int) $product['min_stock_level'] ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-red-600"><?= $deficit ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">$<?= number_format($valueNeeded, 2) ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php elseif ($reportType === 'value'): ?>
            <h3 class="text-lg font-semibold mb-4">Reporte de Valor del Inventario - <?= e($movementRangeLabel) ?></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-md font-medium mb-3">Resumen de Valor</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Valor Total del Inventario</p>
                                <p class="text-xl font-semibold">$<?= number_format((float) $inventoryValue, 2) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Costo Total del Inventario</p>
                                <p class="text-xl font-semibold">$<?= number_format((float) $inventoryCost, 2) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Margen Potencial</p>
                                <p class="text-xl font-semibold text-green-700">$<?= number_format($inventoryValue - $inventoryCost, 2) ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Productos Valorados</p>
                                <p class="text-xl font-semibold"><?= $totalProducts ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-md font-medium mb-3">Productos de Mayor Valor</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="space-y-3">
                            <?php foreach ($topValue as $product): ?>
                                <?php
                                $productValue = $product['price'] * $product['stock_quantity'];
                                $percentage = $inventoryValue > 0 ? ($productValue / $inventoryValue) * 100 : 0;
                                ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700"><?= e($product['name']) ?></span>
                                    <div class="flex items-center">
                                        <div class="w-32 h-3 bg-gray-200 rounded-full mr-2 overflow-hidden">
                                            <div class="h-full bg-green-pastel rounded-full" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500">$<?= number_format($productValue, 2) ?> (<?= number_format($percentage, 1) ?>%)</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="text-md font-medium mt-6 mb-3">Valor por Categoría</h4>
            <div class="bg-gray-50 p-4 rounded-md space-y-3">
                <?php foreach ($valueByCategory as $category => $value): ?>
                    <?php $percentage = $inventoryValue > 0 ? ($value / $inventoryValue) * 100 : 0; ?>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-700"><?= e($category) ?></span>
                        <div class="flex items-center">
                            <div class="w-64 h-6 bg-gray-200 rounded-full mr-3 overflow-hidden">
                                <div class="h-full bg-blue-pastel rounded-full flex items-center justify-end pr-2" style="width: <?= $percentage ?>%">
                                    <span class="text-xs text-gray-700 font-medium"><?= number_format($percentage, 1) ?>%</span>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">$<?= number_format($value, 2) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
