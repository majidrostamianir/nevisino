<div>
    <div class="px-5 pt-10">
        {{-- progress bar --}}
        <div class="px-5 pb-4" x-data="{
                        sum: {{ $sum }},
                        freePack: {{ $free_packaging_threshold }},
                        freeShip: {{ $free_shipping_threshold }},
                        get pct() { return Math.min(this.sum / this.freeShip, 1); },
                        get packPct() { return this.freePack / this.freeShip; },
                        get reachedPack() { return this.sum >= this.freePack; },
                        get reachedShip() { return this.sum >= this.freeShip; },
                        get remainPack() { return this.freePack - this.sum; },
                        get remainShip() { return this.freeShip - this.sum; }
                    }">
            <div class="relative h-3 bg-gray-100 rounded-full border border-gray-200 overflow-visible mb-16">
                {{-- پر شدن نوار --}}
                <div class="h-full rounded-full transition-all duration-500"
                     :style="`width: ${Math.min(pct * 100, 100)}%`"
                     :class="reachedShip ? 'bg-green-600' : (reachedPack ? 'bg-green-400' : 'bg-orange-400')">
                </div>

                {{-- مبلغ سبد --}}
                <div class="absolute -top-6  text-[10px] text-gray-400 font-medium text-nowrap"
                     :style="`right: ${Math.min(pct * 100, 100)-10}%`">
                    {{ english_to_persian_num(number_format($sum)) }} تومان
                </div>

                {{-- نقطه بسته‌بندی رایگان --}}
                <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-md border-2 border-white shadow-md transition-all duration-300 z-10 flex items-center justify-center"
                     :style="`right: ${packPct * 100}%; transform: translate(50%, -15%);`"
                     :class="reachedPack ? 'bg-green-500' : 'bg-gray-300'">
                    <svg x-show="reachedPack" class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                {{-- لیبل بسته‌بندی رایگان --}}
                <div class="absolute text-center transition-all whitespace-nowrap text-xs"
                     :style="`right: ${packPct * 100}%; transform: translateX(50%); top: 20px;`"
                     :class="reachedPack ? 'text-green-700 font-bold' : 'text-green-300'">
                    بسته‌بندی رایگان
                    <div class="text-[9px] font-normal"
                         :class="reachedPack ? 'text-green-600' : 'text-gray-500'">
                        {{ english_to_persian_num(number_format($free_packaging_threshold)) }} تومان
                    </div>
                </div>

                {{-- نقطه ارسال رایگان (سمت چپ) --}}
                {{-- نقطه ارسال رایگان --}}
                <div class="absolute top-1/2 -translate-y-1/2 w-4 h-4 rounded-md border-2 border-white shadow-md transition-all duration-300 z-10 flex items-center justify-center"
                     style="right: 100%; transform: translate(50%, -15%);"
                     :class="reachedShip ? 'bg-green-500' : 'bg-gray-300'">
                    <svg x-show="reachedShip" class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                {{-- لیبل ارسال رایگان --}}
                <div class="absolute text-center transition-all whitespace-nowrap text-xs"
                     style="right: 100%; transform: translateX(50%); top: 20px;"
                     :class="reachedShip ? 'text-green-700 font-bold' : 'text-green-300'">
                    ارسال رایگان
                    <div class="text-[9px] font-normal"
                         :class="reachedShip ? 'text-green-600' : 'text-gray-500'">
                        {{ english_to_persian_num(number_format($free_shipping_threshold)) }} تومان
                    </div>
                </div>
            </div>

            {{-- پیام وضعیت --}}
            <div class="text-xs rounded-xl px-3 py-2 mt-2 leading-relaxed transition-all text-right"
                 :class="reachedShip ? 'bg-green-50 text-green-700 border border-green-200' : reachedPack ? 'bg-gray-50 text-gray-600 border border-gray-200' : 'bg-orange-50 text-orange-700 border border-orange-200'">

                <template x-if="reachedShip">
                    <div class="space-y-1">
                        <div>بسته‌بندی و ارسال رایگان است.</div>
                    </div>
                </template>

                <template x-if="!reachedShip && reachedPack">
                    <div class="space-y-1">
                        <div class="text-green-500">بسته‌بندی رایگان شد.</div>
                        <div class="text-xs text-orange-700">
                            <strong x-text="Number(remainShip).toLocaleString('fa-IR')"></strong> تومان تا
                            ارسال رایگان مانده.
                        </div>
                    </div>
                </template>

                <template x-if="!reachedShip && !reachedPack">
                    <div class="space-y-1">
                        <div>خرید <span x-text="Number(remainPack).toLocaleString('fa-IR')"></span> تومانِ
                            دیگر تا <strong>بسته‌بندی رایگان</strong></div>
                        <div>خرید <span x-text="Number(remainShip).toLocaleString('fa-IR')"></span> تومانِ
                            دیگر تا <strong>ارسال رایگان</strong></div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
