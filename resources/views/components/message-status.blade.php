@props(['status'])

@php
    $map = [
        'scheduled' => ['Scheduled', 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300'],
        'sending' => ['Sending', 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300'],
        'sent' => ['Sent', 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-300'],
        'cancelled' => ['Cancelled', 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'],
    ];
    [$label, $class] = $map[$status] ?? [ucfirst($status), 'bg-slate-100 text-slate-600'];
@endphp
<span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $class }}">{{ $label }}</span>
