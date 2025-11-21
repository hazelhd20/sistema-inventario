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
    <div id="flash-container" class="fixed top-4 right-4 z-50 space-y-3 max-w-sm w-full">
        <?php foreach ($alerts as $index => $alert): ?>
            <?php
            $styles = match ($alert['type']) {
                'success' => 'bg-green-pastel/60 border-green-300 text-gray-800',
                'error' => 'bg-pink-pastel/70 border-pink-300 text-gray-800',
                'warning' => 'bg-peach-pastel/70 border-amber-200 text-gray-800',
                'info' => 'bg-blue-pastel/70 border-blue-300 text-gray-800',
                default => 'bg-gray-100 border-gray-200 text-gray-800',
            };
            ?>
            <div class="flash-item flex items-start justify-between p-4 rounded-md border shadow-md backdrop-blur-sm <?= $styles ?>" data-index="<?= $index ?>">
                <div class="pr-3 text-sm leading-relaxed">
                    <?= e($alert['text']) ?>
                </div>
                <button type="button" class="ml-2 text-gray-600 hover:text-gray-800 focus:outline-none flash-close" aria-label="Cerrar">
                    &times;
                </button>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        (function() {
            const container = document.getElementById('flash-container');
            if (!container) return;
            const closeAlert = (el) => {
                el.classList.add('opacity-0', 'translate-y-2', 'transition', 'duration-200');
                setTimeout(() => el.remove(), 200);
            };
            container.querySelectorAll('.flash-close').forEach(btn => {
                btn.addEventListener('click', () => {
                    closeAlert(btn.closest('.flash-item'));
                });
            });
            setTimeout(() => {
                container.querySelectorAll('.flash-item').forEach(closeAlert);
            }, 4000);
        })();
    </script>
<?php endif; ?>
