@props([
    'name',
    'id' => null,
    'label' => null,
    'searchPlaceholder' => 'جستجو…',
])

@php
    $id = $id ?? $name;
@endphp

<div x-data="searchableSelect()" x-init="init()" @click.outside="close()" class="relative">
    @if ($label)
        <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-slate-700">{{ $label }}</label>
    @endif

    <select
        x-ref="select"
        id="{{ $id }}"
        name="{{ $name }}"
        class="sr-only"
        tabindex="-1"
        {{ $attributes->except(['class', 'id', 'name', 'label', 'searchPlaceholder']) }}
    >
        {{ $slot }}
    </select>

    <button
        type="button"
        class="select-trigger"
        :class="{ 'select-trigger-open': open }"
        @click="toggle()"
        :aria-expanded="open"
        aria-haspopup="listbox"
    >
        <span class="truncate" :class="selectedLabel ? 'text-slate-800' : 'text-slate-500'" x-text="selectedLabel || placeholder"></span>
        <svg class="select-trigger-icon" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 20 20" aria-hidden="true">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="select-dropdown"
        role="listbox"
    >
        <div class="border-b border-slate-100 p-2">
            <input
                x-ref="search"
                type="search"
                x-model="query"
                placeholder="{{ $searchPlaceholder }}"
                class="select-dropdown-search"
                @keydown.escape.prevent="close()"
                @click.stop
            >
        </div>
        <ul class="max-h-56 overflow-y-auto py-1">
            <template x-for="option in filteredOptions" :key="option.value">
                <li>
                    <button
                        type="button"
                        class="select-dropdown-option"
                        :class="{ 'select-dropdown-option-active': option.value === value }"
                        @click="select(option)"
                        x-text="option.label"
                    ></button>
                </li>
            </template>
            <li x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-slate-500">موردی یافت نشد</li>
        </ul>
    </div>
</div>
