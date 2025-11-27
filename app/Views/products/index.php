<?php
$isAdmin = $isAdmin ?? false;
$categories = $categories ?? [];
$showForm = $isAdmin && (bool) $editingProduct;
?>
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Productos</h2>
            <p class="text-sm text-slate-500 mt-1">Gestión del catálogo de productos</p>
        </div>
        <?php if ($isAdmin): ?>
            <div class="flex items-center gap-2">
                <button type="button" id="toggleProductForm"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg font-medium text-sm hover:bg-pastel-blue/80 transition-colors">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span id="toggleProductFormText">Nuevo Producto</span>
                </button>
                <button type="button" id="toggleCategoryForm"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg font-medium text-sm hover:bg-slate-50 transition-colors">
                    <i data-lucide="tag" class="h-4 w-4"></i>
                    <span>Nueva Categoría</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Búsqueda -->
    <form method="GET" action="<?= base_url('products') ?>">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
            <input id="product-search" type="text" name="q" placeholder="Buscar productos..." value="<?= e($search) ?>"
                   class="w-full pl-10 pr-10 py-3 bg-white border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            <button type="button" id="clear-product-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 hidden">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
    </form>

    <!-- Grid de productos -->
    <?php if (count($products) === 0): ?>
        <div class="text-center py-16 bg-white rounded-xl border border-slate-200">
            <i data-lucide="package" class="h-12 w-12 text-slate-300 mx-auto mb-3"></i>
            <p class="text-slate-500">No se encontraron productos</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php foreach ($products as $product): ?>
                <?php
                $isLow = $product['stock_quantity'] <= $product['min_stock_level'];
                $isInactive = empty($product['active']);
                $movementsCount = (int) ($product['movements_count'] ?? 0);
                ?>
                <div class="bg-white rounded-xl border <?= $isLow ? 'border-pastel-rose bg-pastel-rose/10' : 'border-slate-200' ?> p-5 flex flex-col">
                    <div class="flex-1">
                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium bg-pastel-blue/50 text-slate-700">
                                <?= e($product['category']) ?>
                            </span>
                            <span class="px-2 py-0.5 rounded text-xs font-medium <?= $isInactive ? 'bg-slate-200 text-slate-500' : 'bg-pastel-mint text-slate-700' ?>">
                                <?= $isInactive ? 'Inactivo' : 'Activo' ?>
                            </span>
                        </div>

                        <!-- Info -->
                        <h3 class="font-semibold text-slate-800 leading-snug line-clamp-2 mb-1"><?= e($product['name']) ?></h3>
                        <p class="text-sm text-slate-500 line-clamp-2 mb-4"><?= e($product['description']) ?></p>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-slate-500">Precio</p>
                                <p class="font-semibold text-slate-800">$<?= number_format((float) $product['price'], 2) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Costo</p>
                                <p class="font-semibold text-slate-800">$<?= number_format((float) $product['cost'], 2) ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Stock</p>
                                <p class="font-semibold <?= $isLow ? 'text-red-600' : 'text-slate-800' ?>">
                                    <?= (int) $product['stock_quantity'] ?>
                                    <?php if ($isLow): ?><span class="text-xs font-normal">(bajo)</span><?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Mínimo</p>
                                <p class="font-semibold text-slate-800"><?= (int) $product['min_stock_level'] ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <?php if ($isAdmin): ?>
                        <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-slate-100">
                            <button type="button" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors edit-product"
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
                                <i data-lucide="edit" class="h-3.5 w-3.5"></i>
                                Editar
                            </button>
                            <?php if ($isInactive): ?>
                                <form action="<?= base_url('products/reactivate') ?>" method="POST">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-mint text-slate-700 hover:bg-pastel-mint/80 transition-colors">
                                        <i data-lucide="refresh-cw" class="h-3.5 w-3.5"></i>
                                        Reactivar
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?= base_url('products/deactivate') ?>" method="POST">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-peach text-slate-700 hover:bg-pastel-peach/80 transition-colors">
                                        <i data-lucide="pause" class="h-3.5 w-3.5"></i>
                                        Inactivar
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if ($movementsCount === 0): ?>
                                <form action="<?= base_url('products/delete') ?>" method="POST" onsubmit="return confirm('¿Eliminar este producto?');">
                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-rose text-slate-700 hover:bg-pastel-rose/80 transition-colors">
                                        <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                        Eliminar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($isAdmin): ?>
