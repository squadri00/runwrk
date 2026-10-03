<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['status']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $map = [
        'scheduled' => ['Scheduled', 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300'],
        'sending' => ['Sending', 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300'],
        'sent' => ['Sent', 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300'],
        'cancelled' => ['Cancelled', 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'],
    ];
    [$label, $class] = $map[$status] ?? [ucfirst($status), 'bg-slate-100 text-slate-600'];
?>
<span class="rounded-full px-2 py-0.5 text-xs font-medium <?php echo e($class); ?>"><?php echo e($label); ?></span>
<?php /**PATH D:\xampp\htdocs\runwrk\resources\views/components/message-status.blade.php ENDPATH**/ ?>