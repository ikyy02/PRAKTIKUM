@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'min' => null,
])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1.5">
        {{ $label }}
        @if ($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ $value }}"
           placeholder="{{ $placeholder }}" @if ($required) required @endif @if ($min !== null) min="{{ $min }}" @endif
           class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 outline-none">
</div>