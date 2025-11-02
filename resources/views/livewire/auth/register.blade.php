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
            <strong>کاربر گرامی؛</strong>
            <div class="w-full mt-2">لطفا شماره موبایل خود را وارد کنید:</div>

            <input
                inputmode="numeric"
                type="text"
                x-model="value"
                @keydown.enter.prevent="submit()"
                @input="convert($event.target.value)"
                dir="auto"
                autofocus
                placeholder="{{ english_to_persian_num('09123456789') }}"
                class="mt-8 w-full rounded-2xl placeholder:text-gray-300 placeholder:text-center text-center">
        </div>
    </div>

    <div class="flex flex-col items-center mt-8 mb-4">
        <a class="w-full text-center cursor-pointer rounded-2xl p-1.5 bg-pars-500 hover:bg-pars-600 transition-all text-white mb-2"
            @click.prevent="submit()">ورود</a>
{{--        <a class="text-xs cursor-pointer self-end mt-1" href="{{ route('forget') }}" wire:navigate.hover>--}}
{{--            فراموشی رمز عبور--}}
{{--        </a>--}}
    </div>
    @error('enMobile')
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
