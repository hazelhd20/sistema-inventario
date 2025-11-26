<?php
use App\Models\Product;

$user = auth_user();
$lowStockCount = count(Product::lowStock());
?>
<header class="bg-white/80 backdrop-blur-md border-b border-white/50 shadow-sm z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-3">
                <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-9 w-9 object-contain rounded-full shadow-sm ring-1 ring-white/60">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Panel</p>
                    <h1 class="text-xl font-semibold text-gray-800">
                        <?= e(config('app.name')) ?>
                    </h1>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <button class="p-2 rounded-xl text-gray-600 hover:text-gray-800 hover:bg-gray-50/80 border border-white/70 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-pastel">
                        <span class="sr-only">Ver notificaciones</span>
                        <i data-lucide="bell" class="h-5 w-5"></i>
                        <?php if ($lowStockCount > 0): ?>
                            <span class="absolute -top-1 -right-1 block h-4 w-4 rounded-full bg-pink-pastel text-[10px] text-center font-bold text-gray-800">
                                <?= $lowStockCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                </div>
                <div class="flex items-center gap-2 bg-white/70 border border-white/80 rounded-2xl px-3 py-1.5 shadow-sm">
                    <div class="h-9 w-9 rounded-xl bg-blue-pastel flex items-center justify-center">
                        <i data-lucide="user" class="h-5 w-5 text-gray-700"></i>
                    </div>
                    <div class="leading-tight">
                        <div class="text-sm font-semibold text-gray-800">
                            <?= e($user['name'] ?? 'Invitado') ?>
                        </div>
                        <div class="text-xs text-gray-500 capitalize">
                            <?= e($user['role'] ?? '') ?>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('logout') ?>" class="p-2 rounded-xl text-gray-600 hover:text-gray-800 hover:bg-pink-pastel/60 border border-white/70 shadow-sm focus:outline-none focus:ring-2 focus:ring-pink-pastel">
                    <i data-lucide="log-out" class="h-5 w-5"></i>
                </a>
            </div>
        </div>
    </div>
</header>
