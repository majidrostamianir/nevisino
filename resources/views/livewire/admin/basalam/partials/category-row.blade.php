@php
    $hasBasalam = !empty($mappings[$category->id]);
    $mySearch   = $rowSearch[$category->id] ?? '';

    $filtered = $mySearch
        ? collect($basalamCategories)->filter(fn($title) => mb_stripos($title, $mySearch) !== false)->take(150)
        : collect($basalamCategories);
@endphp

<tr class="border-b border-gray-200 transition-colors hover:bg-gray-50 {{ $hasBasalam ? 'bg-emerald-50/40' : ($loop->even ?? false ? 'bg-gray-50/50' : 'bg-white') }}">
    <td class="px-4 py-3 text-sm">
        <div class="flex items-center gap-2">
            <span class="text-gray-400">{{ str_repeat('— ', $level) }}</span>
            @if ($level === 0)
                <span class="text-lg">📁</span>
            @else
                <span class="text-lg">📄</span>
            @endif
            <div class="flex flex-col">
                <span class="font-semibold text-gray-800">{{ $category->title }}</span>
                <span class="text-xs text-gray-400">id: {{ english_to_persian_num($category->id) }}</span>
            </div>
        </div>
    </td>

    <td class="px-4 py-3 text-sm">
        <input type="text"
               wire:model.live.debounce.300ms="rowSearch.{{ $category->id }}"
               placeholder="🔍 جستجو در دسته‌های باسلام..."
               class="w-full rounded-lg border-gray-300 shadow-sm text-xs py-1 px-2 mb-1">

        <select
                wire:model="mappings.{{ $category->id }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pars-500 focus:ring focus:ring-pars-500/30 text-sm py-2 px-3 {{ $hasBasalam ? 'border-emerald-400 bg-emerald-50' : '' }}"
        >
            <option value="">— انتخاب کنید —</option>
            @foreach ($filtered as $id => $title)
                <option value="{{ $id }}">{{ $title }}</option>
            @endforeach
        </select>
    </td>

    <td class="px-4 py-3 text-sm">
        <select wire:model="unitMappings.{{ $category->id }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pars-500 focus:ring focus:ring-pars-500/30 text-sm py-2 px-3">
            <option value="">— عددی —</option>
            @foreach ($unitTypes as $id => $title)
                <option value="{{ $id }}">{{ $title }}</option>
            @endforeach
        </select>
    </td>

    <td class="px-4 py-3 text-sm text-center">
        @if ($hasBasalam)
            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-emerald-100 text-emerald-700 mb-1">✓ متصل</span>
            <br>
            <button wire:click="clearMapping({{ $category->id }})"
                    class="cursor-pointer text-red-600 hover:text-red-800 text-xs hover:underline">
                ✕ پاک کردن
            </button>
        @else
            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-500">—</span>
        @endif
    </td>
</tr>

@foreach ($category->childrenRecursive as $child)
    @include('livewire.admin.basalam.partials.category-row', [
        'category' => $child,
        'level' => $level + 1,
        'rowSearch' => $rowSearch,
    ])
@endforeach