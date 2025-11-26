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
<body class="relative min-h-screen bg-gradient-to-br from-blue-pastel/20 via-white to-pink-pastel/20 font-sans text-gray-900">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -left-24 -top-28 h-72 w-72 rounded-full bg-blue-pastel/30 blur-3xl"></div>
        <div class="absolute bottom-[-6rem] right-[-4rem] h-80 w-80 rounded-full bg-peach-pastel/30 blur-3xl"></div>
    </div>
    <div class="relative z-10 flex min-h-screen">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <?php include __DIR__ . '/partials/header.php'; ?>
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-5 sm:p-8">
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
