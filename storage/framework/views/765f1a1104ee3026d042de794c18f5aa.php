<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'New message']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'New message']); ?>
    <div class="grid max-w-4xl gap-6 lg:grid-cols-5">
        <form method="POST" action="<?php echo e(route('notifications.store')); ?>" enctype="multipart/form-data" class="space-y-4 rounded-xl bg-white p-6 ring-1 ring-slate-200 lg:col-span-3 dark:bg-slate-900 dark:ring-slate-800"
              onsubmit="return document.getElementById('when').value !== 'now' || confirm('Send this to <?php echo e(number_format($subscribers)); ?> <?php echo e(Str::plural('customer', $subscribers)); ?> now?')">
            <?php echo csrf_field(); ?>
            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'title','label' => 'Title','maxlength' => '65','id' => 'f-title','oninput' => 'pv()','hint' => 'Short and clear. Up to 65 characters.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'title','label' => 'Title','maxlength' => '65','id' => 'f-title','oninput' => 'pv()','hint' => 'Short and clear. Up to 65 characters.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Message</label>
                <textarea name="body" id="f-body" rows="3" maxlength="200" oninput="pv()" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"><?php echo e(old('body')); ?></textarea>
                <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'url','label' => 'Link (optional)','type' => 'url','placeholder' => 'https://','hint' => 'Where the customer lands when they tap. Leave empty to open your website or app.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'url','label' => 'Link (optional)','type' => 'url','placeholder' => 'https://','hint' => 'Where the customer lands when they tap. Leave empty to open your website or app.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Picture (optional)</label>
                <input type="file" name="image" accept="image/png,image/jpeg,image/webp" class="mt-1 text-sm">
                <p class="mt-1 text-xs text-slate-400">Wide pictures work best (PNG, JPG or WebP, up to 1 MB). Shown on Android.</p>
                <?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Send</label>
                    <select name="when" id="when" onchange="document.getElementById('later').hidden = this.value !== 'later'" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="now" <?php if(old('when', 'now') === 'now'): echo 'selected'; endif; ?>>Now</option>
                        <option value="later" <?php if(old('when') === 'later'): echo 'selected'; endif; ?>>Later</option>
                    </select>
                </div>
                <div id="later" <?php if(old('when') !== 'later'): ?> hidden <?php endif; ?>>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Date and time</label>
                    <input type="datetime-local" name="send_at" value="<?php echo e(old('send_at')); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    <p class="mt-1 text-xs text-slate-400"><?php echo e($business->timezone); ?> time</p>
                    <?php $__errorArgs = ['send_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-rose-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900">Send message</button>
            <span class="ml-2 text-xs text-slate-400">to <?php echo e(number_format($subscribers)); ?> <?php echo e(Str::plural('customer', $subscribers)); ?></span>
        </form>

        <div class="lg:col-span-2">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Preview</div>
            <div class="mt-2 flex gap-3 rounded-2xl bg-white p-3 shadow-sm ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
                <img src="<?php echo e($business->icon_path ? Storage::disk('public')->url($business->icon_path.'/icon-192.png') : asset('assets/icons/default-192.png')); ?>" alt="" class="h-10 w-10 rounded-lg">
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold" id="pv-title">Your title</div>
                    <div class="text-sm text-slate-600 dark:text-slate-300" id="pv-body">Your message appears here.</div>
                    <div class="mt-1 text-xs text-slate-400"><?php echo e($business->short_name ?: $business->name); ?> · now</div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function pv() {
            document.getElementById('pv-title').textContent = document.getElementById('f-title').value || 'Your title';
            document.getElementById('pv-body').textContent = document.getElementById('f-body').value || 'Your message appears here.';
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/app/notifications/create.blade.php ENDPATH**/ ?>