<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-slate-800">Movimientos Pendientes</h2>
            <p class="text-sm text-slate-500 mt-1">Aprueba o rechaza los movimientos solicitados</p>
        </div>
        <a href="<?= base_url('movements') ?>"
           class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <i data-lucide="list" class="h-4 w-4"></i>
            Ver aprobados
        </a>
    </div>

    <!-- Filtros -->
    <div class="flex flex-wrap gap-2">
        <?php
        $typeOptions = ['all' => 'Todos', 'in' => 'Entradas', 'out' => 'Salidas'];
        foreach ($typeOptions as $key => $label):
            $active = ($filters['type'] ?? 'all') === $key;
        ?>
            <a href="<?= base_url('movements/pending?type=' . $key) ?>"
               class="px-3 py-2 text-sm font-medium rounded-lg border <?= $active ? 'bg-pastel-blue text-slate-700 border-pastel-blue' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Por aprobar</h3>
            <span class="text-sm text-slate-500"><?= count($movements) ?> pendiente(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-soft w-full text-sm">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Producto</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipo</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Cantidad</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Notas</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Solicitado por</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($movements as $movement): ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-5 py-4 text-slate-600">
                                <?= date('d/m/Y H:i', strtotime($movement['date'])) ?>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-800"><?= e($movement['product_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= e($movement['product_category']) ?></p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    <?= $movement['type'] === 'in' ? 'bg-pastel-mint text-slate-700' : 'bg-pastel-rose text-slate-700' ?>">
                                    <?= $movement['type'] === 'in' ? 'Entrada' : 'Salida' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800"><?= (int) $movement['quantity'] ?></td>
                            <td class="px-5 py-4 text-slate-600"><?= e($movement['notes'] ?: '-') ?></td>
                            <td class="px-5 py-4">
                                <p class="text-slate-600"><?= e($movement['user_name'] ?: 'Sistema') ?></p>
                                <p class="text-xs text-slate-400 capitalize"><?= e($movement['user_role'] ?? '') ?></p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <form action="<?= base_url('movements/approve') ?>" method="POST">
                                        <input type="hidden" name="id" value="<?= (int) $movement['id'] ?>">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-mint text-slate-700 hover:bg-pastel-mint/80 transition-colors">
                                            <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                            Aprobar
                                        </button>
                                    </form>
                                    <form action="<?= base_url('movements/reject') ?>" method="POST" onsubmit="return confirm('¿Rechazar este movimiento?');">
                                        <input type="hidden" name="id" value="<?= (int) $movement['id'] ?>">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-lg bg-pastel-rose text-slate-700 hover:bg-pastel-rose/80 transition-colors">
                                            <i data-lucide="x" class="h-3.5 w-3.5"></i>
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
            <div class="text-center py-16">
                <i data-lucide="check-circle" class="h-12 w-12 text-pastel-mint mx-auto mb-3"></i>
                <p class="text-slate-500">No hay movimientos pendientes</p>
            </div>
        <?php endif; ?>
    </div>
</div>
