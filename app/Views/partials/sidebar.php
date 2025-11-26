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
<aside class="bg-white/80 backdrop-blur-md border-r border-white/50 shadow-xl w-20 md:w-64 flex flex-col py-6 px-3 md:px-4">
    <div class="flex items-center justify-center md:justify-start gap-3 px-1 mb-8">
        <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-10 w-10 object-contain rounded-full shadow-sm ring-1 ring-white/60">
        <div class="hidden md:block">
            <p class="text-xs text-gray-500">Inventario</p>
            <p class="text-sm font-semibold text-gray-800"><?= e(config('app.name')) ?></p>
        </div>
    </div>
    <nav class="space-y-2">
        <?php foreach ($links as $link): ?>
            <?php $isActive = rtrim(current_path(), '/') === rtrim($link['path'], '/'); ?>
            <a href="<?= base_url(ltrim($link['path'], '/')) ?>"
               class="group relative flex items-center gap-3 px-2 py-2 rounded-xl text-sm font-medium transition <?= $isActive ? 'bg-blue-pastel/70 text-gray-900 shadow-sm' : 'text-gray-600 hover:bg-white/70 hover:shadow-sm' ?>">
                <span class="sidebar-icon <?= $isActive ? 'active' : '' ?>">
                    <i data-lucide="<?= e($link['icon']) ?>" class="h-5 w-5"></i>
                </span>
                <span class="hidden md:inline"><?= e($link['label']) ?></span>
                <span class="sidebar-tooltip md:hidden group-hover:scale-100 scale-0">
                    <?= e($link['label']) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
