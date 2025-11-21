<?php
$isAdmin = $isAdmin ?? false;
?>
<div class="max-w-7xl mx-auto space-y-6">
    <h2 class="text-2xl font-semibold text-gray-800">Control de Inventario</h2>

    <form method="GET" action="<?= base_url('inventory') ?>" class="mb-4 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
            </div>
            <input type="text" name="q" placeholder="Buscar productos..." value="<?= e($search) ?>"
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
        </div>
        <div class="inline-flex rounded-md shadow-sm">
            <a href="<?= base_url('inventory') ?>"
               class="px-4 py-2 text-sm font-medium rounded-l-md <?= $filter === 'all' ? 'bg-blue-pastel text-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' ?> border border-gray-300">
                Todos
            </a>
            <a href="<?= base_url('inventory?filter=low') ?>"
               class="px-4 py-2 text-sm font-medium rounded-r-md <?= $filter === 'low' ? 'bg-pink-pastel text-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' ?> border border-gray-300 border-l-0">
                Stock Bajo
            </a>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Actual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel Minimo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <?php if ($isAdmin): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ajustar Stock</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($products as $product): ?>
                    <?php $isLow = $product['stock_quantity'] <= $product['min_stock_level']; ?>
                    <tr class="<?= $isLow ? 'bg-pink-50' : '' ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= e($product['name']) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= e($product['category']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium <?= $isLow ? 'text-red-600' : 'text-gray-900' ?>">
                                <?= (int) $product['stock_quantity'] ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500"><?= (int) $product['min_stock_level'] ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($isLow): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-pastel text-pink-800">
                                    <i data-lucide="alert-triangle" class="h-3 w-3 mr-1"></i>
                                    Stock Bajo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-pastel text-green-800">
                                    <i data-lucide="check-circle" class="h-3 w-3 mr-1"></i>
                                    OK
                                </span>
                            <?php endif; ?>
                        </td>
                        <?php if ($isAdmin): ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="<?= base_url('inventory/adjust') ?>" method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <input type="number" min="0" name="stock_quantity"
                                           value="<?= (int) $product['stock_quantity'] ?>"
                                           class="w-20 px-2 py-1 border border-gray-300 text-center rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                                    <button type="submit" class="px-3 py-1 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 text-xs">
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
            <div class="text-center py-8">
                <p class="text-gray-500">No se encontraron productos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
