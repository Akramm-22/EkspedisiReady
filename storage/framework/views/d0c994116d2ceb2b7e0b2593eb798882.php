<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Masuk'); ?> — drgEkspedisi</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/admin.js']); ?>
</head>
<body class="font-sans antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden flex-col justify-between overflow-hidden bg-brand-gradient p-12 text-white lg:flex">
            <!-- pola titik rute dekoratif -->
            <svg class="pointer-events-none absolute inset-0 h-full w-full opacity-20" viewBox="0 0 500 800" preserveAspectRatio="none">
                <path d="M-20,650 C120,700 150,500 280,480 S420,300 520,200"
                      fill="none" stroke="white" stroke-width="2" stroke-dasharray="6 10" />
                <circle cx="-20" cy="650" r="6" fill="white" />
                <circle cx="280" cy="480" r="6" fill="white" />
                <circle cx="520" cy="200" r="6" fill="white" />
            </svg>

            <div class="relative flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-white/20 font-bold">D</span>
                    <span class="text-lg font-extrabold">drgEkspedisi</span>
                </a>
                
                <?php if(auth('customer')->check()): ?>
                <form method="POST" action="<?php echo e(route('customer.logout')); ?>" class="m-0">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-sm font-semibold text-white/80 hover:text-white transition">
                        Keluar
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <div class="relative">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold">
                    📦 5 cabang · pengiriman terlacak
                </span>
                <h2 class="mt-4 text-3xl font-extrabold leading-tight">Kelola pengiriman<br>dari satu dashboard.</h2>
                <p class="mt-3 max-w-sm text-white/80">Pantau shipment, kurir, pembayaran, dan laporan operasional secara realtime.</p>
            </div>

            <p class="relative text-sm text-white/60">© <?php echo e(date('Y')); ?> drgEkspedisi</p>
        </div>

        <div class="flex flex-col items-center justify-center px-6 py-12">
            <?php if(auth('customer')->check()): ?>
                <div class="w-full max-w-sm flex justify-end mb-4 lg:hidden">
                    <form method="POST" action="<?php echo e(route('customer.logout')); ?>" class="m-0">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            <?php endif; ?>
            <div class="w-full max-w-sm">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH C:\Users\lapto\Downloads\Ekspedisii_fixed\resources\views/components/auth-layout.blade.php ENDPATH**/ ?>