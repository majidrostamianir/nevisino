<div
    x-data="{ show: @entangle('show'), message: @entangle('message'), type: @entangle('type') }"
    x-init="$watch('show', value => { if (value) setTimeout(() => show = false, 4000); })"
    x-show="show"
    x-transition
    x-cloak
    class="fixed bottom-5 right-5 left-5 z-50 w-fit">
    <!-- لایه بیرونی: حاشیه رنگی و سایه -->
    <div class="relative rounded shadow">
        <!-- لایه داخلی: پس‌زمینه سفید -->
        <div class="bg-white rounded py-3 pl-8 pr-4 relative">
            <!-- دکمه ضربدر سمت چپ -->
            <button @click="show = false"
                    class="absolute cursor-pointer top-3 left-2 text-gray-400 hover:text-gray-600">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                          clip-rule="evenodd"/>
                </svg>
            </button>

            <!-- محتوای نوتیف -->
            <div class="text-gray-800 text-sm flex ">
                <span class="block" x-html="message"></span>
                <!-- دکمه برو به سبد خرید -->
                <a href="/cart"
                   class="inline-block h-fit text-nowrap bg-pars-500 hover:bg-pars-600 text-white text-sm px-2 py-1 -mt-1 mx-2 rounded">
                    سبد خرید
                </a>
            </div>
        </div>
        <div
            x-bind:class="{
                'bg-green-400': type === 'success',
                'bg-red-400': type === 'error',
                'bg-yellow-400': type === 'warning',
                'bg-blue-400': type === 'info'
            }"
            class="absolute top-0 right-0 w-2 h-full rounded-r"></div>
    </div>
</div>
