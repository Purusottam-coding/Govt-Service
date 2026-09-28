@props(['type' => 'success', 'message' => null])

@php
    $typeClasses = match($type) {
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
        'error', 'danger' => 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
        'warning' => 'bg-slate-50 text-slate-800 border-slate-300 dark:bg-slate-950/50 dark:text-slate-300 dark:border-slate-800',
        'info' => 'bg-sky-50 text-sky-800 border-sky-300 dark:bg-sky-950/50 dark:text-sky-300 dark:border-sky-800',
        default => 'bg-slate-50 text-slate-800 border-slate-300 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800',
    };
@endphp

<div {{ $attributes->merge(['class' => 'p-4 rounded-xl border mb-4 text-sm flex items-start gap-3 shadow-sm ' . $typeClasses]) }} role="alert">
    <div class="flex-1">
        {{ $message ?? $slot }}
    </div>
</div>
