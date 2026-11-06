@props(['title', 'path'])

<div class="w-full p-4 pb-8 sm:p-8 rounded-2xl mx-auto bg-white border border-gray-100 shadow-sm mt-4">
    <div class="font-bold mb-4 text-gray-800 flex items-center gap-2">
        <svg class="w-5 h-5 text-pars-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
        </svg>
        {{ $title }}
    </div>
    {{ $slot }}
</div>