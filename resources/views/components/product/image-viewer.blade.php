@props(['src', 'alt'])

{{-- تصویر اصلی با زوم (منطق زوم در resources/js/product-page.js) --}}
<div class="overflow-hidden h-[60vh] relative flex justify-center items-center bg-gray-50 rounded-2xl"
     x-data="imageZoom"
     @mouseenter="onMouseEnter()"
     @mousemove="onMouseMove($event)"
     @mouseleave="onMouseLeave()"
     @click="onTap($event)"
     :class="{
        'cursor-zoom-in': zoomOnHover && !isZoomed,
        'cursor-pointer': !zoomOnHover
     }"
     style="touch-action: manipulation; -webkit-tap-highlight-color: transparent;">

    <img src="{{ $src }}"
         x-bind:src="$wire.src"
         x-on:load="isLoading = false"
         x-on:error="isLoading = false"
         wire:loading.remove
         wire:target="setImage"
         alt="{{ $alt }}"
         :class="{ 'scale-200': isZoomed, 'scale-100': !isZoomed }"
         :style="`transform-origin: ${originX} ${originY};`"
         style="width: 100%; height: 100%; object-fit: cover;"
         class="h-[60vh] object-contain transition-transform duration-200 mx-auto select-none rounded-2xl">

    <x-product.loading-overlay x-show="isLoading" />
    <x-product.loading-overlay wire:loading wire:target="setImage" />
</div>