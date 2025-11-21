<?php
$isAdmin = $isAdmin ?? false;
$showForm = $isAdmin && (bool) $editingProduct;
?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestion de Productos</h2>
        <?php if ($isAdmin): ?>
            <button type="button" id="toggleProductForm"
                    class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                <i data-lucide="plus" class="h-5 w-5 mr-1"></i>
                <span id="toggleProductFormText">Nuevo Producto</span>
            </button>
        <?php endif; ?>
    </div>

    <?php if ($isAdmin): ?>
        <div class="card <?= $showForm ? '' : 'hidden' ?>" id="productFormCard">
            <h3 class="text-lg font-semibold mb-4" id="formTitle">
                <?= $editingProduct ? 'Editar Producto' : 'Agregar Nuevo Producto' ?>
            </h3>
            <form id="productForm" action="<?= base_url('products/save') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="product-id" value="<?= $editingProduct ? (int) $editingProduct['id'] : '' ?>">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto</label>
                        <input type="text" name="name" id="product-name" required minlength="3"
                               value="<?= e($editingProduct['name'] ?? '') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <input type="text" name="category" id="product-category" required minlength="2"
                               value="<?= e($editingProduct['category'] ?? '') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                        <textarea name="description" id="product-description" rows="3"
                                  class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"><?= e($editingProduct['description'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta</label>
                        <input type="number" step="0.01" min="0" name="price" id="product-price" required
                               value="<?= e($editingProduct['price'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Costo</label>
                        <input type="number" step="0.01" min="0" name="cost" id="product-cost" required
                               value="<?= e($editingProduct['cost'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad en Stock</label>
                        <input type="number" min="0" name="stock_quantity" id="product-stock" required
                               value="<?= e($editingProduct['stock_quantity'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nivel Minimo de Stock</label>
                        <input type="number" min="0" name="min_stock_level" id="product-min" required
                               value="<?= e($editingProduct['min_stock_level'] ?? '0') ?>"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelProductForm" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                        Guardar
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
                                            <?= $isLow ? '<span class="ml-1 text-xs">(Bajo!)</span>' : '' ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Minimo</p>
                                        <p class="font-semibold"><?= (int) $product['min_stock_level'] ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php if ($isAdmin): ?>
                                <div class="flex justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                                    <button type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 edit-product"
                                            title="Editar"
                                            data-product='<?= htmlspecialchars(json_encode([
                                                'id' => (int) $product['id'],
                                                'name' => $product['name'],
                                                'category' => $product['category'],
                                                'description' => $product['description'],
                                                'price' => $product['price'],
                                                'cost' => $product['cost'],
                                                'stock_quantity' => $product['stock_quantity'],
                                                'min_stock_level' => $product['min_stock_level'],
                                            ]), ENT_QUOTES, 'UTF-8') ?>'>
                                        <i data-lucide="edit" class="h-4 w-4"></i>
                                    </button>
                                    <form action="<?= base_url('products/delete') ?>" method="POST" onsubmit="return confirm('Eliminar este producto?');">
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" class="p-2 rounded-full hover:bg-gray-100 text-gray-600" title="Eliminar">
                                            <i data-lucide="trash" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($isAdmin): ?>
<script>
    (function() {
        const formCard = document.getElementById('productFormCard');
        const toggleBtn = document.getElementById('toggleProductForm');
        const toggleText = document.getElementById('toggleProductFormText');
        const cancelBtn = document.getElementById('cancelProductForm');
        const title = document.getElementById('formTitle');
        const idField = document.getElementById('product-id');
        const nameField = document.getElementById('product-name');
        const categoryField = document.getElementById('product-category');
        const descriptionField = document.getElementById('product-description');
        const priceField = document.getElementById('product-price');
        const costField = document.getElementById('product-cost');
        const stockField = document.getElementById('product-stock');
        const minField = document.getElementById('product-min');

        if (formCard && !formCard.classList.contains('hidden') && toggleText) {
            toggleText.textContent = 'Cerrar formulario';
        }

        const resetForm = () => {
            title.textContent = 'Agregar Nuevo Producto';
            idField.value = '';
            nameField.value = '';
            categoryField.value = '';
            descriptionField.value = '';
            priceField.value = '0';
            costField.value = '0';
            stockField.value = '0';
            minField.value = '0';
        };

        const openForm = () => {
            formCard.classList.remove('hidden');
            if (toggleText) toggleText.textContent = 'Cerrar formulario';
            toggleBtn?.blur();
        };

        const closeForm = () => {
            formCard.classList.add('hidden');
            if (toggleText) toggleText.textContent = 'Nuevo Producto';
            resetForm();
        };

        toggleBtn?.addEventListener('click', () => {
            if (formCard.classList.contains('hidden')) {
                openForm();
            } else {
                closeForm();
            }
        });

        cancelBtn?.addEventListener('click', () => {
            closeForm();
        });

        document.querySelectorAll('.edit-product').forEach(btn => {
            btn.addEventListener('click', () => {
                const product = JSON.parse(btn.dataset.product);
                title.textContent = 'Editar Producto';
                idField.value = product.id || '';
                nameField.value = product.name || '';
                categoryField.value = product.category || '';
                descriptionField.value = product.description || '';
                priceField.value = product.price || 0;
                costField.value = product.cost || 0;
                stockField.value = product.stock_quantity || 0;
                minField.value = product.min_stock_level || 0;
                openForm();
            });
        });
    })();
</script>
<?php endif; ?>
