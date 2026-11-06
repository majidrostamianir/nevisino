@props(['path', 'bg' => 'bg-gray-50', 'iconClass' => 'text-gray-400'])

<div class="flex items-center gap-2 text-sm text-gray-600 {{ $bg }} px-3 py-1.5 rounded-lg mb-3">
    <svg class="w-3.5 h-3.5 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
    </svg>
    {{ $slot }}
</div>