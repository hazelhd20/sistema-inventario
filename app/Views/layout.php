<?php /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(config('app.name', 'Sistema de Inventarios')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pink-pastel': '#F7C6D0',
                        'blue-pastel': '#A8D8EA',
                        'green-pastel': '#B8E0D2',
                        'peach-pastel': '#FFD8B5',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'ui-sans-serif', 'system-ui'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="<?= asset_url('styles.css') ?>">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen bg-gray-50">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include __DIR__ . '/partials/header.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4">
                <?php include __DIR__ . '/partials/flash.php'; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
