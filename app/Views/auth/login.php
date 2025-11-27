<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - <?= e(config('app.name')) ?></title>
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
                    },
                    fontFamily: {
                        sans: ['DM Sans', 'system-ui', 'sans-serif'],
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-slate-50 font-sans flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-8">
                <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-10 w-10 object-contain rounded-lg">
                <div>
                    <p class="text-sm text-slate-500">Bienvenido a</p>
                    <h1 class="text-lg font-semibold text-slate-800"><?= e(config('app.name')) ?></h1>
                </div>
            </div>

            <!-- Form -->
            <form action="<?= base_url('login') ?>" method="POST" class="space-y-5">
                <?php if (!empty($error)): ?>
                    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-100 rounded-lg">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-500 shrink-0 mt-0.5"></i>
                        <p class="text-sm text-red-700"><?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
                        <input id="email" name="email" type="email" required value="<?= e(old('email')) ?>"
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                               placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
                        <input id="password" name="password" type="password" required
                               class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500"
                               placeholder="••••••••">
                        <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i data-lucide="eye" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Demo credentials -->
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-medium text-slate-500 mb-2">Credenciales de prueba:</p>
                    <div class="grid grid-cols-2 gap-3 text-xs text-slate-600">
                        <div>
                            <p class="font-medium text-slate-700">Admin</p>
                            <p>admin@demo.com</p>
                            <p>Admin123@</p>
                        </div>
                        <div>
                            <p class="font-medium text-slate-700">Empleado</p>
                            <p>empleado@demo.com</p>
                            <p>Empleado123@</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                    <i data-lucide="log-in" class="h-5 w-5"></i>
                    Iniciar sesión
                </button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        if (window.lucide) lucide.createIcons();
        (function() {
            const toggle = document.getElementById('toggle-password');
            const input = document.getElementById('password');
            if (!toggle || !input) return;
            toggle.addEventListener('click', () => {
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                const icon = toggle.querySelector('i');
                if (icon) {
                    icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
                    if (window.lucide) lucide.createIcons();
                }
            });
        })();
    </script>
</body>
</html>
