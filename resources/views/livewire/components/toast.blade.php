<div
        x-data="{ show: @entangle('show'), message: @entangle('message'), type: @entangle('type'), showCartButton: @entangle('showCartButton') }"
        x-init="$watch('show', value => { if (value) setTimeout(() => show = false, 4000); })"
        x-show="show"
        x-transition.duration.300ms
        x-cloak
        class="fixed bottom-5 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4">

    <div class="relative rounded-2xl shadow-lg overflow-hidden">
        <!-- نوار کناری رنگی -->
        <div
                x-bind:class="{
                'bg-green-500': type === 'success',
                'bg-red-500': type === 'error',
                'bg-amber-500': type === 'warning',
                'bg-blue-500': type === 'info'
            }"
                class="absolute top-0 right-0 w-1.5 h-full"></div>

        <!-- محتوای اصلی -->
        <div class="bg-white rounded-2xl py-3 px-4 md:pr-5 md:pl-12 relative">
            <!-- دکمه بستن -->
            <button @click="show = false"
                    class="absolute top-3 left-3 md:top-1/2 md:-translate-y-1/2 md:left-3 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- محتوا -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <!-- آیکون و متن با هم -->
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <div
                            x-bind:class="{
                            'text-green-500': type === 'success',
                            'text-red-500': type === 'error',
                            'text-amber-500': type === 'warning',
                            'text-blue-500': type === 'info'
                        }"
                            class="flex-shrink-0 mt-0.5">
                        <template x-if="type === 'success'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        <template x-if="type === 'error'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        <template x-if="type === 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </template>
                        <template x-if="type === 'info'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                    </div>

                    <div class="text-sm text-gray-700 leading-relaxed break-words flex-1" x-html="message"></div>
                </div>

                <!-- دکمه سبد خرید - فقط در صورت true بودن showCartButton نمایش داده می‌شود -->
                <a href="/cart"
                   x-show="showCartButton"
                   x-transition
                   class="flex-shrink-0 bg-pars-500 hover:bg-pars-600 active:scale-95 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all duration-200 text-center">
                    سبد خرید
                </a>
            </div>
        </div>
    </div>
</div>