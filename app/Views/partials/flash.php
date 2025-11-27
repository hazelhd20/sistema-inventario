<?php
$alerts = [];
$successMessage = $message ?? flash('success');
$errorMessage = $error ?? flash('error');
$warningMessage = flash('warning');
$infoMessage = flash('info');

if ($successMessage) {
    $alerts[] = ['type' => 'success', 'text' => $successMessage];
}
if ($errorMessage) {
    $alerts[] = ['type' => 'error', 'text' => $errorMessage];
}
if ($warningMessage) {
    $alerts[] = ['type' => 'warning', 'text' => $warningMessage];
}
if ($infoMessage) {
    $alerts[] = ['type' => 'info', 'text' => $infoMessage];
}
?>

<?php if (!empty($alerts)): ?>
    <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm w-full">
        <?php foreach ($alerts as $index => $alert): ?>
            <?php
            $styles = match ($alert['type']) {
                'success' => 'bg-green-50 border-green-200 text-green-800',
                'error' => 'bg-red-50 border-red-200 text-red-800',
                'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
                'info' => 'bg-primary-50 border-primary-200 text-primary-800',
                default => 'bg-slate-50 border-slate-200 text-slate-800',
            };
            $icon = match ($alert['type']) {
                'success' => 'check-circle',
                'error' => 'x-circle',
                'warning' => 'alert-triangle',
                'info' => 'info',
                default => 'info',
            };
            ?>
            <div class="flash-item flex items-start gap-3 p-4 rounded-lg border shadow-sm <?= $styles ?>" data-index="<?= $index ?>">
                <i data-lucide="<?= $icon ?>" class="h-5 w-5 shrink-0 mt-0.5"></i>
                <p class="flex-1 text-sm"><?= e($alert['text']) ?></p>
                <button type="button" class="text-current opacity-60 hover:opacity-100 flash-close" aria-label="Cerrar">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        (function() {
            const container = document.getElementById('flash-container');
            if (!container) return;
            const closeAlert = (el) => {
                el.style.opacity = '0';
                el.style.transform = 'translateX(1rem)';
                el.style.transition = 'all 0.2s ease';
                setTimeout(() => el.remove(), 200);
            };
            container.querySelectorAll('.flash-close').forEach(btn => {
                btn.addEventListener('click', () => closeAlert(btn.closest('.flash-item')));
            });
            setTimeout(() => {
                container.querySelectorAll('.flash-item').forEach(closeAlert);
            }, 4000);
        })();
    </script>
<?php endif; ?>
