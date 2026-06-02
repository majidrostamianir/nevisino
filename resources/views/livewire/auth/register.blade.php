<div x-data="{
        value: '',
        enValue: '',
        convert(val) {
            const fa = {'0':'۰','1':'۱','2':'۲','3':'۳','4':'۴','5':'۵','6':'۶','7':'۷','8':'۸','9':'۹'};
            const en = {'۰':'0','۱':'1','۲':'2','۳':'3','۴':'4','۵':'5','۶':'6','۷':'7','۸':'8','۹':'9',
                        '٠':'0','١':'1','٢':'2','٣':'3','٤':'4','٥':'5','٦':'6','٧':'7','٨':'8','٩':'9'};
            const onlyDigits = val.replace(/[^0-9۰-۹٠-٩]/g, '');
            this.value = onlyDigits.replace(/[0-9]/g, d => fa[d]);
            this.enValue = onlyDigits.replace(/[۰-۹٠-٩]/g, d => en[d]);
        },
        submit() {
            $wire.set('enMobile', this.enValue);
            $wire.submit();
        }
}">
    <div class="flex justify-around items-center mt-20">
        <div class="w-full">
            <strong class="text-gray-800">کاربر گرامی؛</strong>
            <div class="w-full mt-2 text-gray-600">لطفا شماره موبایل خود را وارد کنید:</div>

            <input
                    inputmode="numeric"
                    type="text"
                    x-model="value"
                    @keydown.enter.prevent="submit()"
                    @input="convert($event.target.value)"
                    dir="auto"
                    autofocus
                    placeholder="{{ english_to_persian_num('09123456789') }}"
                    class="mt-8 w-full rounded-2xl placeholder:text-gray-400 placeholder:text-center text-center border-2 border-gray-200 focus:border-pars-500 focus:outline-none focus:ring-2 focus:ring-pars-200 bg-white p-3 transition-all duration-200">

            <div class="text-right mt-2 text-xs text-gray-400 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>مثال: {{ english_to_persian_num('09123456789') }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center mt-8 mb-4">
        <button @click.prevent="submit()"
                class="w-full text-center cursor-pointer rounded-2xl p-1.5 bg-pars-500 hover:bg-pars-600 transition-all text-white mb-2">
            ورود
        </button>
    </div>

    @error('enMobile')
    <span class="text-xs text-red-500 block text-right">{{ english_to_persian_num($message) }}</span>
    @enderror
</div>