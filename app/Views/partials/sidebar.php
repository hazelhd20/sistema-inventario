<?php
$user = auth_user();
$isAdmin = $user && ($user['role'] === 'admin');
?>
<div class="bg-white shadow-lg w-20 flex flex-col items-center py-4">
    <div class="mb-8 h-10 w-10">
        <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-10 w-10 object-contain rounded-full shadow-sm">
    </div>
    <?php
    $links = [
        ['path' => '/', 'icon' => 'home', 'label' => 'Dashboard'],
        ['path' => '/products', 'icon' => 'package', 'label' => 'Productos'],
        ['path' => '/inventory', 'icon' => 'archive', 'label' => 'Inventario'],
        ['path' => '/movements', 'icon' => 'repeat', 'label' => 'Movimientos'],
        ['path' => '/reports', 'icon' => 'bar-chart-2', 'label' => 'Reportes'],
    ];

    if ($isAdmin) {
        $links[] = ['path' => '/users', 'icon' => 'users', 'label' => 'Usuarios'];
    }

    foreach ($links as $link):
        $isActive = rtrim(current_path(), '/') === rtrim($link['path'], '/');
        ?>
        <a href="<?= base_url(ltrim($link['path'], '/')) ?>"
           class="sidebar-icon group <?= $isActive ? 'bg-gray-100' : '' ?>">
            <i data-lucide="<?= e($link['icon']) ?>"></i>
            <span class="sidebar-tooltip group-hover:scale-100 scale-0">
                <?= e($link['label']) ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>
