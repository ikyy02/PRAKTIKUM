@props(['status'])

@php
    $styles = match ($status) {
        \App\Models\Loan::STATUS_PENDING => 'bg-amber-50 text-amber-700 border-amber-200',
        \App\Models\Loan::STATUS_APPROVED => 'bg-violet-50 text-violet-700 border-violet-200',
        \App\Models\Loan::STATUS_REJECTED => 'bg-red-50 text-red-700 border-red-200',
        \App\Models\Loan::STATUS_RETURNED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        default => 'bg-slate-50 text-slate-600 border-slate-200',
    };
    $label = \App\Models\Loan::STATUS_LABELS[$status] ?? $status;
@endphp

<span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $styles }}">
    {{ $label }}
</span>