<?php if (isset($component)) { $__componentOriginal03b6c44728e100ba2673d02906458342 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal03b6c44728e100ba2673d02906458342 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-layout','data' => ['title' => 'Verifikasi Email']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Verifikasi Email']); ?>
    <div class="text-center">
        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-brand-gradient-soft text-3xl">📧</div>
        <h1 class="mt-4 text-xl font-extrabold text-slate-800">Cek Email Kamu</h1>
        <p class="mt-2 text-sm text-slate-500">
            Kami sudah mengirim link verifikasi ke email kamu. Klik link itu
            untuk mengaktifkan akun sepenuhnya.
        </p>

        <?php if(session('warning')): ?>
            <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700"><?php echo e(session('warning')); ?></div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="mt-4 rounded-xl bg-brand-50 px-4 py-3 text-sm font-medium text-brand-700"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('verification.send')); ?>" class="mt-6">
            <?php echo csrf_field(); ?>
            <button type="submit" class="rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white hover:opacity-90">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <form method="POST" action="<?php echo e(route('customer.logout')); ?>" class="mt-3">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-sm font-medium text-slate-500 underline">Keluar</button>
        </form>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal03b6c44728e100ba2673d02906458342)): ?>
<?php $attributes = $__attributesOriginal03b6c44728e100ba2673d02906458342; ?>
<?php unset($__attributesOriginal03b6c44728e100ba2673d02906458342); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal03b6c44728e100ba2673d02906458342)): ?>
<?php $component = $__componentOriginal03b6c44728e100ba2673d02906458342; ?>
<?php unset($__componentOriginal03b6c44728e100ba2673d02906458342); ?>
<?php endif; ?>
<?php /**PATH C:\Users\lapto\Downloads\Ekspedisii_fixed\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>