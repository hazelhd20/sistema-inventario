<?php
$isAdmin = $isAdmin ?? false;
$categories = $categories ?? [];
$showForm = $isAdmin && (bool) $editingProduct;
$openModal = $showForm;
?>
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Gestion de Productos</h2>
        <?php if ($isAdmin): ?>
            <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                <button type="button" id="toggleProductForm"
                        class="inline-flex items-center px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                    <i data-lucide="plus" class="h-5 w-5 mr-1"></i>
                    <span id="toggleProductFormText">Nuevo Producto</span>
                </button>
                <button type="button" id="toggleCategoryForm"
                        class="inline-flex items-center px-4 py-2 bg-purple-100 rounded-md text-purple-900 hover:bg-purple-200 transition-colors duration-200">
                    <i data-lucide="tag" class="h-5 w-5 mr-1"></i>
                    <span>Agregar Categoria</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($isAdmin): ?>
        <div id="productModal" class="<?= $openModal ? '' : 'hidden' ?> fixed inset-0 z-40 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm px-4 py-8">
            <div class="card modal-card w-full max-w-4xl relative max-h-[85vh] overflow-y-auto" id="productModalContent">
                <button type="button" id="closeProductModal" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700" aria-label="Cerrar">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
                <div class="mb-4 pr-8">
                    <h3 class="text-lg font-semibold" id="formTitle">
                        <?= $editingProduct ? 'Editar Producto' : 'Agregar Nuevo Producto' ?>
                    </h3>
                    <p class="text-sm text-gray-500">Campos marcados con <span class="text-red-500" aria-hidden="true">*</span> son obligatorios.</p>
                </div>
                <form id="productForm" action="<?= base_url('products/save') ?>" method="POST" class="form-modern space-y-4">
                    <input type="hidden" name="id" id="product-id" value="<?= $editingProduct ? (int) $editingProduct['id'] : '' ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Producto <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input type="text" name="name" id="product-name" required minlength="3"
                                   value="<?= e($editingProduct['name'] ?? '') ?>"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria <span class="text-red-500" aria-hidden="true">*</span></label>
                            <select name="category_id" id="product-category" required
                                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                                <option value="">Seleccionar categoria</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['id'] ?>" <?= isset($editingProduct['category_id']) && (int) $editingProduct['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                                        <?= e($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripcion</label>
                            <textarea name="description" id="product-description" rows="3"
                                      class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"><?= e($editingProduct['description'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input type="number" step="0.01" min="0" name="price" id="product-price" required
                                   value="<?= e($editingProduct['price'] ?? '0') ?>"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Costo <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input type="number" step="0.01" min="0" name="cost" id="product-cost" required
                                   value="<?= e($editingProduct['cost'] ?? '0') ?>"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad en Stock <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input type="number" min="0" name="stock_quantity" id="product-stock" required
                                   value="<?= e($editingProduct['stock_quantity'] ?? '0') ?>"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nivel Minimo de Stock <span class="text-red-500" aria-hidden="true">*</span></label>
                            <input type="number" min="0" name="min_stock_level" id="product-min" required
                                   value="<?= e($editingProduct['min_stock_level'] ?? '0') ?>"
                                   class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelProductForm" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isAdmin): ?>
        <div id="categoryModal" class="hidden fixed inset-0 z-40 flex items-center justify-center bg-gray-900/40 backdrop-blur-sm px-4 py-8">
            <div class="card modal-card w-full max-w-md relative" id="categoryModalContent">
                <button type="button" id="closeCategoryModal" class="absolute right-3 top-3 text-gray-500 hover:text-gray-700" aria-label="Cerrar">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
                <div class="mb-4 pr-8">
                    <h3 class="text-lg font-semibold">Agregar Categoria</h3>
                    <p class="text-sm text-gray-500">Las categorias son seleccionadas por los empleados en los productos.</p>
                </div>
                <form action="<?= base_url('categories/save') ?>" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la categoria <span class="text-red-500" aria-hidden="true">*</span></label>
                        <input type="text" name="name" id="category-name" required minlength="3"
                               class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelCategoryForm" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-100 rounded-md text-purple-900 hover:bg-purple-200 transition-colors duration-200">
                            Guardar Categoria
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <form method="GET" action="<?= base_url('products') ?>" class="mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
            </div>
            <input id="product-search" type="text" name="q" placeholder="Buscar productos..." value="<?= e($search) ?>"
                   class="w-full pl-10 pr-10 py-3 border border-white/60 bg-white/80 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-pastel">
            <button type="button" id="clear-product-search" class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600 focus:outline-none hidden" aria-label="Limpiar busqueda">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
    </form>

    <?php if (count($products) === 0): ?>
        <div class="text-center py-12">
            <p class="text-gray-500">No se encontraron productos.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
                <?php
                $isLow = $product['stock_quantity'] <= $product['min_stock_level'];
                $isInactive = empty($product['active']);
                $movementsCount = (int) ($product['movements_count'] ?? 0);
                ?>
                <div class="card <?= $isLow ? 'low-stock' : '' ?>">
                    <div class="flex flex-col h-full">
                        <div class="flex-1">
                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="product-chip product-chip--category">
                                        <?= e($product['category']) ?>
                                    </span>
                                    <span class="product-chip <?= $isInactive ? 'product-chip--inactive' : 'product-chip--active' ?>">
                                        <?= $isInactive ? 'Inactivo' : 'Activo' ?>
                                    </span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 leading-tight line-clamp-2"><?= e($product['name']) ?></h3>
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    <?= e($product['description']) ?>
                                </p>
                                <div class="grid grid-cols-2 gap-2">
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
                        </div>
                        <?php if ($isAdmin): ?>
                            <div class="flex flex-wrap items-center justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                                <button type="button" class="p-2 rounded-full hover:bg-gray-100 text-gray-600 edit-product"
                                        title="Editar"
                                        data-product='<?= htmlspecialchars(json_encode([
                                            'id' => (int) $product['id'],
                                            'name' => $product['name'],
                                            'category_id' => $product['category_id'],
                                            'description' => $product['description'],
                                            'price' => $product['price'],
                                            'cost' => $product['cost'],
                                            'stock_quantity' => $product['stock_quantity'],
                                            'min_stock_level' => $product['min_stock_level'],
                                        ]), ENT_QUOTES, 'UTF-8') ?>'>
                                    <i data-lucide="edit" class="h-4 w-4"></i>
                                </button>
                                <?php if ($isInactive): ?>
                                    <form action="<?= base_url('products/reactivate') ?>" method="POST">
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" class="px-3 py-1 rounded-full bg-green-pastel text-green-900 hover:bg-green-300 text-xs font-semibold">
                                            Reactivar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?= base_url('products/deactivate') ?>" method="POST">
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" class="px-3 py-1 rounded-full bg-amber-200 text-amber-900 hover:bg-amber-300 text-xs font-semibold">
                                            Inactivar
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($movementsCount === 0): ?>
                                    <form action="<?= base_url('products/delete') ?>" method="POST" onsubmit="return confirm('Eliminar este producto?');">
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" class="p-2 rounded-full hover:bg-gray-100 text-gray-600" title="Eliminar">
                                            <i data-lucide="trash" class="h-4 w-4"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button type="button" class="p-2 rounded-full text-gray-400 cursor-not-allowed" title="Este producto no puede eliminarse porque tiene transacciones registradas.">
                                        <i data-lucide="lock" class="h-4 w-4"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    (function() {
        const searchInput = document.getElementById('product-search');
        const clearBtn = document.getElementById('clear-product-search');
        if (!searchInput || !clearBtn) return;
        const form = searchInput.closest('form');
        let debounceId;
        const submitForm = () => {
            if (!form) return;
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        };
        const toggleClear = () => {
            clearBtn.classList[searchInput.value ? 'remove' : 'add']('hidden');
        };
        const restoreFocus = () => {
            searchInput.focus({ preventScroll: true });
            const end = searchInput.value.length;
            searchInput.setSelectionRange(end, end);
        };
        toggleClear();
        restoreFocus();
        searchInput.addEventListener('input', () => {
            toggleClear();
            clearTimeout(debounceId);
            debounceId = setTimeout(submitForm, 400);
        });
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            toggleClear();
            submitForm();
            restoreFocus();
        });
    })();
