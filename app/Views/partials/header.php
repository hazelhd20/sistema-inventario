<?php
use App\Models\Product;

$user = auth_user();
$lowStockCount = count(Product::lowStock());
?>
<header class="bg-white border-b border-slate-200 shrink-0">
    <div class="px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div>
                <h1 class="text-lg font-semibold text-slate-800"><?= e(config('app.name')) ?></h1>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($lowStockCount > 0): ?>
                    <a href="<?= base_url('inventory?filter=low') ?>"
                       class="relative p-2 rounded-lg text-slate-500 hover:bg-pastel-rose/30 hover:text-slate-700 transition-colors">
                        <i data-lucide="bell" class="h-5 w-5"></i>
                        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center h-4 w-4 rounded-full bg-pastel-rose text-[10px] font-bold text-slate-700">
                            <?= $lowStockCount ?>
                        </span>
                    </a>
                <?php endif; ?>

                <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                    <div class="h-9 w-9 rounded-lg bg-pastel-blue flex items-center justify-center">
                        <i data-lucide="user" class="h-5 w-5 text-slate-600"></i>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-slate-700"><?= e($user['name'] ?? 'Invitado') ?></p>
                        <p class="text-xs text-slate-500 capitalize"><?= e($user['role'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
