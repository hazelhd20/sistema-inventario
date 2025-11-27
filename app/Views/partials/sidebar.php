<?php
$user = auth_user();
$isAdmin = $user && ($user['role'] === 'admin');
$links = [
    ['path' => '/', 'icon' => 'home', 'label' => 'Dashboard'],
    ['path' => '/products', 'icon' => 'package', 'label' => 'Productos'],
    ['path' => '/inventory', 'icon' => 'archive', 'label' => 'Inventario'],
    ['path' => '/movements', 'icon' => 'repeat', 'label' => 'Movimientos'],
    ['path' => '/reports', 'icon' => 'bar-chart-2', 'label' => 'Reportes'],
];

if ($isAdmin) {
    $links[] = ['path' => '/movements/pending', 'icon' => 'clock', 'label' => 'Pendientes'];
    $links[] = ['path' => '/users', 'icon' => 'users', 'label' => 'Usuarios'];
}
?>
<aside class="bg-white border-r border-slate-200 w-16 lg:w-60 flex flex-col py-5 px-2 lg:px-3 shrink-0">
    <div class="flex items-center justify-center lg:justify-start gap-3 px-2 mb-6">
        <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-9 w-9 object-contain rounded-lg">
        <div class="hidden lg:block">
            <p class="text-[11px] uppercase tracking-wider text-slate-400 font-medium">Inventario</p>
            <p class="text-sm font-semibold text-slate-700 truncate"><?= e(config('app.name')) ?></p>
        </div>
    </div>

    <nav class="flex-1 space-y-1">
        <?php foreach ($links as $link): ?>
            <?php $isActive = rtrim(current_path(), '/') === rtrim($link['path'], '/'); ?>
            <a href="<?= base_url(ltrim($link['path'], '/')) ?>"
               class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                      <?= $isActive
                          ? 'bg-pastel-blue/40 text-slate-800'
                          : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors
                            <?= $isActive ? 'bg-pastel-blue text-slate-700' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200' ?>">
                    <i data-lucide="<?= e($link['icon']) ?>" class="h-[18px] w-[18px]"></i>
                </span>
                <span class="hidden lg:inline"><?= e($link['label']) ?></span>
                <span class="sidebar-tooltip lg:hidden"><?= e($link['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="pt-4 border-t border-slate-100 mt-4">
        <a href="<?= base_url('logout') ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-pastel-rose/30 hover:text-slate-800 transition-colors">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100">
                <i data-lucide="log-out" class="h-[18px] w-[18px]"></i>
            </span>
            <span class="hidden lg:inline">Cerrar sesión</span>
        </a>
    </div>
</aside>
