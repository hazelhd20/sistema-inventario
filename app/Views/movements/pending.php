<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">Aprobación</p>
            <h2 class="text-2xl font-semibold text-gray-800">Movimientos pendientes</h2>
            <p class="text-sm text-gray-500 mt-1">Solo los aprobados impactan el stock.</p>
        </div>
        <a href="<?= base_url('movements') ?>"
           class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-md hover:bg-gray-50 transition-colors duration-200">
            <i data-lucide="list" class="h-5 w-5 mr-1"></i>
            Ver aprobados
        </a>
    </div>

    <div class="flex flex-wrap gap-2 items-center">
        <?php
        $typeOptions = ['all' => 'Todos', 'in' => 'Entradas', 'out' => 'Salidas'];
        foreach ($typeOptions as $key => $label):
            $active = ($filters['type'] ?? 'all') === $key;
            ?>
            <a href="<?= base_url('movements/pending?type=' . $key) ?>"
               class="px-3 py-2 text-sm font-medium rounded-md border <?= $active ? 'bg-blue-pastel text-gray-900 border-transparent' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-gray-800">Por aprobar</h3>
            <span class="text-sm text-gray-500"><?= count($movements) ?> pendiente(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-soft min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitado por</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($movements as $movement): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?= date('d/m/Y H:i', strtotime($movement['date'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= e($movement['product_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= e($movement['product_category']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $movement['type'] === 'in' ? 'bg-green-pastel text-green-800' : 'bg-pink-pastel text-pink-800' ?>">
                                <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            <?= (int) $movement['quantity'] ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <?= e($movement['notes'] ?: '-') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            <?= e($movement['user_name'] ?: 'Sistema') ?>
                            <div class="text-xs text-gray-400"><?= e($movement['user_role'] ?? '') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <form action="<?= base_url('movements/approve') ?>" method="POST" class="inline">
                                    <input type="hidden" name="id" value="<?= (int) $movement['id'] ?>">
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-pastel text-gray-800 rounded-md hover:bg-green-400 transition-colors duration-150">
                                        <i data-lucide="check" class="h-4 w-4 mr-1"></i>
                                        Aprobar
                                    </button>
                                </form>
                                <form action="<?= base_url('movements/reject') ?>" method="POST" class="inline" onsubmit="return confirm('�Rechazar y eliminar este movimiento?');">
                                    <input type="hidden" name="id" value="<?= (int) $movement['id'] ?>">
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-pink-pastel text-gray-800 rounded-md hover:bg-pink-300 transition-colors duration-150">
                                        <i data-lucide="x" class="h-4 w-4 mr-1"></i>
                                        Rechazar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($movements)): ?>
            <div class="text-center py-10">
                <p class="text-gray-500">No hay movimientos pendientes.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
