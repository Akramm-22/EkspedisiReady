<?php if (isset($component)) { $__componentOriginal03b6c44728e100ba2673d02906458342 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal03b6c44728e100ba2673d02906458342 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth-layout','data' => ['title' => 'Daftar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Daftar']); ?>
    <h1 class="text-2xl font-extrabold text-slate-800">Buat Akun Customer</h1>
    <p class="mt-1 text-sm text-slate-500">Daftar untuk mulai kirim & lacak paket.</p>

    <?php if($errors->any()): ?>
        <div class="mt-5 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc pl-4">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('customer.register')); ?>" class="mt-6 space-y-4">
        <?php echo csrf_field(); ?>

        <div>
            <label class="text-xs font-semibold text-slate-500">Nama Lengkap</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required autofocus
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Email</label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">No. HP</label>
            <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Kota</label>
            <input type="text" name="city" value="<?php echo e(old('city')); ?>" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Alamat</label>
            <textarea name="address" required rows="2"
                      class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100"><?php echo e(old('address')); ?></textarea>
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Kata Sandi</label>
            <input type="password" name="password" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>
        <div>
            <label class="text-xs font-semibold text-slate-500">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" required
                   class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-100" />
        </div>

        <div class="g-recaptcha" data-sitekey="<?php echo e(config('services.recaptcha.site_key')); ?>"></div>

        <button type="submit"
                class="w-full rounded-xl bg-brand-gradient px-6 py-3 text-sm font-semibold text-white transition hover:opacity-90">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah punya akun? <a href="<?php echo e(route('login')); ?>" class="font-semibold text-brand-600">Masuk di sini</a>
    </p>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH C:\Users\lapto\Downloads\Ekspedisii_fixed\resources\views/auth/register.blade.php ENDPATH**/ ?>