<?php /** @var string $content */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(config('app.name', 'Sistema de Inventarios')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                        },
                        accent: {
                            rose: '#fecdd3',
                            mint: '#bbf7d0',
                            peach: '#fed7aa',
                            sky: '#bae6fd',
                        },
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="<?= asset_url('styles.css') ?>">
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <div class="flex h-screen">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
        <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
            <?php include __DIR__ . '/partials/header.php'; ?>
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
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
