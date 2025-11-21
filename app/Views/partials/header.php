<?php
use App\Models\Product;

$user = auth_user();
$lowStockCount = count(Product::lowStock());
?>
<header class="bg-white shadow-sm z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-3">
                <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-8 w-8 object-contain rounded-full shadow-sm">
                <h1 class="text-xl font-semibold text-gray-800">
                    <?= e(config('app.name')) ?>
                </h1>
            </div>
            <div class="flex items-center">
                <div class="relative">
                    <button class="p-2 rounded-full text-gray-600 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-pastel">
                        <span class="sr-only">Ver notificaciones</span>
                        <i data-lucide="bell" class="h-6 w-6"></i>
                        <?php if ($lowStockCount > 0): ?>
                            <span class="absolute -top-1 -right-1 block h-4 w-4 rounded-full bg-pink-pastel text-[10px] text-center font-bold text-gray-800">
                                <?= $lowStockCount ?>
                            </span>
                        <?php endif; ?>
                    </button>
                </div>
                <div class="ml-3 relative flex items-center">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-blue-pastel flex items-center justify-center">
                            <i data-lucide="user" class="h-5 w-5 text-gray-700"></i>
                        </div>
                        <div class="ml-2">
                            <div class="text-sm font-medium text-gray-800">
                                <?= e($user['name'] ?? 'Invitado') ?>
                            </div>
                            <div class="text-xs text-gray-500 capitalize">
                                <?= e($user['role'] ?? '') ?>
                            </div>
                        </div>
                    </div>
                    <a href="<?= base_url('logout') ?>" class="ml-4 p-1 rounded-full text-gray-600 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-pink-pastel">
                        <i data-lucide="log-out" class="h-5 w-5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
