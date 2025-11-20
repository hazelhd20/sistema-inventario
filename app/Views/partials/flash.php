<?php if (!empty($message) || !empty($error) || flash('error') || flash('success')): ?>
    <?php $successMessage = $message ?? flash('success'); ?>
    <?php $errorMessage = $error ?? flash('error'); ?>
    <div class="space-y-3 mb-4">
        <?php if ($successMessage): ?>
            <div class="p-4 rounded-md bg-green-pastel bg-opacity-40 text-gray-800 border border-green-200">
                <?= e($successMessage) ?>
            </div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="p-4 rounded-md bg-pink-pastel bg-opacity-40 text-gray-800 border border-pink-200">
                <?= e($errorMessage) ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
