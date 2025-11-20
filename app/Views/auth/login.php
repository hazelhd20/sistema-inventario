<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - <?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?= asset_url('styles.css') ?>">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-full bg-blue-pastel flex items-center justify-center">
                <span class="text-2xl font-bold text-gray-800">SI</span>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                <?= e(config('app.name')) ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ingresa tus credenciales para acceder
            </p>
        </div>
        <form class="mt-8 space-y-6" action="<?= base_url('login') ?>" method="POST">
            <?php if (!empty($error)): ?>
                <div class="flex items-center p-3 bg-pink-pastel bg-opacity-30 rounded-md text-gray-800">
                    <i data-lucide="alert-circle" class="h-5 w-5 text-pink-700 mr-2"></i>
                    <p class="text-sm"><?= e($error) ?></p>
                </div>
            <?php endif; ?>
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Correo electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" required value="<?= e(old('email')) ?>"
                               class="appearance-none rounded-none relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-pastel focus:border-blue-pastel focus:z-10 sm:text-sm"
                               placeholder="Correo electrónico">
                    </div>
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="appearance-none rounded-none relative block w-full px-3 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-pastel focus:border-blue-pastel focus:z-10 sm:text-sm"
                               placeholder="Contraseña">
                    </div>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-600 mb-2">
                    <strong>Demo:</strong> admin@demo.com / admin123 (Administrador)
                    <br>
                    empleado@demo.com / empleado123 (Empleado)
                </p>
                <button type="submit"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-gray-800 bg-blue-pastel hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-pastel transition-colors duration-200">
                    Iniciar sesión
                </button>
            </div>
        </form>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>if (window.lucide) { lucide.createIcons(); }</script>
</body>
</html>
