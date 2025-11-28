<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - <?= e(config('app.name')) ?></title>
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
    <style>
        .password-strength { transition: all 0.3s ease; }
        .requirement-met { color: #10b981; }
        .requirement-unmet { color: #94a3b8; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 font-sans flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-pastel-mint/50 rounded-lg flex items-center justify-center">
                    <i data-lucide="shield-check" class="h-5 w-5 text-slate-600"></i>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Crear nueva contraseña</p>
                    <h1 class="text-lg font-semibold text-slate-800"><?= e(config('app.name')) ?></h1>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-slate-600">
                    Restableciendo contraseña para: <strong class="text-slate-800"><?= e($email) ?></strong>
                </p>
            </div>

            <!-- Form -->
            <form action="<?= base_url('reset-password') ?>" method="POST" class="space-y-5" id="resetForm">
                <input type="hidden" name="token" value="<?= e($token) ?>">

                <?php if (!empty($error)): ?>
                    <div class="flex items-start gap-3 p-4 bg-pastel-rose/30 border border-pastel-rose rounded-lg">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-500 shrink-0 mt-0.5"></i>
                        <p class="text-sm text-slate-700"><?= e($error) ?></p>
                    </div>
                <?php endif; ?>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Nueva contraseña
                    </label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
                        <input id="password" name="password" type="password" required
                               class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue"
                               placeholder="••••••••"
                               autofocus>
                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password">
                            <i data-lucide="eye" class="h-5 w-5"></i>
                        </button>
                    </div>
                    
                    <!-- Password strength indicator -->
                    <div class="mt-3 space-y-2">
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div id="strengthBar" class="h-full password-strength bg-slate-300 rounded-full" style="width: 0%"></div>
                        </div>
                        <p id="strengthText" class="text-xs text-slate-400">Ingresa tu contraseña</p>
                    </div>
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Confirmar contraseña
                    </label>
                    <div class="relative">
                        <i data-lucide="lock-keyhole" class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"></i>
                        <input id="password_confirm" name="password_confirm" type="password" required
                               class="w-full pl-10 pr-10 py-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-pastel-blue focus:border-pastel-blue"
                               placeholder="••••••••">
                        <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password_confirm">
                            <i data-lucide="eye" class="h-5 w-5"></i>
                        </button>
                    </div>
                    <p id="matchStatus" class="mt-2 text-xs text-slate-400 hidden">
                        <i data-lucide="check" class="h-3 w-3 inline"></i> Las contraseñas coinciden
                    </p>
                </div>

                <!-- Password requirements -->
                <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                    <p class="text-xs font-medium text-slate-600 mb-2">La contraseña debe contener:</p>
                    <ul class="space-y-1 text-xs">
                        <li id="req-length" class="flex items-center gap-2 requirement-unmet">
                            <i data-lucide="circle" class="h-3 w-3"></i>
                            Mínimo 8 caracteres
                        </li>
                        <li id="req-upper" class="flex items-center gap-2 requirement-unmet">
                            <i data-lucide="circle" class="h-3 w-3"></i>
                            Una letra mayúscula
                        </li>
                        <li id="req-lower" class="flex items-center gap-2 requirement-unmet">
                            <i data-lucide="circle" class="h-3 w-3"></i>
                            Una letra minúscula
                        </li>
                        <li id="req-number" class="flex items-center gap-2 requirement-unmet">
                            <i data-lucide="circle" class="h-3 w-3"></i>
                            Un número
                        </li>
                        <li id="req-special" class="flex items-center gap-2 requirement-unmet">
                            <i data-lucide="circle" class="h-3 w-3"></i>
                            Un carácter especial (!@#$%...)
                        </li>
                    </ul>
                </div>

                <button type="submit" id="submitBtn"
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-pastel-mint hover:bg-pastel-mint/80 text-slate-700 font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    <i data-lucide="check-circle" class="h-5 w-5"></i>
                    Restablecer contraseña
                </button>
            </form>
        </div>

        <!-- Timer warning -->
        <div class="mt-4 p-4 bg-pastel-peach/30 rounded-xl border border-pastel-peach/50">
            <div class="flex items-center gap-3">
                <i data-lucide="clock" class="h-5 w-5 text-orange-500 shrink-0"></i>
                <p class="text-xs text-slate-600">
                    Este enlace expirará pronto. Completa el formulario para no perder tu acceso.
                </p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        if (window.lucide) lucide.createIcons();

        (function() {
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            const matchStatus = document.getElementById('matchStatus');
            const submitBtn = document.getElementById('submitBtn');

            const requirements = {
                length: { el: document.getElementById('req-length'), test: p => p.length >= 8 },
                upper: { el: document.getElementById('req-upper'), test: p => /[A-Z]/.test(p) },
                lower: { el: document.getElementById('req-lower'), test: p => /[a-z]/.test(p) },
                number: { el: document.getElementById('req-number'), test: p => /[0-9]/.test(p) },
                special: { el: document.getElementById('req-special'), test: p => /[^A-Za-z0-9]/.test(p) }
            };

            function updateRequirement(key, met) {
                const req = requirements[key];
                const icon = req.el.querySelector('i');
                
                if (met) {
                    req.el.classList.remove('requirement-unmet');
                    req.el.classList.add('requirement-met');
                    icon.setAttribute('data-lucide', 'check-circle');
                } else {
                    req.el.classList.remove('requirement-met');
                    req.el.classList.add('requirement-unmet');
                    icon.setAttribute('data-lucide', 'circle');
                }
                lucide.createIcons();
            }

            function checkPassword() {
                const p = password.value;
                let score = 0;
                let allMet = true;

                for (const [key, req] of Object.entries(requirements)) {
                    const met = req.test(p);
                    updateRequirement(key, met);
                    if (met) score++;
                    if (!met) allMet = false;
                }

                // Update strength bar
                const percent = (score / 5) * 100;
                strengthBar.style.width = percent + '%';

                if (score === 0) {
                    strengthBar.className = 'h-full password-strength bg-slate-300 rounded-full';
                    strengthText.textContent = 'Ingresa tu contraseña';
                    strengthText.className = 'text-xs text-slate-400';
                } else if (score <= 2) {
                    strengthBar.className = 'h-full password-strength bg-red-400 rounded-full';
                    strengthText.textContent = 'Contraseña débil';
                    strengthText.className = 'text-xs text-red-500';
                } else if (score <= 4) {
                    strengthBar.className = 'h-full password-strength bg-yellow-400 rounded-full';
                    strengthText.textContent = 'Contraseña moderada';
                    strengthText.className = 'text-xs text-yellow-600';
                } else {
                    strengthBar.className = 'h-full password-strength bg-green-500 rounded-full';
                    strengthText.textContent = 'Contraseña fuerte';
                    strengthText.className = 'text-xs text-green-600';
                }

                checkMatch();
                return allMet;
            }

            function checkMatch() {
                const p1 = password.value;
                const p2 = passwordConfirm.value;

                if (p2.length > 0) {
                    matchStatus.classList.remove('hidden');
                    if (p1 === p2) {
                        matchStatus.innerHTML = '<i data-lucide="check" class="h-3 w-3 inline text-green-500"></i> Las contraseñas coinciden';
                        matchStatus.className = 'mt-2 text-xs text-green-600';
                    } else {
                        matchStatus.innerHTML = '<i data-lucide="x" class="h-3 w-3 inline text-red-500"></i> Las contraseñas no coinciden';
                        matchStatus.className = 'mt-2 text-xs text-red-500';
                    }
                    lucide.createIcons();
                } else {
                    matchStatus.classList.add('hidden');
                }

                updateSubmitButton();
            }

            function updateSubmitButton() {
                const p = password.value;
                const p2 = passwordConfirm.value;
                let allMet = true;

                for (const req of Object.values(requirements)) {
                    if (!req.test(p)) allMet = false;
                }

                submitBtn.disabled = !(allMet && p === p2 && p2.length > 0);
            }

            password.addEventListener('input', checkPassword);
            passwordConfirm.addEventListener('input', checkMatch);

            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetId = btn.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = btn.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.setAttribute('data-lucide', 'eye-off');
                    } else {
                        input.type = 'password';
                        icon.setAttribute('data-lucide', 'eye');
                    }
                    lucide.createIcons();
                });
            });
        })();
    </script>
</body>
</html>

