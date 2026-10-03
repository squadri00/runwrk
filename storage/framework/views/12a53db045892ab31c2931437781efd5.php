<?php if (isset($component)) { $__componentOriginal96f5a324002a49acd05602ce99cfb071 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal96f5a324002a49acd05602ce99cfb071 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.superadmin','data' => ['title' => 'Message from '.$message->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.superadmin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Message from '.$message->name)]); ?>
    <div class="max-w-2xl space-y-4">
        <div class="rounded-xl bg-white p-5 ring-1 ring-slate-200 dark:bg-slate-900 dark:ring-slate-800">
            <dl class="grid grid-cols-3 gap-y-2 text-sm">
                <dt class="text-slate-500">Name</dt><dd class="col-span-2"><?php echo e($message->name); ?></dd>
                <dt class="text-slate-500">Email</dt><dd class="col-span-2"><a class="underline" href="mailto:<?php echo e($message->email); ?>"><?php echo e($message->email); ?></a></dd>
                <dt class="text-slate-500">Phone</dt><dd class="col-span-2"><?php echo e($message->phone ?: '—'); ?></dd>
                <dt class="text-slate-500">Interested in</dt><dd class="col-span-2"><?php echo e(\App\Models\ContactMessage::SERVICES[$message->service] ?? $message->service); ?></dd>
                <dt class="text-slate-500">Received</dt><dd class="col-span-2"><?php echo e($message->created_at->format('M j, Y g:i a')); ?></dd>
            </dl>
            <hr class="my-4 border-slate-200 dark:border-slate-800">
            <p class="whitespace-pre-line text-sm"><?php echo e($message->message); ?></p>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('admin.messages.index')); ?>" class="rounded-lg border border-slate-300 px-4 py-2 text-sm dark:border-slate-700">Back</a>
            <form method="POST" action="<?php echo e(route('admin.messages.destroy', $message)); ?>" onsubmit="return confirm('Delete this message?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="rounded-lg border border-rose-300 px-4 py-2 text-sm font-medium text-rose-600 dark:border-rose-800">Delete</button>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal96f5a324002a49acd05602ce99cfb071)): ?>
<?php $attributes = $__attributesOriginal96f5a324002a49acd05602ce99cfb071; ?>
<?php unset($__attributesOriginal96f5a324002a49acd05602ce99cfb071); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal96f5a324002a49acd05602ce99cfb071)): ?>
<?php $component = $__componentOriginal96f5a324002a49acd05602ce99cfb071; ?>
<?php unset($__componentOriginal96f5a324002a49acd05602ce99cfb071); ?>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/admin/messages/show.blade.php ENDPATH**/ ?>