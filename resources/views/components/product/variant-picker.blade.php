@props(['product'])

{{-- از متغیرهای selected و select() ویوی والد استفاده می‌کند --}}
<div class="mb-4 p-1">
    <div class="flex flex-wrap items-center gap-3">
        <div class="flex flex-wrap gap-2">
            <span class="font-bold text-gray-700 text-sm pt-1.5 ml-2">انتخاب {{ $product->variant }}:</span>
            <div @click="select(1)"
                 class="relative px-4 py-1.5 rounded-xl border select-none transition-all duration-150 text-sm cursor-pointer"
                 :class="{
                    'border-pars-500 bg-pars-500 text-white font-bold shadow-md': selected < 1000,
                    'border-gray-300 hover:border-pars-400 bg-white text-gray-700': selected >= 1000
                 }">
                <span>به انتخاب نویسینو</span>
            </div>

            @foreach($product->variants as $variant)
                <div wire:key="variant-{{ $variant->id }}"
                     @click="select('{{ $variant->id }}')"
                     class="relative px-4 py-1.5 rounded-xl border select-none transition-all duration-150 text-sm"
                     :class="{
                        'border-pars-500 bg-pars-500 text-white font-bold shadow-md': selected == '{{ $variant->id }}',
                        'border-gray-300 hover:border-pars-400 bg-white text-gray-700 cursor-pointer': selected != '{{ $variant->id }}' && {{ $variant->stock }} > 0,
                        'opacity-50 cursor-not-allowed border-gray-200 bg-gray-100': {{ $variant->stock }} == 0
                     }">
                    <span>{{ english_to_persian_num($variant->name) }}</span>
                    @if($variant->stock == 0)
                        <span class="mr-1 text-xs text-red-500">(ناموجود)</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @error('selectedVariant')
    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
    @enderror
</div>