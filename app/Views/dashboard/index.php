<?php $user = auth_user(); ?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">
            ¡Bienvenido, <?= e($user['name'] ?? '') ?>!
        </h2>
        <div class="text-sm text-gray-600">
            <?= strftime('%A %d de %B de %Y') ?>
        </div>
    </div>

    <?php if (!empty($lowStock)): ?>
        <div class="bg-pink-pastel bg-opacity-20 border-l-4 border-pink-pastel p-4 rounded-md">
            <div class="flex items-start">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-pink-700"></i>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-800">¡Alerta de inventario bajo!</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <?= count($lowStock) ?> producto(s) están por debajo del mínimo.
                    </p>
                    <ul class="mt-2 list-disc list-inside text-sm text-gray-700">
                        <?php foreach (array_slice($lowStock, 0, 3) as $p): ?>
                            <li><?= e($p['name']) ?> (<?= (int) $p['stock_quantity'] ?> unidades)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="card flex items-center">
            <div class="bg-blue-pastel p-3 rounded-lg mr-4">
                <i data-lucide="package" class="h-6 w-6 text-blue-700"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Total Productos</p>
                <p class="text-2xl font-semibold text-gray-800"><?= $stats['total_products'] ?></p>
            </div>
        </div>
        <div class="card flex items-center">
            <div class="bg-green-pastel p-3 rounded-lg mr-4">
                <i data-lucide="bar-chart-2" class="h-6 w-6 text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Stock Total</p>
                <p class="text-2xl font-semibold text-gray-800"><?= $stats['total_stock'] ?></p>
            </div>
        </div>
        <div class="card flex items-center">
            <div class="bg-peach-pastel p-3 rounded-lg mr-4 flex space-x-1">
                <i data-lucide="trending-up" class="h-5 w-5 text-green-700"></i>
                <i data-lucide="trending-down" class="h-5 w-5 text-pink-700"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Entradas / Salidas</p>
                <p class="text-2xl font-semibold text-gray-800">
                    <?= $movementStats['incoming_qty'] ?> / <?= $movementStats['outgoing_qty'] ?>
                </p>
            </div>
        </div>
        <div class="card flex items-center">
            <div class="bg-pink-pastel p-3 rounded-lg mr-4">
                <i data-lucide="dollar-sign" class="h-6 w-6 text-pink-700"></i>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Valor del Inventario</p>
                <p class="text-2xl font-semibold text-gray-800">$<?= number_format($stats['total_value'], 2) ?></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="text-lg font-semibold mb-4">Productos recientes</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($recentProducts as $product): ?>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= e($product['name']) ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-500"><?= e($product['category']) ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium <?= $product['stock_quantity'] <= $product['min_stock_level'] ? 'text-red-500' : 'text-gray-900' ?>">
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
            <h3 class="text-lg font-semibold mb-4">Últimos movimientos</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($recentMovements as $movement): ?>
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($movement['date'])) ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?= e($movement['product_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= e($movement['product_category']) ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $movement['type'] === 'in' ? 'bg-green-pastel text-green-800' : 'bg-pink-pastel text-pink-800' ?>">
                                    <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= (int) $movement['quantity'] ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
