<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Connect website']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Connect website']); ?>
    <?php
        $card = 'rounded-xl bg-white p-6 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800';
        $code = 'block w-full overflow-x-auto rounded-lg bg-slate-100 px-3 py-2.5 font-mono text-xs text-slate-800 dark:bg-slate-800 dark:text-slate-200';
    ?>

    <div class="max-w-3xl space-y-6">
        <section class="<?php echo e($card); ?>">
            <h2 class="text-base font-semibold">1. Your app page (works with no website)</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Share this link or print the code. Customers open it on their phone, add the app and turn on messages.</p>
            <div class="mt-4 flex flex-wrap items-center gap-6">
                <div class="rounded-lg bg-white p-2"><?php echo $qr; ?></div>
                <div class="min-w-0 flex-1">
                    <code class="<?php echo e($code); ?>"><?php echo e($appUrl); ?></code>
                    <button type="button" data-copy="<?php echo e($appUrl); ?>" class="mt-2 rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium dark:border-slate-700">Copy link</button>
                </div>
            </div>
        </section>

        <section class="<?php echo e($card); ?>">
            <h2 class="text-base font-semibold">2. Add the app to your own website</h2>

            <?php if(! $site): ?>
                <div class="mt-3 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-900 ring-1 ring-amber-200 dark:bg-amber-500/15 dark:text-amber-200 dark:ring-amber-500/30">
                    We have not linked your website yet. Tell us your website address and we will set it up, then these steps will appear here.
                </div>
            <?php else: ?>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Visitors to your website will see a small <b>Get our app</b> button. It takes about 10 minutes.</p>

                <?php if(count($sites) > 1): ?>
                    <form method="GET" class="mt-4 flex items-center gap-2 text-sm">
                        <label class="font-medium">Website</label>
                        <select name="site" onchange="this.form.submit()" class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 dark:border-slate-700 dark:bg-slate-900">
                            <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php if($s === $site): echo 'selected'; endif; ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                <?php else: ?>
                    <p class="mt-3 text-sm">Your website: <b><?php echo e($site); ?></b></p>
                <?php endif; ?>

                <ol class="mt-5 space-y-5 text-sm">
                    <li>
                        <b>Step 1. Download two small files.</b>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a href="<?php echo e(route('connect.worker')); ?>" class="rounded-lg bg-slate-900 px-3 py-1.5 font-medium text-white dark:bg-white dark:text-slate-900">runwrk-sw.js</a>
                            <a href="<?php echo e(route('connect.manifest', ['site' => $site])); ?>" class="rounded-lg bg-slate-900 px-3 py-1.5 font-medium text-white dark:bg-white dark:text-slate-900">runwrk-manifest.webmanifest</a>
                        </div>
                    </li>
                    <li>
                        <b>Step 2. Upload both files to the main folder of your website</b>, next to your home page (index.html). Keep the names exactly as they are. After uploading, these should open in a browser:
                        <code class="<?php echo e($code); ?> mt-2"><?php echo e($site); ?>/runwrk-sw.js<br><?php echo e($site); ?>/runwrk-manifest.webmanifest</code>
                    </li>
                    <li>
                        <b>Step 3. Paste this line into your pages</b>, just before the closing <code>&lt;/body&gt;</code> tag (or in your footer so it appears on every page).
                        <code class="<?php echo e($code); ?> mt-2 whitespace-pre-wrap break-all"><?php echo e($snippet); ?></code>
                        <button type="button" data-copy="<?php echo e($snippet); ?>" class="mt-2 rounded-lg border border-slate-300 px-3 py-1.5 font-medium dark:border-slate-700">Copy line</button>
                    </li>
                    <li><b>Step 4. Open your website.</b> The <b>Get our app</b> button appears in the corner. Tap it to try it. Send yourself a message from Notifications.</li>
                </ol>

                <details class="mt-6 text-sm">
                    <summary class="cursor-pointer font-semibold">Using WordPress?</summary>
                    <p class="mt-2 text-slate-600 dark:text-slate-300">Upload the two files with your hosting File Manager or FTP into the folder that contains <code>wp-config.php</code>. Then add the line from Step 3 with a plugin such as "Insert Headers and Footers" (footer section).</p>
                </details>

                <details class="mt-3 text-sm">
                    <summary class="cursor-pointer font-semibold">Using a Grav website?</summary>
                    <p class="mt-2 text-slate-600 dark:text-slate-300">Easier: download our plugin. Your key is already inside it.</p>
                    <ol class="mt-2 list-decimal space-y-1 pl-5 text-slate-600 dark:text-slate-300">
                        <li>Download <a class="font-medium underline" href="<?php echo e(route('connect.grav')); ?>">runwrk-connect.zip</a>.</li>
                        <li>Unzip it and upload the <code>runwrk-connect</code> folder to <code>user/plugins/</code> on your website.</li>
                        <li>Clear the Grav cache and open your website. The button appears.</li>
                    </ol>
                </details>

                <details class="mt-3 text-sm">
                    <summary class="cursor-pointer font-semibold">Using Wix, Squarespace or Shopify?</summary>
                    <p class="mt-2 text-slate-600 dark:text-slate-300">These services do not let us add the two files, so the button cannot run on them. Use your app page link from section 1 instead: put it on your website as a button, and share the QR code in your shop.</p>
                </details>
            <?php endif; ?>
        </section>

        <section class="<?php echo e($card); ?>">
            <h2 class="text-base font-semibold">Website addresses we have linked</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">The app only works on these addresses. Moving to a new website address? Tell us, so we can add it.</p>
            <ul class="mt-3 space-y-1 text-sm">
                <?php $__empty_1 = true; $__currentLoopData = $business->domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><li class="font-mono"><?php echo e($d->domain); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><li class="text-slate-400">None yet.</li><?php endif; ?>
            </ul>
        </section>
    </div>

    <script>
        document.querySelectorAll('[data-copy]').forEach(function (b) {
            b.addEventListener('click', function () {
                var t = b.getAttribute('data-copy'), old = b.textContent;
                (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).then(function () { b.textContent = 'Copied'; }, function () { window.prompt('Copy this:', t); });
                setTimeout(function () { b.textContent = old; }, 1500);
            });
        });
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
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/app/connect.blade.php ENDPATH**/ ?>