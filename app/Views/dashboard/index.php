<?php $user = auth_user(); ?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">Resumen general</p>
            <h2 class="text-3xl font-semibold text-gray-900">
                ¡Bienvenido, <?= e($user['name'] ?? '') ?>!
            </h2>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-white/80 border border-white/70 shadow-sm text-sm text-gray-700">
            <i data-lucide="calendar" class="h-4 w-4"></i>
            <span><?= strftime('%A %d de %B de %Y') ?></span>
        </div>
    </div>

    <?php if (!empty($lowStock)): ?>
        <div class="card border border-pink-pastel/50 bg-pink-pastel/30">
            <div class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-xl bg-pink-pastel flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="h-5 w-5 text-pink-800"></i>
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-gray-800">¡Alerta de inventario bajo!</h3>
                    <p class="text-sm text-gray-600">
                        <?= count($lowStock) ?> producto(s) están por debajo del mínimo.
                    </p>
                    <ul class="mt-1 list-disc list-inside text-sm text-gray-700 space-y-1">
                        <?php foreach (array_slice($lowStock, 0, 3) as $p): ?>
                            <li><?= e($p['name']) ?> (<?= (int) $p['stock_quantity'] ?> unidades)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total productos</p>
                <p class="text-3xl font-semibold text-gray-900"><?= $stats['total_products'] ?></p>
            </div>
            <span class="h-12 w-12 rounded-2xl bg-blue-pastel/70 border border-white/70 flex items-center justify-center">
                <i data-lucide="package" class="h-6 w-6 text-gray-800"></i>
            </span>
        </div>
        <div class="card flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Stock total</p>
                <p class="text-3xl font-semibold text-gray-900"><?= $stats['total_stock'] ?></p>
            </div>
            <span class="h-12 w-12 rounded-2xl bg-green-pastel/70 border border-white/70 flex items-center justify-center">
                <i data-lucide="bar-chart-2" class="h-6 w-6 text-gray-800"></i>
            </span>
        </div>
        <div class="card flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Entradas / Salidas</p>
                <p class="text-3xl font-semibold text-gray-900">
                    <?= $movementStats['incoming_qty'] ?> / <?= $movementStats['outgoing_qty'] ?>
                </p>
            </div>
            <span class="h-12 w-12 rounded-2xl bg-peach-pastel/70 border border-white/70 flex items-center justify-center">
                <div class="flex space-x-1">
                    <i data-lucide="trending-up" class="h-5 w-5 text-gray-800"></i>
                    <i data-lucide="trending-down" class="h-5 w-5 text-gray-800"></i>
                </div>
            </span>
        </div>
        <div class="card flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Valor del inventario</p>
                <p class="text-3xl font-semibold text-gray-900">$<?= number_format($stats['total_value'], 2) ?></p>
            </div>
            <span class="h-12 w-12 rounded-2xl bg-pink-pastel/70 border border-white/70 flex items-center justify-center">
                <i data-lucide="dollar-sign" class="h-6 w-6 text-gray-800"></i>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Productos recientes</h3>
                <span class="text-xs text-gray-500">Actualizado</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-soft min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Producto</th>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Categoría</th>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Stock</th>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Precio</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                    <?php foreach ($recentProducts as $product): ?>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900"><?= e($product['name']) ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600"><?= e($product['category']) ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-semibold <?= $product['stock_quantity'] <= $product['min_stock_level'] ? 'text-red-500' : 'text-gray-900' ?>">
                                    <?= (int) $product['stock_quantity'] ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900">$<?= number_format((float) $product['price'], 2) ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Últimos movimientos</h3>
                <span class="text-xs text-gray-500">Tiempo real</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-soft min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                    <tr>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Fecha</th>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Producto</th>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Tipo</th>
                        <th class="px-4 py-3 text-left uppercase tracking-wide">Cantidad</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                    <?php foreach ($recentMovements as $movement): ?>
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600">
                                    <?= date('d/m/Y', strtotime($movement['date'])) ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900"><?= e($movement['product_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= e($movement['product_category']) ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $movement['type'] === 'in' ? 'bg-green-pastel text-green-900' : 'bg-pink-pastel text-pink-900' ?>">
                                    <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900"><?= (int) $movement['quantity'] ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
