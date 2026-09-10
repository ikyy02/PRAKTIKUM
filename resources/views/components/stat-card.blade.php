@props(['label', 'value', 'icon' => 'box', 'color' => 'emerald'])

@php
    $colors = [
        'emerald' => ['bg-emerald-50', 'text-emerald-600'],
        'blue' => ['bg-blue-50', 'text-blue-600'],
        'amber' => ['bg-amber-50', 'text-amber-600'],
        'violet' => ['bg-violet-50', 'text-violet-600'],
        'teal' => ['bg-teal-50', 'text-teal-600'],
        'red' => ['bg-red-50', 'text-red-600'],
    ];
    $palette = $colors[$color] ?? $colors['emerald'];

    $icons = [
        'box' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
        'archive' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
        'clock' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'arrow-out' => 'M15 11.25l-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'check' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
        'alert' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
    ];
    $path = $icons[$icon] ?? $icons['box'];
@endphp

<div class="bg-white rounded-xl border border-slate-200 shadow-sm px-5 py-4 flex items-center gap-4">
    <span class="inline-flex items-center justify-center size-11 rounded-lg {{ $palette[0] }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 {{ $palette[1] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
        </svg>
    </span>
    <div>
        <p class="text-xs text-slate-500">{{ $label }}</p>
        <p class="text-2xl font-bold text-slate-900 leading-tight">{{ $value }}</p>
    </div>
</div>