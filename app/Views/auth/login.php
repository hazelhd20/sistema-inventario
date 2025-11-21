<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion - <?= e(config('app.name')) ?></title>
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
<body class="min-h-screen font-sans bg-gradient-to-br from-blue-pastel via-peach-pastel to-pink-pastel flex items-center justify-center relative overflow-hidden">
<div class="absolute inset-0 pointer-events-none">
    <div class="absolute -top-24 -left-24 h-64 w-64 rounded-full bg-white/40 blur-3xl"></div>
    <div class="absolute bottom-0 right-10 h-72 w-72 rounded-full bg-white/30 blur-3xl"></div>
    <div class="absolute top-1/3 left-1/2 transform -translate-x-1/2 h-40 w-40 rounded-full bg-blue-pastel/40 blur-2xl"></div>
</div>

<div class="relative w-full max-w-4xl grid grid-cols-1 lg:grid-cols-2 gap-6 items-center px-4 sm:px-6 lg:px-8">
    <div class="hidden lg:block">
        <div class="bg-white/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 p-10">
            <div class="flex items-center space-x-3 mb-6">
                <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-12 w-12 object-contain rounded-full shadow-md">
                <div>
                    <p class="text-sm text-gray-600 tracking-wide">Bienvenido al</p>
                    <h1 class="text-2xl font-semibold text-gray-800"><?= e(config('app.name')) ?></h1>
                </div>
            </div>
            <div class="space-y-4 text-gray-700 text-sm leading-relaxed">
                <p>Controla tu inventario con una interfaz ligera. Gestiona productos, movimientos y usuarios desde un solo lugar.</p>
                <ul class="space-y-2">
                    <li class="flex items-start space-x-2">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mt-0.5"></i>
                        <span>Alertas de stock en tiempo real.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mt-0.5"></i>
                        <span>Reportes claros y exportables.</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-600 mt-0.5"></i>
                        <span>Roles de usuario para mayor seguridad.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="relative">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/60 p-8 sm:p-10">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <img src="<?= asset_url('img/logo.png') ?>" alt="Logo" class="h-10 w-10 object-contain rounded-full shadow">
                    <div>
                        <p class="text-sm text-gray-500">Acceso</p>
                        <h2 class="text-xl font-semibold text-gray-900">Iniciar sesion</h2>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-pastel text-gray-800 border border-white">Dashboard</span>
            </div>

            <form class="space-y-5" action="<?= base_url('login') ?>" method="POST">
                <?php if (!empty($error)): ?>
                    <div class="flex items-start p-3 bg-pink-pastel/60 rounded-lg text-gray-800 border border-pink-200">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-pink-700 mr-2 mt-0.5"></i>
                        <p class="text-sm leading-relaxed"><?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-700">Correo electronico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input id="email" name="email" type="email" required value="<?= e(old('email')) ?>"
                               class="block w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-pastel focus:border-blue-pastel transition text-gray-900 placeholder-gray-400"
                               placeholder="correo@ejemplo.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-700">Contrasena</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="block w-full pl-11 pr-11 py-3 rounded-xl border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-blue-pastel focus:border-blue-pastel transition text-gray-900 placeholder-gray-400"
                               placeholder="********">
                        <button type="button" id="toggle-login-password" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-700 focus:outline-none">
                            <i data-lucide="eye" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2">
                    <div>
                        <p class="font-semibold text-gray-700">Demo Admin</p>
                        <p>admin@demo.com / Admin123@</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-700">Demo Empleado</p>
                        <p>empleado@demo.com / Empleado123@</p>
                    </div>
                </div>

                <button type="submit"
                        class="group w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-gray-900 bg-gradient-to-r from-blue-pastel to-green-pastel hover:from-blue-300 hover:to-green-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-pastel transition transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl">
                    <span class="flex items-center space-x-2">
                        <i data-lucide="log-in" class="h-5 w-5"></i>
                        <span>Entrar al sistema</span>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    if (window.lucide) { lucide.createIcons(); }
    (function() {
        const toggle = document.getElementById('toggle-login-password');
        const input = document.getElementById('password');
        if (!toggle || !input) return;
        toggle.addEventListener('click', () => {
            const isHidden = input.getAttribute('type') === 'password';
            input.setAttribute('type', isHidden ? 'text' : 'password');
            const icon = toggle.querySelector('i');
            if (icon) {
                icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
                if (window.lucide) { lucide.createIcons(); }
            }
        });
    })();
</script>
</body>
</html>
