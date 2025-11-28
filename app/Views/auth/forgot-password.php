<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - <?= e(config('app.name')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pastel: {
                            rose: '#F7C6D0',
                            blue: '#A8D8EA',
                            mint: '#B8E0D2',
                            peach: '#FFD8B5',
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
            <div class="flex items-center gap-3 mb-6">
                <a href="<?= base_url('login') ?>" class="p-2 -ml-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <div>
                    <p class="text-sm text-slate-500">Recuperar acceso</p>
                    <h1 class="text-lg font-semibold text-slate-800"><?= e(config('app.name')) ?></h1>
                </div>
            </div>

            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-pastel-blue/30 rounded-full flex items-center justify-center">
                    <i data-lucide="key-round" class="h-8 w-8 text-slate-600"></i>
                </div>
            </div>

            <div class="text-center mb-6">
                <h2 class="text-xl font-semibold text-slate-800 mb-2">¿Olvidaste tu contraseña?</h2>
                <p class="text-sm text-slate-500">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>
            </div>

            <!-- Form -->
            <form action="<?= base_url('forgot-password') ?>" method="POST" class="space-y-5">
                <?php if (!empty($error)): ?>
                    <div class="flex items-start gap-3 p-4 bg-pastel-rose/30 border border-pastel-rose rounded-lg">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-500 shrink-0 mt-0.5"></i>
                        <p class="text-sm text-slate-700"><?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="flex items-start gap-3 p-4 bg-pastel-mint/30 border border-pastel-mint rounded-lg">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-600 shrink-0 mt-0.5"></i>
                        <p class="text-sm text-slate-700"><?= e($success) ?></p>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
                        <input id="email" name="email" type="email" required value="<?= e(old('email')) ?>"
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue"
                               placeholder="correo@ejemplo.com"
                               autofocus>
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-pastel-blue hover:bg-pastel-blue/80 text-slate-700 font-medium rounded-lg transition-colors">
                    <i data-lucide="send" class="h-5 w-5"></i>
                    Enviar enlace de recuperación
                </button>

                <div class="text-center">
                    <a href="<?= base_url('login') ?>" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">
                        ← Volver al inicio de sesión
                    </a>
                </div>
            </form>

            <!-- Info -->
            <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-100">
                <div class="flex items-start gap-3">
                    <i data-lucide="info" class="h-5 w-5 text-slate-400 shrink-0 mt-0.5"></i>
                    <div class="text-xs text-slate-500 space-y-1">
                        <p>El enlace de recuperación expirará en <strong>10 minutos</strong>.</p>
                        <p>Si no recibes el correo, revisa tu carpeta de spam.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        if (window.lucide) lucide.createIcons();
    </script>
</body>
</html>

