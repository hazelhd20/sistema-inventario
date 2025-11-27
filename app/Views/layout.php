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
                        // Paleta pastel del cliente
                        pastel: {
                            rose: '#F7C6D0',
                            blue: '#A8D8EA',
                            mint: '#B8E0D2',
                            peach: '#FFD8B5',
                        },
                        // Variantes para UI
                        primary: {
                            50: '#f0f9fc',
                            100: '#d4eef6',
                            200: '#A8D8EA',  // azul pastel base
                            300: '#7ec8e0',
                            400: '#54b8d6',
                            500: '#3a9fc0',
                            600: '#2d7a94',
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
