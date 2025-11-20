<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Movimientos de Inventario</h2>
    </div>

    <div class="card">
        <h3 class="text-lg font-semibold mb-4">Registrar Movimiento</h3>
        <form action="<?= base_url('movements/save') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                <select name="product_id" required
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
                    <option value="">Seleccionar producto</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= (int) $product['id'] ?>">
                            <?= e($product['name']) ?> (Stock: <?= (int) $product['stock_quantity'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Movimiento</label>
                <div class="flex">
                    <label class="inline-flex items-center mr-4">
                        <input type="radio" name="type" value="in" checked class="h-4 w-4 text-blue-pastel">
                        <span class="ml-2 text-gray-700">Entrada</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="type" value="out" class="h-4 w-4 text-pink-pastel">
                        <span class="ml-2 text-gray-700">Salida</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="number" name="quantity" min="1" required value="1"
                       class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                <input type="text" name="notes"
                       class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel"
                       placeholder="Motivo del movimiento">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-pastel rounded-md text-gray-800 hover:bg-blue-400 transition-colors duration-200">
                    Registrar
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <form method="GET" action="<?= base_url('movements') ?>" class="mb-4 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="q" placeholder="Buscar movimientos..." value="<?= e($filters['search'] ?? '') ?>"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-pastel">
            </div>
            <div class="flex space-x-2">
                <div class="inline-flex rounded-md shadow-sm">
                    <?php
                    $typeOptions = ['all' => 'Todos', 'in' => 'Entradas', 'out' => 'Salidas'];
                    foreach ($typeOptions as $key => $label):
                        $active = ($filters['type'] ?? 'all') === $key;
                        $classes = 'px-3 py-2 text-xs font-medium border border-gray-300';
                        if ($key === 'all') {
                            $classes .= ' rounded-l-md';
                        } elseif ($key === 'out') {
                            $classes .= ' rounded-r-md border-l-0';
                        } else {
                            $classes .= ' border-l-0';
                        }
                        ?>
                        <a href="<?= base_url('movements?type=' . $key . '&range=' . e($filters['date_range'])) ?>"
                           class="<?= $classes ?> <?= $active ? 'bg-blue-pastel text-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="inline-flex rounded-md shadow-sm">
                    <?php
                    $rangeOptions = ['all' => 'Todo', 'today' => 'Hoy', 'week' => 'Semana', 'month' => 'Mes', 'quarter' => 'Trimestre'];
                    foreach ($rangeOptions as $key => $label):
                        $active = ($filters['date_range'] ?? 'all') === $key;
                        $classes = 'px-3 py-2 text-xs font-medium border border-gray-300';
                        if ($key === 'all') {
                            $classes .= ' rounded-l-md';
                        } elseif ($key === 'quarter') {
                            $classes .= ' rounded-r-md border-l-0';
                        } else {
                            $classes .= ' border-l-0';
                        }
                        ?>
                        <a href="<?= base_url('movements?range=' . $key . '&type=' . e($filters['type'])) ?>"
                           class="<?= $classes ?> <?= $active ? 'bg-peach-pastel text-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($movements as $movement): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($movement['date'])) ?>
                                <div class="text-xs text-gray-400"><?= date('H:i', strtotime($movement['date'])) ?></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= e($movement['product_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= e($movement['product_category']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $movement['type'] === 'in' ? 'bg-green-pastel text-green-800' : 'bg-pink-pastel text-pink-800' ?>">
                                <?php if ($movement['type'] === 'in'): ?>
                                    <i data-lucide="arrow-up" class="h-3 w-3 mr-1"></i> Entrada
                                <?php else: ?>
                                    <i data-lucide="arrow-down" class="h-3 w-3 mr-1"></i> Salida
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= (int) $movement['quantity'] ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?= e($movement['notes'] ?: '-') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                <?= e($movement['user_name'] ?: 'Sistema') ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($movements)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500">No se encontraron movimientos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
