<?php $user = auth_user(); ?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">
                Bienvenido, <?= e($user['name'] ?? '') ?>
            </h2>
            <p class="text-sm text-slate-500 mt-1">Resumen general del inventario</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white border border-slate-200 text-sm text-slate-600">
            <i data-lucide="calendar" class="h-4 w-4 text-slate-400"></i>
            <span><?= strftime('%d de %B, %Y') ?></span>
        </div>
    </div>

    <!-- Alerta de stock bajo -->
    <?php if (!empty($lowStock)): ?>
        <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-100">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 shrink-0">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-red-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-semibold text-red-800">Alerta de inventario bajo</h3>
                <p class="text-sm text-red-700 mt-0.5">
                    <?= count($lowStock) ?> producto(s) están por debajo del mínimo.
                </p>
                <ul class="mt-2 space-y-1">
                    <?php foreach (array_slice($lowStock, 0, 3) as $p): ?>
                        <li class="text-sm text-red-600">• <?= e($p['name']) ?> (<?= (int) $p['stock_quantity'] ?> unidades)</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Total productos</p>
                    <p class="text-2xl font-semibold text-slate-800 mt-1"><?= $stats['total_products'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-accent-sky/50 flex items-center justify-center">
                    <i data-lucide="package" class="h-5 w-5 text-primary-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Stock total</p>
                    <p class="text-2xl font-semibold text-slate-800 mt-1"><?= $stats['total_stock'] ?></p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-accent-mint/50 flex items-center justify-center">
                    <i data-lucide="bar-chart-2" class="h-5 w-5 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Entradas / Salidas</p>
                    <p class="text-2xl font-semibold text-slate-800 mt-1">
                        <?= $movementStats['incoming_qty'] ?> / <?= $movementStats['outgoing_qty'] ?>
                    </p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-accent-peach/50 flex items-center justify-center">
                    <i data-lucide="repeat" class="h-5 w-5 text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Valor inventario</p>
                    <p class="text-2xl font-semibold text-slate-800 mt-1">$<?= number_format($stats['total_value'], 0) ?></p>
                </div>
                <div class="w-11 h-11 rounded-lg bg-accent-rose/50 flex items-center justify-center">
                    <i data-lucide="dollar-sign" class="h-5 w-5 text-rose-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tablas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Productos recientes -->
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Productos recientes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-soft w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Stock</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Precio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($recentProducts as $product): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-800"><?= e($product['name']) ?></p>
                                    <p class="text-xs text-slate-500"><?= e($product['category']) ?></p>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="<?= $product['stock_quantity'] <= $product['min_stock_level'] ? 'text-red-600 font-medium' : 'text-slate-700' ?>">
                                        <?= (int) $product['stock_quantity'] ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-700">$<?= number_format((float) $product['price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Últimos movimientos -->
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Últimos movimientos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-soft w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipo</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cant.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($recentMovements as $movement): ?>
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 text-slate-600"><?= date('d/m/Y', strtotime($movement['date'])) ?></td>
                                <td class="px-5 py-3">
                                    <p class="font-medium text-slate-800"><?= e($movement['product_name']) ?></p>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        <?= $movement['type'] === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                        <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-medium text-slate-800"><?= (int) $movement['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
