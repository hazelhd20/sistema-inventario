<?php
$showForm = isset($_GET['show_form']) || $editingProduct;
?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestión de Productos</h2>
        <a href="<?= base_url($showForm ? 'products' : 'products?show_form=1#form') ?>"
           class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
            <i data-lucide="plus" class="h-5 w-5 mr-1"></i>
            <?= $showForm ? 'Cerrar formulario' : 'Nuevo Producto' ?>
        </a>
    </div>

    <?php if ($showForm): ?>
        <div class="card" id="form">
            <h3 class="text-lg font-semibold mb-4">
                <?= $editingProduct ? 'Editar Producto' : 'Agregar Nuevo Producto' ?>
            </h3>
            <form action="<?= base_url('products/save') ?>" method="POST" class="space-y-4">
                <?php if ($editingProduct): ?>
                    <input type="hidden" name="id" value="<?= (int) $editingProduct['id'] ?>">
                <?php endif; ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto</label>
                        <input type="text" name="name" required
                               value="<?= e($editingProduct['name'] ?? '') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                        <input type="text" name="category" required
                               value="<?= e($editingProduct['category'] ?? '') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="3"
                                  class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"><?= e($editingProduct['description'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta</label>
                        <input type="number" step="0.01" min="0" name="price" required
                               value="<?= e($editingProduct['price'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
                        <input type="number" step="0.01" min="0" name="cost" required
                               value="<?= e($editingProduct['cost'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad en Stock</label>
                        <input type="number" min="0" name="stock_quantity" required
                               value="<?= e($editingProduct['stock_quantity'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nivel Mínimo de Stock</label>
                        <input type="number" min="0" name="min_stock_level" required
                               value="<?= e($editingProduct['min_stock_level'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL de Imagen (opcional)</label>
                        <input type="text" name="image_url"
                               value="<?= e($editingProduct['image_url'] ?? '') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"
                               placeholder="https://ejemplo.com/imagen.jpg">
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <a href="<?= base_url('products') ?>" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                        <?= $editingProduct ? 'Actualizar' : 'Guardar' ?>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="GET" action="<?= base_url('products') ?>" class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="q" placeholder="Buscar productos..." value="<?= e($search) ?>"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
            </div>
        </form>

        <?php if (count($products) === 0): ?>
            <div class="text-center py-12">
                <p class="text-gray-500">No se encontraron productos.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <?php $isLow = $product['stock_quantity'] <= $product['min_stock_level']; ?>
                    <div class="card <?= $isLow ? 'low-stock' : '' ?>">
                        <div class="flex flex-col h-full">
                            <?php if (!empty($product['image_url'])): ?>
                                <div class="h-40 w-full overflow-hidden rounded-t-lg mb-3">
                                    <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>" class="w-full h-full object-cover">
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-semibold text-gray-800"><?= e($product['name']) ?></h3>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-pastel text-gray-700">
                                        <?= e($product['category']) ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                    <?= e($product['description']) ?>
                                </p>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Precio</p>
                                        <p class="font-semibold">$<?= number_format((float) $product['price'], 2) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Costo</p>
                                        <p class="font-semibold">$<?= number_format((float) $product['cost'], 2) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Existencias</p>
                                        <p class="font-semibold <?= $isLow ? 'text-red-500' : '' ?>">
                                            <?= (int) $product['stock_quantity'] ?>
                                            <?= $isLow ? '<span class="ml-1 text-xs">(¡Bajo!)</span>' : '' ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Mínimo</p>
                                        <p class="font-semibold"><?= (int) $product['min_stock_level'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                                <a href="<?= base_url('products?edit=' . (int) $product['id'] . '#form') ?>" class="p-2 rounded-full hover:bg-gray-100 text-gray-600" title="Editar">
                                    <i data-lucide="edit" class="h-4 w-4"></i>
                                </a>
                                <form action="<?= base_url('products/delete') ?>" method="POST" onsubmit="return confirm('¿Eliminar este producto?');">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button type="submit" class="p-2 rounded-full hover:bg-gray-100 text-gray-600" title="Eliminar">
                                        <i data-lucide="trash" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