<!-- Modal Producto -->
<div id="productModal" class="<?= $showForm ? '' : 'hidden' ?> fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" id="productModalContent">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-semibold text-slate-800" id="formTitle">
                    <?= $editingProduct ? 'Editar Producto' : 'Nuevo Producto' ?>
                </h3>
                <p class="text-sm text-slate-500">Campos con <span class="text-red-500">*</span> son obligatorios</p>
            </div>
            <button type="button" id="closeProductModal" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <form id="productForm" action="<?= base_url('products/save') ?>" method="POST" class="p-6">
            <input type="hidden" name="id" id="product-id" value="<?= $editingProduct ? (int) $editingProduct['id'] : '' ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="product-name" required minlength="3"
                           value="<?= e($editingProduct['name'] ?? '') ?>"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoría <span class="text-red-500">*</span></label>
                    <select name="category_id" id="product-category" required
                            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                        <option value="">Seleccionar...</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>" <?= isset($editingProduct['category_id']) && (int) $editingProduct['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripción</label>
                    <textarea name="description" id="product-description" rows="2"
                              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue"><?= e($editingProduct['description'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" id="product-price" required
                           value="<?= e($editingProduct['price'] ?? '0') ?>"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Costo <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="cost" id="product-cost" required
                           value="<?= e($editingProduct['cost'] ?? '0') ?>"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Stock <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="stock_quantity" id="product-stock" required
                           value="<?= e($editingProduct['stock_quantity'] ?? '0') ?>"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Stock Mínimo <span class="text-red-500">*</span></label>
                    <input type="number" min="0" name="min_stock_level" id="product-min" required
                           value="<?= e($editingProduct['min_stock_level'] ?? '0') ?>"
                           class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                <button type="button" id="cancelProductForm" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg text-sm font-medium hover:bg-pastel-blue/80">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Categoría -->
<div id="categoryModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md" id="categoryModalContent">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="text-lg font-semibold text-slate-800">Nueva Categoría</h3>
            <button type="button" id="closeCategoryModal" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>
        <form action="<?= base_url('categories/save') ?>" method="POST" class="p-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="category-name" required minlength="3"
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue">
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="cancelCategoryForm" class="px-4 py-2.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2.5 bg-pastel-blue text-slate-700 rounded-lg text-sm font-medium hover:bg-pastel-blue/80">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
(function() {
    const searchInput = document.getElementById('product-search');
    const clearBtn = document.getElementById('clear-product-search');
    if (!searchInput || !clearBtn) return;
    const form = searchInput.closest('form');
    let debounceId;
    const submitForm = () => form?.requestSubmit?.() || form?.submit();
    const toggleClear = () => clearBtn.classList.toggle('hidden', !searchInput.value);
    toggleClear();
    searchInput.focus();
    searchInput.addEventListener('input', () => {
        toggleClear();
        clearTimeout(debounceId);
        debounceId = setTimeout(submitForm, 400);
    });
    clearBtn.addEventListener('click', () => {
        searchInput.value = '';
        toggleClear();
        submitForm();
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
    const fields = {
        id: document.getElementById('product-id'),
        name: document.getElementById('product-name'),
        category: document.getElementById('product-category'),
        description: document.getElementById('product-description'),
        price: document.getElementById('product-price'),
        cost: document.getElementById('product-cost'),
        stock: document.getElementById('product-stock'),
        min: document.getElementById('product-min')
    };
    const categoryModal = document.getElementById('categoryModal');
    const toggleCategoryBtn = document.getElementById('toggleCategoryForm');
    const closeCategoryBtn = document.getElementById('closeCategoryModal');
    const cancelCategoryBtn = document.getElementById('cancelCategoryForm');

    if (!modal || !toggleBtn) return;
    if (modal.parentElement !== document.body) document.body.appendChild(modal);
    if (categoryModal?.parentElement !== document.body) document.body.appendChild(categoryModal);

    const resetForm = () => {
        title.textContent = 'Nuevo Producto';
        Object.values(fields).forEach(f => f && (f.value = f.tagName === 'SELECT' ? '' : (f.type === 'number' ? '0' : '')));
    };

    const openModal = () => { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
    const closeModal = () => { modal.classList.add('hidden'); document.body.style.overflow = ''; resetForm(); };

    const fillForm = (p) => {
        title.textContent = 'Editar Producto';
        fields.id.value = p.id || '';
        fields.name.value = p.name || '';
        fields.category.value = p.category_id || '';
        fields.description.value = p.description || '';
        fields.price.value = p.price ?? 0;
        fields.cost.value = p.cost ?? 0;
        fields.stock.value = p.stock_quantity ?? 0;
        fields.min.value = p.min_stock_level ?? 0;
    };

    toggleBtn.addEventListener('click', () => { resetForm(); openModal(); });
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => e.target === modal && closeModal());
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeModal());

    document.querySelectorAll('.edit-product').forEach(btn => {
        btn.addEventListener('click', () => { fillForm(JSON.parse(btn.dataset.product)); openModal(); });
    });

    const openCategory = () => { categoryModal?.classList.remove('hidden'); document.body.style.overflow = 'hidden'; };
    const closeCategory = () => { categoryModal?.classList.add('hidden'); document.body.style.overflow = ''; };
    toggleCategoryBtn?.addEventListener('click', openCategory);
    closeCategoryBtn?.addEventListener('click', closeCategory);
    cancelCategoryBtn?.addEventListener('click', closeCategory);
    categoryModal?.addEventListener('click', (e) => e.target === categoryModal && closeCategory());

    const initial = <?= $editingProduct ? json_encode([
        'id' => (int) $editingProduct['id'],
        'name' => $editingProduct['name'],
        'category_id' => $editingProduct['category_id'],
        'description' => $editingProduct['description'],
        'price' => $editingProduct['price'],
        'cost' => $editingProduct['cost'],
        'stock_quantity' => $editingProduct['stock_quantity'],
        'min_stock_level' => $editingProduct['min_stock_level'],
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) : 'null' ?>;
    if (initial) { fillForm(initial); openModal(); }
})();
</script>
<?php endif; ?>