</script>

<?php if ($isAdmin): ?>
<script>
    (function() {
        const modal = document.getElementById('productModal');
        const toggleBtn = document.getElementById('toggleProductForm');
        const toggleText = document.getElementById('toggleProductFormText');
        const closeBtn = document.getElementById('closeProductModal');
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
        const categoryModal = document.getElementById('categoryModal');
        const toggleCategoryBtn = document.getElementById('toggleCategoryForm');
        const closeCategoryBtn = document.getElementById('closeCategoryModal');
        const cancelCategoryBtn = document.getElementById('cancelCategoryForm');
        const categoryNameField = document.getElementById('category-name');

        if (!modal || !toggleBtn || !title || !idField || !nameField || !categoryField || !descriptionField || !priceField || !costField || !stockField || !minField) {
            return;
        }

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        const setToggleText = (isOpen) => {
            if (toggleText) {
                toggleText.textContent = isOpen ? 'Cerrar' : 'Nuevo Producto';
            }
        };
        const resetCategoryForm = () => {
            if (categoryNameField) {
                categoryNameField.value = '';
            }
        };

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

        const openModal = () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setToggleText(true);
            toggleBtn.blur();
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            setToggleText(false);
            resetForm();
        };

        const fillForm = (product) => {
            title.textContent = 'Editar Producto';
            idField.value = product.id || '';
            nameField.value = product.name || '';
            categoryField.value = product.category_id || '';
            descriptionField.value = product.description || '';
            priceField.value = product.price ?? 0;
            costField.value = product.cost ?? 0;
            stockField.value = product.stock_quantity ?? 0;
            minField.value = product.min_stock_level ?? 0;
        };

        toggleBtn.addEventListener('click', () => {
            resetForm();
            openModal();
        });

        cancelBtn?.addEventListener('click', () => {
            closeModal();
        });

        closeBtn?.addEventListener('click', () => {
            closeModal();
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
            if (event.key === 'Escape' && categoryModal && !categoryModal.classList.contains('hidden')) {
                closeCategory();
            }
        });

        document.querySelectorAll('.edit-product').forEach(btn => {
            btn.addEventListener('click', () => {
                const product = JSON.parse(btn.dataset.product);
                fillForm(product);
                openModal();
            });
        });

        const initialProduct = <?= $editingProduct ? json_encode([
            'id' => (int) $editingProduct['id'],
            'name' => $editingProduct['name'],
            'category_id' => $editingProduct['category_id'],
            'description' => $editingProduct['description'],
            'price' => $editingProduct['price'],
            'cost' => $editingProduct['cost'],
            'stock_quantity' => $editingProduct['stock_quantity'],
            'min_stock_level' => $editingProduct['min_stock_level'],
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : 'null' ?>;

        if (initialProduct) {
            fillForm(initialProduct);
            openModal();
        }

        const openCategory = () => {
            if (!categoryModal) return;
            categoryModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            resetCategoryForm();
            categoryNameField?.focus();
        };
        const closeCategory = () => {
            if (!categoryModal) return;
            categoryModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            resetCategoryForm();
        };

        toggleCategoryBtn?.addEventListener('click', openCategory);
        closeCategoryBtn?.addEventListener('click', closeCategory);
        cancelCategoryBtn?.addEventListener('click', closeCategory);
        categoryModal?.addEventListener('click', (event) => {
            if (event.target === categoryModal) {
                closeCategory();
            }
        });
    })();
</script>
<?php endif; ?>
